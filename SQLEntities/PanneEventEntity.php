<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\PanneEvent;
use SQLEntities\PanneHasEventEntity;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `PanneEvent`.
* Hérite de `PanneEvent`. Ajoutez ici vos propres méthodes.
*/
class PanneEventEntity extends PanneEvent
{
   // Ajoutez vos méthodes ici

   public function getPannes($sqlf){
      $pannesids=PanneHasEventEntity::getPanneHasEventBy($sqlf,"idEvent",$this->idEvent);
      if($pannesids==false){
        return 0;
      }
      $arr=is_array($pannesids)?$pannesids:[$pannesids];
      $return=array_reduce($arr,function($car,$el)use($sqlf){
        $p=PannesEntity::getPannesBy($sqlf,"id",$el->pannes_id);
        $car[]=$p->getArray();
        return $car;
      },[]);
      return $return;
   }
   public static function getAll($sqlF){
    $arr=PanneEvent::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(PanneEvent::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\PanneEventEntity");
      return $c;
    },[]);
  }else
    return Main::fixObject($arr,"SQLEntities\PanneEventEntity");
    }else
    return false;
  }
    public static function getPanneEventBy($sqlF,$key,$value,$filter=null){
      $arr=PanneEvent::getPanneEventBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\PanneEventEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\PanneEventEntity");
    }else{
      return false;
    }
      }
 }