<?php
 namespace apis\module\asyncModule;

 use DateTime;
use Exception;
use SQLEntities\UsersEntity;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\Autoloader;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
 use vendor\easyFrameWork\Core\Master\GhostLog;
use vendor\easyFrameWork\Core\Master\SessionManager;
 use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\EnvParser;
use vendor\easyFrameWork\Core\Master\SQLFactory;
abstract class ViewFnc{
     private static function getSQLFactory(){
        return new SQLFactory(null,"../include/config.ini");
    } 
    public static function callView($view){
         if(!file_exists("./views/$view.view")){
           return ["status"=>"error","message"=>"File doesn't esist"];
        }
        return file_get_contents("./views/$view.view");
    }
    public static function getPannesArray(){
         if(!file_exists("./views/pannesArray.view")){
           return ["status"=>"error","message"=>"File doesn't esist"];
        }
        return file_get_contents("./views/pannesArray.view");
    }
    public static function newUser(){
         if(!file_exists("./views/newUser.view")){
           return ["status"=>"error","message"=>"File doesn't esist"];
        }
        return file_get_contents("./views/newUser.view");
    }
    public static function getUsers(){
        if(!file_exists("./views/seeUser.view")){
           return ["status"=>"error","File doesn't esist"];
        }
        return file_get_contents("./views/seeUser.view");
    }
}