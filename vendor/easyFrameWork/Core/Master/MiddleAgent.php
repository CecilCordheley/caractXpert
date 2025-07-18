<?php
 namespace Vendor\EasyFrameWork\Core\Master;

use Exception;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\TokenManager;

 class MiddleAgent{
    private static $exception=["onExpire"=>[],"onInvalid"=>[]];
   public static function INIT() {
   $file = "../include/MdlAgtEvt";
if (file_exists($file)) {
    $content = file_get_contents($file);
    self::$exception = unserialize($content);
} else {
    self::$exception = [
        "onExpire" => [],
        "onInvalid" => []
    ];
}
    }
    public static function delegateRole($user,$role,$expire){
        $roles=["admin","manager","dev"];
        if(Main::isUUID($user,"light") && in_array($role,$roles)){
        TokenManager::delegate($user,$role,$expire);
        return true;
        }else{
            return false;
        }
    }
    public static function refreshToken(){
        $t=self::getToken();
       return TokenManager::refreshToken($t);
    }
    private static function commit(){
        $file="../include/MdlAgtEvt";
        $evts=serialize(self::$exception);
        file_put_contents($file,$evts);
    }
  public static function attachEvent($evt, $callback) {
    if (!in_array($evt, ["onExpire", "onInvalid"])) {
        throw new Exception("Invalid Event");
    }

    if (!is_string($callback) && !(is_array($callback) && is_string($callback[0]) && is_string($callback[1]))) {
        throw new Exception("Only named functions or static class methods allowed");
    }

    self::$exception[$evt][] = $callback;
    self::commit();
}
private static function getToken(){
     $headers = getallheaders();
        $token = null;

        if (isset($headers['Authorization'])) {
            $authorizationHeader = $headers['Authorization'];
            if (preg_match('/Bearer : (.+)/', $authorizationHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            throw new Exception("No token header", 401);
        }
        return $token;
}
    public static function checkToken(){
         $headers = getallheaders();
        $token = null;

        if (isset($headers['Authorization'])) {
            $authorizationHeader = $headers['Authorization'];
            if (preg_match('/Bearer : (.+)/', $authorizationHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            throw new Exception("No token header", 401);
        }

        $dataUser = TokenManager::verify($token);

        if (!is_array($dataUser)) {
            if ($dataUser == -1) {
               /* foreach(self::$exception["onExpire"] as $fnc){
                    call_user_func($fnc,$token);
                }*/
                throw new Exception("Token expired", 401);
            }
           
            foreach(self::$exception["onInvalid"] as $fnc){
                    call_user_func($fnc,$token);
                }
            throw new Exception("Invalid token", 401);
        }

        return $dataUser; // Retourne les infos du user lié au token
    }
    public static function checkTokenAndRole($requiredRole) {
        $userData = self::checkToken();
        if(gettype($requiredRole)=="array"){
            if (!isset($userData['data']['role']) || !in_array($userData['data']['role'], $requiredRole)) {
            throw new Exception("Insufficient permissions", 403);
        }
        }else
        if (!isset($userData['data']['role']) || $userData['data']['role'] !== $requiredRole) {
            throw new Exception("Insufficient permissions", 403);
        }

        return $userData;
    }
 }