<?php

 namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\CaracteristiquesEntity;
use SQLEntities\CategorieEntity;
use SQLEntities\ClientEntity;
use SQLEntities\PanneCaracteristiqueEntity;
use SQLEntities\PanneEvent;
use SQLEntities\PanneEventEntity;
use SQLEntities\PannesEntity;
use SQLEntities\Users;
use SQLEntities\UsersEntity;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\Autoloader;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
 use vendor\easyFrameWork\Core\Master\GhostLog;
use vendor\easyFrameWork\Core\Master\SessionManager;
 use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\EnvParser;
use vendor\easyFrameWork\Core\Master\HistoryLog;
use Vendor\EasyFrameWork\Core\Master\MiddleAgent;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Master\TokenManager;

abstract class MiscFunction{
    public static function getCategorie($id=null){

        $sqlF=self::getSQLFactory();
        $session_manager=new SessionManager;
        $curentUser=Main::fixObject($session_manager->get("user"),"SQLEntities\UsersEntity");
        $client=ClientEntity::getClientBy($sqlF,"client_id",$curentUser->client);
        $arr=$client->getCategories($sqlF);
        return array_reduce($arr,function($car,$el){
            $car[]=$el->getArray();
            return $car;
        },[]);
    }
    public static function getPanneHistory($id){
        $sqlF=self::getSQLFactory();
        $user=UsersEntity::getUsersBy($sqlF,"uuidUser",$id);
        if($user==false){
             echo json_encode(["result"=>"error","message"=>"no User Finded"]);
            exit();
        }
        return $user->getPanneHistory($sqlF);
    }
    public static function updateUser($uuid,$nom,$prenom,$mail,$manager="0"){
         MiddleAgent::INIT();
        $userData = MiddleAgent::checkTokenAndRole("admin");
        $sqlF=self::getSQLFactory();
        $user=UsersEntity::getUsersBy($sqlF,"uuidUser",$uuid);
        if($user==false){
             echo json_encode(["result"=>"error","message"=>"no User Finded"]);
            exit();
        }
        $user->nomUser=$nom;
        $user->prenomUser=$prenom;
        $user->mailUser=$mail;
        $user->manager_id=$manager;
        MiddleAgent::refreshToken();
        return UsersEntity::update($sqlF,$user);
    }
   /* public static function getAllLog(){
        $log=new HistoryLog("../../../include/connexion.log");
        $log->
    }*/
    public static function ResetPwd($user){
        MiddleAgent::INIT();
        $userData = MiddleAgent::checkTokenAndRole("admin");
        
        $sqlf=self::getSQLFactory();
        $user=UsersEntity::getUsersBy($sqlf,"uuidUser",$user);
        if($user==false){
            echo json_encode(["result"=>"error","message"=>"no User Finded"]);
            exit();
        }
        $user->password_hash="";
        MiddleAgent::refreshToken();
        return UsersEntity::update($sqlf,$user);
    }
    public static function getAllCaracteristics(){
        $session_manager=new SessionManager;
        $curentUser=Main::fixObject($session_manager->get("user"),"SQLEntities\UsersEntity");
        $client=ClientEntity::getClientBy(self::getSQLFactory(),"client_id",$curentUser->client);
        $return=$client->getCaracterisiques(self::getSQLFactory());
        return array_reduce($return,function($c,$e){
            $c[]=$e->getArray();
            return $c;
        },[]);
    }
    private static function getSQLFactory(){
        return new SQLFactory(null,"../include/config.ini");
    } 
    public static function generatePassWord($mailUser,$mdp){
        $sqlf=self::getSQLFactory();
        $user=UsersEntity::getUsersBy($sqlf,"mailUser",$mailUser);
        if($user==false){
            echo json_encode(["result"=>"error","message"=>"no User Finded"]);
            exit();
        }
        $crypto=new Cryptographer();
        $crypt=$crypto->hashString($mdp);
        $user->password_hash=$crypt;
        return UsersEntity::update($sqlf,$user);
    }
    public static function createTrigger($ref,$name,$content){
        $panneEvent=new PanneEventEntity;
        $panneEvent->refEvent=$ref;
        $panneEvent->event_name=$name;
        $panneEvent->event_callBack=$content;
        if(PanneEventEntity::add(self::getSQLFactory(),$panneEvent)){
            return true;
        }
    }
    public static function addUser($nom,$prenom,$mail,$role){
        try{
         $userData = MiddleAgent::checkTokenAndRole(["admin","manager"]);
        $user_required=UsersEntity::getUsersBy(self::getSQLFactory(),"uuidUser",$userData["user"]);
        $user=new UsersEntity;
        $user->nomUser=$nom;
        $user->prenomUser=$prenom;
        $user->mailUser=$mail;
        $user->roleUser=$role;
        $user->created_at=date("Y-m-d H:i:s");
        $user->uuidUser=uniqid();
        $user->client=$user_required->client;
        if(UsersEntity::add(self::getSQLFactory(),$user)){
            return $user->getArray();
        }else{
            return false;
        }
    }catch(Exception $e){
 echo json_encode([
        "status" => "error",
        "file"=>$e->getFile(),
        "message" => $e->getMessage(),
        "code" => $e->getCode()
    ]);
    exit();
    }
    }
    public static function getEvents($id){
        $sqlf=self::getSQLFactory();
        $panne=PannesEntity::getPannesBy($sqlf,"id",$id);
        if($panne==false){
            echo json_encode([
        "status" => "error",
        "message" => "No panne with current ID $id"
    ]);
    exit();
        }
        $events=$panne->getEvents($sqlf);
        if($events==false){
            return false;
        }
        return $events;
    }
    public static function connexion($mail,$mdp){
        try{
         $session_manager=new SessionManager;
        $return=UsersEntity::connexion(self::getSQLFactory(),
        $mail,$mdp,function($user) use($session_manager){
            $session_manager->set("user",$user);
        });
       
        if($return!=false){
            $log=new HistoryLog("../include/connexion.log");
            $log->addEntry($return["user_id"]."- connexion");
            $log->commit();
            $session_manager->set("isConnect","1");
            return $return;
        }
        return ["status"=>"error","message"=>"not a valid mail or pwd"];
    }catch(Exception $exception){
          echo json_encode(["status"=>"error","message"=>$exception->getMessage()]);
        exit();
    }
    }
    public static function foundPanne($userID,$panneID,$comment){
        $sqlf = self::getSQLFactory();
        $user=UsersEntity::getUsersBy($sqlf,"uuidUser",$userID);
        if($user==false){
               echo json_encode([
        "status" => "error",
        "message" => "No user with current ID $userID"
    ]);
    exit();
        }
        $panne=PannesEntity::getPannesBy($sqlf,"id",$panneID);
        if($panne==false){
               echo json_encode([
        "status" => "error",
        "message" => "No panne with current ID $panneID"
    ]);
    exit();
        }
       return $user->FoundPanne($sqlf,$panne,$comment);
    }
    public static function updatePannes($id,$diag,$cars){
        $sqlF=self::getSQLFactory();
        $panne=PannesEntity::getPannesBy($sqlF,"id",$id);
        if($panne==false){
             echo json_encode([
        "status" => "error",
        "message" => "No panne with current ID"
    ]);
    exit();
        }
        $panne->diagnostique=$diag;
        $update=PannesEntity::update($sqlF,$panne);
        if($update==false){
         echo json_encode([
        "status" => "error",
        "message" => "Error occure while updating panne"
    ]);
    exit();
        }
        //Supprimer caracteristique associées
        $oldCars=PanneCaracteristiqueEntity::getPanneCaracteristiqueBy($sqlF,"panne_id",$id);
      
        if($oldCars!=false){
        foreach($oldCars as $c){
            PanneCaracteristiqueEntity::del($sqlF,$c);
        }
    }
        for($i=0;$i<count($cars);$i++){
            $newCar=new PanneCaracteristiqueEntity;
            $newCar->panne_id=$id;
            $newCar->caracteristique_id=$cars[$i];
            PanneCaracteristiqueEntity::add($sqlF,$newCar);
        }
        return true;
    }
   public static function getUsers($user = null)
{
    try {
        $userData = MiddleAgent::checkTokenAndRole(["admin", "manager"]);
        $sqlF = self::getSQLFactory();

        $currentUser = UsersEntity::getUsersBy($sqlF, "uuidUser", $userData["user"]);
        if (!$currentUser) {
            return ["status" => "error", "message" => "User not found"];
        }

        // Récupération de la liste filtrée
        if ($user) {
            $users = UsersEntity::getUsersBy($sqlF, "uuidUser", $user, function ($u) use ($currentUser) {
                return $u->client === $currentUser->client;
            });
        } else {
            $users = UsersEntity::getAll($sqlF);
        }

        if (!$users) {
            return ["status" => "error", "message" => "No users found"];
        }

        $users = is_array($users) ? $users : [$users];
        $output = [];

        foreach ($users as $u) {
            // Règle pour les managers : voir uniquement leurs subordonnés
            if ($userData["data"]["role"] === "manager") {
                if ($u->client === $currentUser->client && $u->manager_id === $currentUser->idusers) {
                    $output[] = $u->getArray();
                }
            }

            // Règle pour les admins : voir tout leur client
            elseif ($userData["data"]["role"] === "admin") {
                if ($u->client === $currentUser->client) {
                    $arr = $u->getArray();
                    if ($user) {
                        $arr["manager"] = $u->getManager($sqlF)?->getArray();
                    }
                    $output[] = $arr;
                }
            }
        }

        return $output;
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "file" => $e->getFile(),
            "message" => $e->getMessage(),
            "code" => $e->getCode()
        ]);
        exit();
    }
}

    public static function getPannes($ids=0){
        $sqlF = self::getSQLFactory();
          $userData=MiddleAgent::checkTokenAndRole(["admin", "manager","agent","dev"]);
           $currentUser = UsersEntity::getUsersBy($sqlF, "uuidUser", $userData["user"]);
           $client_id=$currentUser->client;
     $result=  self::getSQLFactory()->execQuery("SELECT p.id as idPanne, p.code, p.diagnostique, c.label, c.id
        FROM pannes p
        JOIN panne_caracteristique pc ON p.id = pc.panne_id
        JOIN caracteristiques c ON pc.caracteristique_id = c.id
        WHERE p.client_id=$client_id
        ORDER BY p.code");
        $pannes = [];
        foreach ($result as $row) {
    $code = $row['code'];
    if (!isset($pannes[$code])) {
        $pannes[$code] = [
            'idPanne'=>$row["idPanne"],
            'code'=>$row["code"],
            'car' => [],
            'diagnostique' => $row['diagnostique']
        ];
    }
    if($ids==1)
    $pannes[$code]['car'][] = ["label"=>$row['label'],"id"=>$row["id"]];
    else
    $pannes[$code]['car'][] = $row['label'];
}
//EasyFrameWork::Debug($pannes);
return $pannes;
    }
    
}