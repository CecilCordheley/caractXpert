<?php
namespace apis;

use Exception;
use SQLEntities\TypeTicket;
require_once ("../vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
require_once ("../bin/consoleFnc.php");
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\AjaxPHPTranspiler;
require_once "../vendor/easyFrameWork/Core/Master/AjaxPHPTranspiler.php";
use vendor\easyFrameWork\Core\Master\Autoloader;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Utils\MyConsole;
use Vendor\EasyFrameWork\Core\Master\MiddleAgent;
use vendor\easyFrameWork\Core\Utils\Logger;


require_once "../vendor/easyFrameWork/Core/Master/SQLFactory.php";
require_once "../vendor/easyFrameWork/Core/Master/EasyGlobal.php";
EasyFrameWork::INIT("../vendor/easyFrameWork/Core/config/config.json");
MiddleAgent::INIT();

Autoloader::register();
//EasyFrameWork::showClasses();
if(isset($_GET["root"])){
    $root=$_GET["root"];
    if($root=="console_cmd"){
    // $user = MiddleAgent::checkTokenAndRole("dev");
    $return=["status"=>"success"];
    //check for param
    $param=array_slice($_GET,2);
    if(in_array($_GET["cmd"],MyConsole::list())){
     $return["result"]=MyConsole::run($_GET["cmd"],$param);
     $type=gettype($return["result"]);
     if($type=="array" && array_keys($return["result"]))
      $type="list";
     $return["type"]=$type;
     echo json_encode($return);
    }else{
      echo json_encode(["status"=>"error","message"=>"invalid command ".$_GET["cmd"]]);
    }
    }else{
     $transpiler = new AjaxPHPTranspiler(__DIR__,$root,true);
    if($root!="views"){
     $transpiler->setAction($_GET["action"]);
    }else{
      //  echo $_GET["name"];
        $view=$_GET["name"];
        $transpiler->setAction($view);
    }
    $transpiler->run();
  }
}

