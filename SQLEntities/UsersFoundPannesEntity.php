<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\UsersFoundPannes;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `UsersFoundPannes`.
* Hérite de `UsersFoundPannes`. Ajoutez ici vos propres méthodes.
*/
class UsersFoundPannesEntity extends UsersFoundPannes
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=UsersFoundPannes::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(UsersFoundPannes::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\UsersFoundPannesEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getUsersFoundPannesBy($sqlF,$key,$value,$filter=null){
      $arr=UsersFoundPannes::getUsersFoundPannesBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\UsersFoundPannesEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }