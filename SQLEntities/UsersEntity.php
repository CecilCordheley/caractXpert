<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Users;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\Token;
use vendor\easyFrameWork\Core\Master\TokenManager;
use SQLEntities\UsersFoundPannesEntity;
use Exception;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

/**
* Class personnalisée pour la table `Users`.
* Hérite de `Users`. Ajoutez ici vos propres méthodes.
*/
class UsersEntity extends Users
{

  public function getClient($sqlF){
    return ClientEntity::getClientBy($sqlF,"client_id",$this->client);
  }
  public function getPanneHistory($sqlF){
    $UFP=UsersFoundPannesEntity::getUsersFoundPannesBy($sqlF,"user",$this->idusers);
    return array_reduce($UFP,function($car,$el)use($sqlF){
      $p=PannesEntity::getPannesBy($sqlF,"id",$el->panne);
      if($p!=false){
        $car[]=["panne"=>$p->getFullArray($sqlF),"date"=>$el->date_found,"comment"=>$el->comment_pannes];
      }
      return $car;
    },[]);
  }
  public function getManager($sqlF){
    if($this->manager_id!=0){
      $manager=UsersEntity::getUsersBy($sqlF,"idusers",$this->manager_id);
      if($manager==false){
        return false;
      }else{
        return $manager;
      }
    }
  }
   public function FoundPanne(SQLFactory $sqlF,PannesEntity $panne,$comment=""){
    $ufp=new UsersFoundPannesEntity;
    $ufp->user=$this->idusers;
    $ufp->panne=$panne->id;
    $ufp->date_found=date("Y-m-d H:i:s");
    $ufp->comment_pannes=$comment;
    return UsersFoundPannesEntity::add($sqlF,$ufp);
   }
  public static function connexion($sqlF,$mail,$mdp,$callBack=null){

    $users = self::getUsersBy($sqlF, 'mailUser', $mail);
    
    if (!$users) {
        throw new Exception("Utilisateur introuvable");
    }
    if($users->password_hash==""){
      throw new Exception("First connexion");
    }
     $user = $users;
     $crypto=new Cryptographer();
     $hash_password=$crypto->hashString($mdp);
     if($user->password_hash!=$hash_password){
      throw new Exception("Mot de passe incorect");
     }
     if($callBack!=null){
      call_user_func($callBack,$user);
     }
     $token=TokenManager::generate($user->uuidUser,$user->roleUser);
     $delegate=TokenManager::getDelegate($user->uuidUser);
     $client=$user->getClient($sqlF);
     if($delegate==false)
      return [
        "token" => $token,
        "role" => $user->roleUser,
        "user_id" => $user->uuidUser,
        "client"=>$client->getArray()??""
    ];
    else{
      
      return [
        "token" => $token,
        "delegate"=>$delegate,
        "role" => $user->roleUser,
        "user_id" => $user->uuidUser,
        "client"=>$client->getArray()??""
    ];
  }
  }
   public static function getAll($sqlF){
    $arr=Users::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Users::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\UsersEntity");
      return $c;
    },[]);
  }else
    return Main::fixObject($arr,"SQLEntities\UsersEntity");
    }else
    return false;
  }
    public static function getUsersBy($sqlF,$key,$value,$filter=null){
      $arr=Users::getUsersBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\UsersEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\UsersEntity");
    }else{
      return false;
    }
      }
 }