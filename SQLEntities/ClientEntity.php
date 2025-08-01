<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Client;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Client`.
* Hérite de `Client`. Ajoutez ici vos propres méthodes.
*/
class ClientEntity extends Client
{
   // Ajoutez vos méthodes ici

   public function getPannes($sqlF){
      return PannesEntity::getPannesBy($sqlF,"client_id",$this->client_id);
   }
   public function getCaracterisiques($sqlF){
      return CaracteristiquesEntity::getCaracteristiquesBy($sqlF,"client_id",$this->client_id);
   }
   public function getCategories($sqlF){
      return CategorieEntity::getCategorieBy($sqlF,"client_id",$this->client_id);
   }
   public function getPanneEvents($sqlF){
      return PanneEvent::getPanneEventBy($sqlF,"client_id",$this->client_id);
   }
   public function getUSers($sqlF){
      return UsersEntity::getUsersBy($sqlF,"client_id",$this->client_id);
   }
   public static function getAll($sqlF){
    $arr=Client::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Client::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\ClientEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getClientBy($sqlF,$key,$value,$filter=null){
      $arr=Client::getClientBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\ClientEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\ClientEntity");
    }else{
      return false;
    }
      }
 }