<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\PanneHasEvent;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `PanneHasEvent`.
* Hérite de `PanneHasEvent`. Ajoutez ici vos propres méthodes.
*/
class PanneHasEventEntity extends PanneHasEvent
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=PanneHasEvent::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(PanneHasEvent::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\PanneHasEventEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getPanneHasEventBy($sqlF,$key,$value,$filter=null){
      $arr=PanneHasEvent::getPanneHasEventBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\PanneHasEventEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }