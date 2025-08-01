<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Pannes;
use SQLEntities\PanneEventEntity;
use SQLEntities\PanneHasEventEntity;
use vendor\easyFrameWork\Core\Main;
use Exception;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

/**
* Class personnalisée pour la table `Pannes`.
* Hérite de `Pannes`. Ajoutez ici vos propres méthodes.
*/
class PannesEntity extends Pannes
{
  public static function getNbPannes($sqlF,$client_id){
    $panne=UsersFoundPannesEntity::getAll($sqlF);
    return array_reduce($panne,function($car,$el)use($sqlF,$client_id){
      if(isset($car[$el->panne])){
        $car[$el->panne]["nb"]++;
      }else{
        $p=PannesEntity::getPannesBy($sqlF,"id",$el->panne,function($el)use($client_id){return $el->client_id==$client_id;});
        $car[$el->panne]=["panne"=>$p->getArray()??false,"nb"=>1];
      }
      return $car;
    },[]);
  }
   // Ajoutez vos méthodes ici
       public function getCategorie($sqlF){
      return CategorieEntity::getCategorieBy($sqlF,"idcategorie",$this->categorie);
    }
public function dissociateEvent($sqlF,$idEvent){
    $panne=PanneHasEventEntity::getPanneHasEventBy($sqlF,"pannes_id",$this->id,function($el)use($idEvent){
      return $el->idEvent==$idEvent;
    });
    PanneHasEventEntity::del($sqlF,$panne);
   }
   public function associateEvent($sqlF,$idEvent){
    $panne_has_event=new PanneHasEventEntity;
    $panne_has_event->pannes_id=$this->id;
    $panne_has_event->idEvent=$idEvent;
    return PanneHasEventEntity::add($sqlF,$panne_has_event);
   }
   public function getEvents($sqlF){
    $panne_has_event=PanneHasEventEntity::getPanneHasEventBy($sqlF,"pannes_id",$this->id);
    if($panne_has_event==false){
      return false;
    }
    $arr=is_array($panne_has_event)?$panne_has_event:[$panne_has_event];
    $events=array_reduce($arr,function($car,$el) use($sqlF){
      $event=PanneEventEntity::getPanneEventBy($sqlF,"idEvent",$el->idEvent);
      $car[]=$event->getArray();
      return $car;
    },[]);
    return $events;
   }
   public function getFullArray($sqlF){
    $a=$this->getArray();
    $a["categorie"]=$this->getCategorie($sqlF)->getArray();
    return $a;
   }
   public static function getAll($sqlF){
    $arr=Pannes::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Pannes::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\PannesEntity");
      return $c;
    },[]);
  }else
    return Main::fixObject($arr,"SQLEntities\PannesEntity");
    }else
    return false;
  }
    public static function getPannesBy($sqlF,$key,$value,$filter=null){
      $arr=Pannes::getPannesBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\PannesEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\PannesEntity");
    }else{
      return false;
    }
      }
 }