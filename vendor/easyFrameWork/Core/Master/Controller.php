<?php
namespace vendor\easyFrameWork\Core\Master;
abstract class Controller {
   private array $data;
   protected $envParser;
   public function __construct($env=null){
        $this->envParser=$env;

               $this->data=[];
   }
   abstract public function handleRequest();
   public function setData($key,$value){
       $this->data[$key]=$value;
   }
   protected function getData(){return $this->data;}
}