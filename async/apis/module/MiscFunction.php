<?php

 namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\CaracteristiquesEntity;
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
use Vendor\EasyFrameWork\Core\Master\MiddleAgent;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Master\TokenManager;
abstract class MiscFunction{
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
        $return=CaracteristiquesEntity::getAll(self::getSQLFactory());
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
        $user=new UsersEntity;
        $user->nomUser=$nom;
        $user->prenomUser=$prenom;
        $user->mailUser=$mail;
        $user->roleUser=$role;
        $user->created_at=date("Y-m-d H:i:s");
        $user->uuidUser=uniqid();
        if(UsersEntity::add(self::getSQLFactory(),$user)){
            return $user->getArray();
        }else{
            return false;
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
         $session_manager=new SessionManager;
        $return=UsersEntity::connexion(self::getSQLFactory(),
        $mail,$mdp,function($user) use($session_manager){
            $session_manager->set("user",$user);
        });
       
        if($return!=false){
            $session_manager->set("isConnect","1");
            return $return;
        }
        return ["status"=>"error","message"=>"not a valid mail or pwd"];
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
    public static function getUsers($user=null){
try {
         $userData = MiddleAgent::checkTokenAndRole(["admin","manager"]);
        $user_required=UsersEntity::getUsersBy(self::getSQLFactory(),"uuidUser",$userData["user"]);


if($user){
    $result=UsersEntity::getUsersBy(self::getSQLFactory(),"uuidUser",$user);
}else
    $result=UsersEntity::getAll(self::getSQLFactory());
        if($result==false){
            return ["status"=>"error","message"=>"no users found"];
        }
        $arr=is_array($result)?$result:[$result];
        $sqlF=self::getSQLFactory();
        $i=0;
        $role=$userData["data"]["role"];
        $idManager=$user_required->idusers;
        return array_reduce($arr,function($c,$u)use($user,$sqlF,&$i,$role,$idManager){
           // echo "$role $idManager=>".$u->manager_id;
            if($role=="manager"){
            if($u->manager_id==$idManager){
                 $c[$i]=$u->getArray();
                 $i++;
            }}else{
            $c[$i]=$u->getArray();
            if($user){
                $c[$i]["manager"]=$u->getManager($sqlF)?->getArray();
            }
            $i++;
        }
            return $c;
        },[]);
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
    public static function getPannes($ids=0){
          
     $result=  self::getSQLFactory()->execQuery("SELECT p.id as idPanne, p.code, p.diagnostique, c.label, c.id
        FROM pannes p
        JOIN panne_caracteristique pc ON p.id = pc.panne_id
        JOIN caracteristiques c ON pc.caracteristique_id = c.id
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