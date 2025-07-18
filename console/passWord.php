<?php
require_once ("../vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EnvParser;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;

//use Core\Master\Controller\HomeController;
//EasyFrameWork::INIT();

$env=new EnvParser("../.env");
/*
EasyFrameWork::registerClass("Cryptographer",new Cryptographer());
*/
$crypt=new Cryptographer;

//$str=readline();
$key= $env->get("KEY");
$str=substr($crypt->hashString(time(), $key,"MD2"),0,8);
echo "Votre mot de passe privé : $str";

$r = "";
$c = new Cryptographer;
$r = $c->hashString($str, $key,"MD2");
$r = str_replace(array("/", "+", "-", "\\"), '0', $r);
echo "\n Mot de passe crypté";
echo $r;