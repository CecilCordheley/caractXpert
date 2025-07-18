<?php

namespace Commandes;
require __DIR__.'/../../vendor/easyFrameWork/Core/Master/EasyFrameWork.php';
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EnvParser;
class CrypterCommande
{
    public function __invoke(string $chaine,$action=0)
    {
        $crypto=new Cryptographer;
        $env=new EnvParser(EasyFrameWork::$Racines["dirAccess"]."/.env");
        switch($action){
            case 0:
                $str=$crypto->hashString($chaine,$env->get("KEY"),Cryptographer::HASH_ALGO["MD2"]);
                break;
            case 1:
                $str=$crypto->encrypt($chaine,$env->get("KEY"));
                break;
            case 2:
                $str=$crypto->decrypt($chaine,$env->get("KEY"));
        }
        
        // Votre logique de cryptage ici
        return ($str);
    }
}