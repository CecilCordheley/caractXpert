<?php
require __DIR__ . '/../vendor/easyFrameWork/Core/Master/Autoloader.php';

use SQLEntities\UsersEntity;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Utils\MyConsole;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\Autoloader;
use Vendor\EasyFrameWork\Core\Master\MiddleAgent;
use vendor\easyFrameWork\Core\Utils\Logger;


EasyFrameWork::INIT("./../vendor/easyFrameWork/Core/config/config.json");
Autoloader::register();

MyConsole::register("seeDir", function ($args) {
    MiddleAgent::INIT();
    $userData = MiddleAgent::checkTokenAndRole("dev");
    $basePath = realpath(__DIR__ . "/../");  // racine autorisée
    $relativePath = $args["arg0"] ?? "";
    $dir = realpath($basePath . "/" . $relativePath);

    // Sécurité : vérifier que le répertoire est bien dans la racine autorisée
    if ($dir === false || strpos($dir, $basePath) !== 0) {
        return ["error" => "Invalid directory"];
    }

    if (!is_dir($dir) || !is_readable($dir)) {
        return ["error" => "Directory not accessible"];
    }

    $entries = scandir($dir);
    $filtered = array_values(array_filter($entries, function ($el) {
        return !in_array($el, [".", "..", ".env", ".htaccess",".ghost.log"]);
    }));

    return $filtered;
});

MyConsole::register("seeAgent",function(){
     $sqlF = new SQLFactory(null,__DIR__."/../include/config.ini");
    $users=UsersEntity::getAll($sqlF);
    return array_reduce($users,function($car,$el){
        $car[]=$el->getArray();
        return $car;
    },[]);
});
MyConsole::register('test',function($args){
    var_dump($args);
    return `il y a `.count($args)." paramètres";
});
MyConsole::register('resetToken', function () {
     MiddleAgent::INIT();
    $userData = MiddleAgent::checkTokenAndRole("dev");
    if(file_put_contents("../include/tokens.json", json_encode([]))!=false)
        return "token file cleared !";
    
});
MyConsole::register('archiveToken', function ($args) {
     MiddleAgent::INIT();
    $userData = MiddleAgent::checkTokenAndRole("dev");
    $content=file_get_contents("../include/tokens.json");
    if(!isset($args['file'])){
        return "file argument missing";
    }
    return file_put_contents("../".$args["file"],$content);
    
});
MyConsole::register("delegate",function($args){
Logger::init("../include/.ghost.log",true);
     MiddleAgent::INIT();
    $userData = MiddleAgent::checkTokenAndRole("dev");
    $user = $args["arg0"] ?? "";
    $role=$args["arg1"]??"";
    $expire=$args["arg2"]??"";
    if($user==""){
        echo json_encode(["status"=>"error","message"=>"user Param missing"]);
        exit();
    }
    if($role==""){
        echo json_encode(["status"=>"error","message"=>"role Param missing"]);
        exit();
    }
    if($expire==""){
        echo json_encode(["status"=>"error","message"=>"expiration Param missing"]);
        exit();
    }
    if(MiddleAgent::delegateRole($user,$role,$expire)){
        return true;
    }else{
        echo json_encode(["status"=>"error","message"=>"user is not a UUID or role is not a admissing role"]);
        exit();
    }
});
MyConsole::register("ResetPwd",function($args){
    Logger::init("../include/.ghost.log",true);
     MiddleAgent::INIT();
    $userData = MiddleAgent::checkTokenAndRole("dev");
    $sqlF = new SQLFactory(null,__DIR__."/../include/config.ini");
    $uuidAgent = $args["arg0"] ?? "";
    $userSelect=UsersEntity::getUsersBy($sqlF,"uuidUser",$uuidAgent);
    if($userSelect==false){
        echo json_encode(["status"=>"error","message"=>"no user found with $uuidAgent"]);
    }
    $userSelect->password_hash="";
    Logger::write($userData["user"]."reset PWD for $uuidAgent");
    if(UsersEntity::update($sqlF,$userSelect))
        return "passWord $uuidAgent reset !";

});
MyConsole::register("writeFile",function($args){
     MiddleAgent::INIT();
    $userData = MiddleAgent::checkTokenAndRole("dev");
    if(!file_exists("../".$args["file"])){
        throw new Exception("file ".$args["file"]." doesn't exist in the current context");
    }
    $content=file_get_contents("../".$args["file"]);
    $content.=html_entity_decode($args["message"]);
    return file_put_contents("../".$args["file"],$content);
});