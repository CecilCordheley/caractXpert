<?php
namespace vendor\easyFrameWork\Core\Master;

use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use PDO;
use Exception;
use PDOException;
class SQLFactory
{
    private $query = "";
    private $PDO;
    private $tables;
    private $routine_fnc;
    private $ini;
    /**
     * Instancie un nouvel SqlFactory
     * @param PDO|null $PDO
     * @param string $configPath
     */
    public function __construct($PDO = null, $configPath = "include/config.ini")
    {
        $this->query = "";
        $this->ini = parse_ini_file($configPath, true)["BDD"];
        $this->PDO = $PDO ?? new PDO('mysql:host=' . $this->ini["host"] . ';dbname=' . $this->ini["bdd"], $this->ini["user"], $this->ini["mdp"],[
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
]);
        $this->tables = [];
        $t = $this->getTableSchema();
        $this->tables = array_reduce($t, function ($carry, $item) {
            // echo count($carry);
            $carry[$item["TABLE_NAME"]] = [];
            $carry[$item["TABLE_NAME"]]["PRI"] = $this->getID($item["TABLE_NAME"]);
            return $carry;
        }, []);
        $r = $this->getStorageFnc();
        //   var_dump($r[0]);
        $this->routine_fnc = array_reduce($r, function ($carry, $item) {
            $fncName = $item["ROUTINE_NAME"] ?? $item["routine_name"];
            $carry[$fncName]["type"] = $item["DATA_TYPE"] ?? $item["data_type"];

            $carry[$fncName]["exec"] = function ($args) use ($fncName) {
                $argsString = [];
                foreach ($args as $el) {
                    $argsString[] = $el["value"];
                }
                return $this->execQuery("SELECT `$fncName`(" . implode(",", $argsString) . ") AS `$fncName`;");
            };
            return $carry;
        }, []);
        //   var_dump($this->tables);
    }
    public function getPdo(){
        return $this->PDO;
    }
    public function prepareQuery($query, $key, $value="")
    {
        $this->query = $query;
        $stmt = $this->PDO->prepare($query);
     //   echo gettype($key);
        if(gettype($key)=="array"){
          
            foreach($key as $k=>$v){
                $stmt->bindParam(":$k", $v, PDO::PARAM_STR);
                $this->query = str_replace(":$k", "'" . $v . "'", $this->query);
            }
            
        }else{
             $stmt->bindParam(':val', $value, PDO::PARAM_STR);
             $this->query = str_replace(":val", "'" . $value . "'", $this->query);
        }
       // $stmt->debugDumpParams();
     //   echo $stmt->queryString;
    // echo $this->query;
        $stmt->execute();
        $return = $stmt->fetchAll();
        $stmt->closeCursor();
        return $return;
    }
    /**
     * Retourne l'identifiant de la table
     * @param string $table
     */
    private function getID($table)
    {
        return array_reduce($this->execQuery("SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '" . $this->ini["bdd"] . "'
          AND TABLE_NAME = '$table'
          AND COLUMN_KEY = 'PRI'"), function ($carry, $item) {
            $carry[] = $item["COLUMN_NAME"];
            return $carry;
        }, []);
    }
    /**
     * Retourne les champs de la table
     * @param string $table
     */
    public function getColumns($table)
    {
        $i = 0;
        $str = "SELECT *
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '" . $this->ini["bdd"] . "'
          AND TABLE_NAME = '$table'";
      //   EasyFrameWork::Debug($str);
        return array_reduce($this->execQuery("SELECT COLUMN_TYPE,COLUMN_NAME,EXTRA,COLUMN_KEY,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH,IS_NULLABLE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '" . $this->ini["bdd"] . "'
          AND TABLE_NAME = '$table'"), function ($carry, $item) use (&$i, $table) {
      //      EasyFrameWork::Debug($item,false);
            $carry[$i]["NAME"] = $item["COLUMN_NAME"];
            $carry[$i]["COL_TYPE"] = $item["COLUMN_TYPE"];
            $carry[$i]["PRIMARY"] = $item["COLUMN_KEY"];
            $carry[$i]["NULLABLE"]=$item["IS_NULLABLE"];
            if ($item["COLUMN_KEY"] == "MUL") {
                $str = "SELECT TABLE_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE COLUMN_NAME LIKE '%" . $carry[$i]["NAME"] . "%'
                AND TABLE_NAME <>'$table';";
                // EasyFrameWork::Debug($str);
                $t = $this->execQuery("SELECT TABLE_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE COLUMN_NAME LIKE '%" . $carry[$i]["NAME"] . "%'
                AND TABLE_NAME <>'$table';");
                if(isset($t[0]))
                    $carry[$i]["TABLE_ASSOC"] = $t[0]["TABLE_NAME"];
            }
            $carry[$i]["TYPE"] = $item["DATA_TYPE"];
            if ($item["COLUMN_KEY"] == "PRI")
                $carry[$i]["AUTO_INCR"] = ($item["EXTRA"] == "auto_increment") ? "YES" : "NO";
            $carry[$i]["LENGHT"] = ($item["DATA_TYPE"] == "varchar") ? $item["CHARACTER_MAXIMUM_LENGTH"] : "";
            $i++;
            return $carry;
        }, []);
    }
    /**
     * Retourne toutes les occurences de la table
     * @param string $table
     * @throws Exception la table n'existe pas
     */
    public function getTable($table)
    {

        if (key_exists($table, $this->tables)) {
            $sth = $this->PDO->query("SELECT * FROM $table");
            $arr = $sth->fetchAll(PDO::FETCH_ASSOC);
            $sth->closeCursor();
            return $arr;
        } else {
            throw new Exception("$table doesn't exist in the current schema");
        }
    }
    /**
     * Ajoute une occurence dans la table
     * @param array $item
     * @param string $table
     */
    public function addItem($item, $table,$debug=false)
    {

        try{
        $query = "INSERT INTO $table (#K#) VALUES (#VALUES#)";
        $k = [];
        $v = [];
        foreach ($item as $key => $values) {
            $k[] = $key;

            $v[] = $values != null ? "\"$values\"" : "null";
        }
        $query = str_replace("#K#", implode(",", $k), $query);
        $query = str_replace("#VALUES#", implode(",", $v), $query);
        //  echo $this->PDO->lastInsertId();
     //  echo $query;
        return $this->execQuery($query);
    }catch(PDOException $e){
        if ($e->getCode() == 23000) {
            // Code d'erreur SQL pour une violation de contrainte d'intégrité
            // ou de contrainte d'unicité
            if($debug)
                echo "<pre>Error violation constrainte from query $query</pre>";
            
                return false;
        } else {
            // Autres exceptions PDO
            if($debug)
                echo "<pre>Erreur: " . $e->getMessage()."Query : $query</pre>";
            else
                return false;
        }
    }

    }
    public function lastInsertId($table)
    {
         try{
        $idField = $this->getId($table)[0];
        $query = "SELECT $idField FROM $table ORDER BY $idField DESC LIMIT 1";
    //    echo $query;
        $return= $this->execQuery($query);
        return ($return[0][$idField])??false;
         }catch(PDOException $e){
        if ($e->getCode() == 23000) {
            // Code d'erreur SQL pour une violation de contrainte d'intégrité
            // ou de contrainte d'unicité
                echo "<pre>Error violation constrainte from query $query</pre>";
                return false;
        } else {
            // Autres exceptions PDO
                echo "<pre>Erreur: " . $e->getMessage()."Query : $query</pre>";
                return false;
        }
    }}
    /**
     * supprimer l'occurence de la table
     * @param string $id
     * @param string $table
     */
    public function deleteItem($id, $table)
    {
       // EasyFrameWork::Debug($this->tables);
       if(isset($this->tables[$table])){
        $f = $this->tables[$table]["PRI"][0];
       }elseif(isset($this->tables[strtolower($table)])){
        $f = $this->tables[strtolower($table)]["PRI"][0];
       }elseif(isset($this->tables[strtoupper($table)])){
        $f = $this->tables[strtoupper($table)]["PRI"][0];
       }else{
        throw new Exception("no $table find in current schema");
       }
        
   //     echo "<pre>DELETE FROM $table WHERE $f=$id</pre>";
        return $this->execQuery("DELETE FROM $table WHERE $f=$id");

    }
    /**
     * Met à jour l'item de la table
     * @param array $item
     * @param string $table
     */
    public function updateItem($item, $table)
    {
        $u = [];
        $f = $this->tables[$table]["PRI"][0];
        $fields=$this->getColumns($table);
        foreach ($item as $key => $value) {
           $colInfo=array_filter($fields,function($e)use($key){
                return $e["NAME"]==$key;
            });
            $null=array_shift($colInfo)["NULLABLE"];
           
            if ($key != $f){
                if($null=="YES" && $value=="")
                    $u[]="$key=NULL";
                else
                    $u[] = "$key=\"$value\"";
            }
        }

        $id = $item[$f];
   //    echo "UPDATE $table SET " . implode(",", $u) . " WHERE $f=$id";
        return $this->execQuery("UPDATE $table SET " . implode(",", $u) . " WHERE $f=$id");
        
    }
    /**
     * Exécute une requête
     * @param string $query
     */
    public function execQuery($query)
    {
        $sth = $this->PDO->query($query);
        $arr = $sth->fetchAll(PDO::FETCH_ASSOC);
        $sth->closeCursor();
        return $arr;
    }
    /**
     * Execute une fonction stockée
     * @param string $fncName nom de la fonction
     * @param array $args
     */
    public function execFnc($fncName, $args)
    {
        //  echo "SELECT `fncName`(".$args[0]["value"].") AS `$fncName`;";
        if(count($args))
         return $this->execQuery("SELECT `$fncName`(" . $args[0]["value"] . ") AS `$fncName`;");
        else
            return $this->execQuery("SELECT `$fncName`() AS `$fncName`;");
    }
    /**
     * Retourne toute les tables du schema courant
     */
    public function getTableSchema()
    {
        return $this->execQuery("SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='" . $this->ini["bdd"] . "' ");
    }
    /**
     * Retourne les tables avec les informations associées
     */
    public function getTableArray()
    {
        return $this->tables;
    }
    /**
     * Retourne les procédures stockées
     */
    public function getRoutineArray()
    {
        return $this->routine_fnc;
    }
    /**
     * Retourne les fonctions stockées
     */
    public function getStorageFnc()
    {
        return $this->execQuery("SELECT routine_schema as \"Database\", routine_name, data_type FROM information_schema.routines WHERE routine_type = 'FUNCTION' AND routine_schema = \"" . $this->ini["bdd"] . "\" ORDER BY routine_schema ASC, routine_name ASC");
    }
}