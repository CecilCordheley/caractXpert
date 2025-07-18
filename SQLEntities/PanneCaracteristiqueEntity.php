<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\PanneCaracteristique;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `PanneCaracteristique`.
* Hérite de `PanneCaracteristique`. Ajoutez ici vos propres méthodes.
*/
class PanneCaracteristiqueEntity extends PanneCaracteristique
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=PanneCaracteristique::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(PanneCaracteristique::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\PanneCaracteristiqueEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getPanneCaracteristiqueBy($sqlF,$key,$value,$filter=null){
      $arr=PanneCaracteristique::getPanneCaracteristiqueBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\PanneCaracteristiqueEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }