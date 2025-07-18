<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class UsersFoundPannes{
    private $attr=["user"=>'',"panne"=>'',"date_found"=>'',"comment_pannes"=>''];
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
    public static function  add(SQLFactory $sqlF,UsersFoundPannes &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"users_found_pannes");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
     // $item->user=$sqlF->lastInsertId("users_found_pannes");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,UsersFoundPannes $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"users_found_pannes");
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
    public static function  del(SQLFactory $sqlF,UsersFoundPannes $item){
      $sqlF->deleteItem($item->user,"users_found_pannes");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM users_found_pannes");
      $return=[];
      foreach($query as $element){
      $entity=new UsersFoundPannes();
         $entity->user=$element["user"];
$entity->panne=$element["panne"];
$entity->date_found=$element["date_found"];
$entity->comment_pannes=$element["comment_pannes"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getUsersFoundPannesBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM users_found_pannes WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new UsersFoundPannes();
         $entity->user=$element["user"];
$entity->panne=$element["panne"];
$entity->date_found=$element["date_found"];
$entity->comment_pannes=$element["comment_pannes"];
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