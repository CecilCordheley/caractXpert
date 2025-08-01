<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class Client{
    private $attr=["client_id"=>'',"client_uuid"=>'',"client_name"=>'',"client_info"=>'',"date_client"=>''];
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
    public static function  add(SQLFactory $sqlF,Client &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"client");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->client_id=$sqlF->lastInsertId("client");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,Client $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"client");
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
    public static function  del(SQLFactory $sqlF,Client $item){
      $sqlF->deleteItem($item->client_id,"client");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM client");
      $return=[];
      foreach($query as $element){
      $entity=new Client();
         $entity->client_id=$element["client_id"];
$entity->client_uuid=$element["client_uuid"];
$entity->client_name=$element["client_name"];
$entity->client_info=$element["client_info"];
$entity->date_client=$element["date_client"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getClientBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM client WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new Client();
         $entity->client_id=$element["client_id"];
$entity->client_uuid=$element["client_uuid"];
$entity->client_name=$element["client_name"];
$entity->client_info=$element["client_info"];
$entity->date_client=$element["date_client"];
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