<?php
namespace vendor\easyFrameWork\Core\Master;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use RuntimeException;
use InvalidArgumentException;
abstract class SqlEntities
{
    public static $DIRECTORY = "./SQLEntities";
    /**
     * Remplace les %field% par le nom de la colonne
     * @param array $table
     * @param string $pattern
     */
    private static function replaceCallBack($table, $pattern)
    {
        return array_reduce($table, function ($carry, $field) use ($pattern) {
            $str = $pattern;
            $str = str_replace("%field%", $field["NAME"], $str);
            $carry[] = $str;
            return $carry;
        }, []);
    }
    /**
     * Retourne l'array de la SQLEntities
     * @param array $array
     * @param callable|null $callBack
     * @return array
     */
    public static function getArrayEntities(array $array,callable $callBack=null):array
    {
        if ($array[0]!=null) {
            return array_reduce($array, function ($carry, $el) use($callBack) {
                if($callBack!=null){
                    $item=$el;
                    $carry=call_user_func_array($callBack,array(&$carry,$item));
                }else
                    $carry[] = $el->getArray();
                return $carry;
            }, []);
        }else{
            return [];
        }
    }
    private static function getIdField($columns){
      //  var_dump($columns);
        $a=array_filter($columns,function($el){
            return $el["PRIMARY"]==="PRI";
        });
       // var_dump(current($a));
        return current($a)["NAME"];
    }
    /**
     * Génére la class SQLEntities
     * @param SQLFactory $sqlF
     * @param string $table
     */
    public static function generateEntity(SQLFactory $sqlF,string $table):void
    {
        $content = file_get_contents(self::$DIRECTORY . "/EntityModel");
        $className = EasyFrameWork::toCamelCase($table);
    
        $columns = $sqlF->getColumns($table);
        $pattern = "\"%field%\"=>''";
        $attrs = self::replaceCallBack($columns, $pattern);

        $pattern = "\$entity->%field%=\$element[\"%field%\"];";
        $affect = self::replaceCallBack($columns, $pattern);

        $content = str_replace("[%className%]", $className, $content);
        $content = str_replace("[%table%]", $table, $content);
        $content = str_replace("[%attr%]", implode(",", $attrs), $content);
        $content = str_replace("[%affect%]", implode("\n", $affect), $content);
        $content=str_replace("%idField%",self::getIdField($columns),$content);
        if (file_put_contents(self::$DIRECTORY . "/$className.php", $content)) {
            echo ">Class $table [$className] - genérée";
        }
    }
    public static function TblClassToEntity(SQLFactory $sqlF, string $className): void
    {
        // Vérifier que la classe existe
     /*   if (!class_exists($className)) {
            throw new InvalidArgumentException("Class $className does not exist.");
        }
    
        // Obtenir le nom de la table à partir de la classe
        if (!is_subclass_of($className, SqlEntities::class)) {
            throw new InvalidArgumentException("$className must extend SQLEntity.");
        }*/
    
       /* $table = $className::getTableName();
    
        // Générer la classe de base
        self::generateEntity($sqlF, $table);*/
    
        // Nom de la classe générée et de la classe personnalisée
        $baseClass = str_replace("Tbl","",EasyFrameWork::toCamelCase($className)); // Nom de la classe générée

        $customClass = "{$baseClass}Entity"; // Nom de la classe personnalisée
        $filePath = self::$DIRECTORY . "/$customClass.php";
    
        // Vérifier si la classe personnalisée existe déjà
        if (file_exists($filePath)) {
            echo "> Custom class $customClass already exists. Skipping generation.\n";
            return;
        }
    
        // Contenu de la classe personnalisée
        $content = file_get_contents("../SQLEntities/SQLEntityModel");
        $content=str_replace("[%customClass%]",$customClass,$content);
        $content=str_replace("[%baseClass%]",$className,$content);
        $content=str_replace("[%className%]",$baseClass,$content);
        // Sauvegarder la classe personnalisée
        if (file_put_contents($filePath, $content)) {
            echo "> Custom class $customClass for table [$className] created successfully.\n";
        } else {
            throw new RuntimeException("Failed to generate custom class $customClass.");
        }
    }
    
    /**
     * Charge la classe SQLEntitie de la table passée en paramètre
     * @param string $table
     */
    public static function LoadEntity(string $table):void
    {
        $filename = easyFrameWork::toCamelCase($table);
        require_once self::$DIRECTORY . "/$filename.php";
    }
}
