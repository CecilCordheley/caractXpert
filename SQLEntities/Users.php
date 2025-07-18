<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class Users{
    private $attr=["idusers"=>'',"uuidUser"=>'',"nomUser"=>'',"prenomUser"=>'',"mailUser"=>'',"password_hash"=>'',"created_at"=>'',"roleUser"=>'',"manager_id"=>''];
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
    public static function  add(SQLFactory $sqlF,Users &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"users");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idusers=$sqlF->lastInsertId("users");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,Users $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"users");
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
    public static function  del(SQLFactory $sqlF,Users $item){
      $sqlF->deleteItem($item->idusers,"users");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM users");
      $return=[];
      foreach($query as $element){
      $entity=new Users();
         $entity->idusers=$element["idusers"];
$entity->uuidUser=$element["uuidUser"];
$entity->nomUser=$element["nomUser"];
$entity->prenomUser=$element["prenomUser"];
$entity->mailUser=$element["mailUser"];
$entity->password_hash=$element["password_hash"];
$entity->created_at=$element["created_at"];
$entity->roleUser=$element["roleUser"];
$entity->manager_id=$element["manager_id"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getUsersBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM users WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new Users();
         $entity->idusers=$element["idusers"];
$entity->uuidUser=$element["uuidUser"];
$entity->nomUser=$element["nomUser"];
$entity->prenomUser=$element["prenomUser"];
$entity->mailUser=$element["mailUser"];
$entity->password_hash=$element["password_hash"];
$entity->created_at=$element["created_at"];
$entity->roleUser=$element["roleUser"];
$entity->manager_id=$element["manager_id"];
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