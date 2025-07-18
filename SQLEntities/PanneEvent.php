<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class PanneEvent{
    private $attr=["idEvent"=>'',"event_name"=>'',"event_callBack"=>'',"refEvent"=>''];
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
    public static function  add(SQLFactory $sqlF,PanneEvent &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"panne_event");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idEvent=$sqlF->lastInsertId("panne_event");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,PanneEvent $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"panne_event");
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
    public static function  del(SQLFactory $sqlF,PanneEvent $item){
      $sqlF->deleteItem($item->idEvent,"panne_event");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM panne_event");
      $return=[];
      foreach($query as $element){
      $entity=new PanneEvent();
         $entity->idEvent=$element["idEvent"];
$entity->event_name=$element["event_name"];
$entity->event_callBack=$element["event_callBack"];
$entity->refEvent=$element["refEvent"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getPanneEventBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM panne_event WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new PanneEvent();
         $entity->idEvent=$element["idEvent"];
$entity->event_name=$element["event_name"];
$entity->event_callBack=$element["event_callBack"];
$entity->refEvent=$element["refEvent"];
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