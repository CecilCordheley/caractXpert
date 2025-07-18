<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class PanneHasEvent{
    private $attr=["idEvent"=>'',"pannes_id"=>''];
    public function __set($name,$value){
      if (array_key_exists($name, $this->attr)) {
         $this->attr[$name]=$value;
     } else {
         throw new Exception("Propriété non définie : $name");
     }
    }
    public function getArray(){
      return $this->attr;
    }
    public function __get($name){
      if (array_key_exists($name, $this->attr)) {
         return $this->attr[$name];
     } else {
         throw new Exception("Propriété non définie : $name");
     }
    }
    public static function  add(SQLFactory $sqlF,PanneHasEvent &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"panne_has_event");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idEvent=$sqlF->lastInsertId("panne_has_event");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,PanneHasEvent $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"panne_has_event");
      if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
        echo "<pre>$return</pre>";
        return false;
      } else {
        if($callBack!=null){
          call_user_func($callBack,$item);
        }
        return true;
      }
    }
    public static function  del(SQLFactory $sqlF,PanneHasEvent $item){
      $sqlF->deleteItem($item->idEvent,"panne_has_event");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM panne_has_event");
      $return=[];
      foreach($query as $element){
      $entity=new PanneHasEvent();
         $entity->idEvent=$element["idEvent"];
$entity->pannes_id=$element["pannes_id"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getPanneHasEventBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM panne_has_event WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new PanneHasEvent();
         $entity->idEvent=$element["idEvent"];
$entity->pannes_id=$element["pannes_id"];
      $return[]=$entity;
      }
      if($filter!=null && count($return)>0){
        $return = array_filter($return,$filter);
      }
      if(count($return))
      return (count($return) > 1) ? $return : $return[0];
    else
      return false;
    }
 }