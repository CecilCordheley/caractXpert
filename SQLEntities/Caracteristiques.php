<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class Caracteristiques{
    private $attr=["id"=>'',"label"=>'',"client_id"=>''];
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
    public static function  add(SQLFactory $sqlF,Caracteristiques &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"caracteristiques");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->id=$sqlF->lastInsertId("caracteristiques");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,Caracteristiques $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"caracteristiques");
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
    public static function  del(SQLFactory $sqlF,Caracteristiques $item){
      $sqlF->deleteItem($item->id,"caracteristiques");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM caracteristiques");
      $return=[];
      foreach($query as $element){
      $entity=new Caracteristiques();
         $entity->id=$element["id"];
$entity->label=$element["label"];
$entity->client_id=$element["client_id"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getCaracteristiquesBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM caracteristiques WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new Caracteristiques();
         $entity->id=$element["id"];
$entity->label=$element["label"];
$entity->client_id=$element["client_id"];
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