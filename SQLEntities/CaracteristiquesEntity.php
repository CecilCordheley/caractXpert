<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Caracteristiques;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Caracteristiques`.
* Hérite de `Caracteristiques`. Ajoutez ici vos propres méthodes.
*/
class CaracteristiquesEntity extends Caracteristiques
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=Caracteristiques::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Caracteristiques::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\CaracteristiquesEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getCaracteristiquesBy($sqlF,$key,$value,$filter=null){
      $arr=Caracteristiques::getCaracteristiquesBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\CaracteristiquesEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }