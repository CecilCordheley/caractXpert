<?php
namespace vendor\easyFrameWork\Core\Master;

use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use DateTime;
use Exception;
use Vendor\EasyFrameWork\Core\Master\MiddleAgent;

class TokenManager {
    private static $expireCount = []; 
    private static $store = [];
    private static $delegate=[];
    private static $filStorage=["current"=>"../include/tokens/tokens.json","delegate"=>"../include/tokens/delegate.json"];
    public static function setFileStorage($current,$delegate){
        self::$filStorage["current"]=$current;
        self::$filStorage["delegate"]=$delegate;
    }
    private static function INIT(){
        $content=file_get_contents(self::$filStorage["current"]);
        if($content==""){
            self::$store=[];
        }else{
            self::$store=json_decode($content,true);
        }
        $delegate=file_get_contents(self::$filStorage["delegate"]);
        if($delegate==""){
            self::$delegate=[];
        }else{
            self::$delegate=json_decode($delegate,true);
        }
    }
    public static function generate($userId, $role) {
        date_default_timezone_set("Europe/Paris");
        self::INIT();
        $token = bin2hex(random_bytes(32));
        $expire = (new DateTime())->modify('+15 minutes');
        $tkInfo=new Token($userId,$expire);
        $tkInfo->setData("role",$role);
        $delegate=self::getDelegate($userId);
        if($delegate!=false){
            $tkInfo->setData("delegate",$delegate);
        }
        self::$store[$token] = $tkInfo->getArray();
        file_put_contents(self::$filStorage["current"],json_encode(self::$store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $token;
    }
    public static function getDelegate($userId){
        self::INIT();
        if(isset(self::$delegate[$userId])){
            $expire=new DateTime(self::$delegate[$userId]["expire"]);
            if($expire>= new DateTime()){
                return self::$delegate[$userId]["role"];
            }
            return false;
        }
        return false;
    }
    public static function refreshToken($tokenKey){
        if (!isset(self::$store[$tokenKey])) return false;
        $data = self::$store[$tokenKey];
        $t=new Token($data['user'], (new DateTime())->modify('+15 minutes'));
        $t->setData("role",$data["data"]["role"]);
        if(isset($data["data"]["delegate"]))
            $t->setData("delegate",$data["data"]["delegate"]);
        self::$store[$tokenKey]= $t->getArray();
        file_put_contents(self::$filStorage["current"],json_encode(self::$store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return true;
    }
    public static function delegate($user,$role,$expire){
        self::$delegate[$user]=["role"=>$role,"expire"=>$expire];
        file_put_contents(self::$filStorage["delegate"],json_encode(self::$delegate, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);
    }
    public static function verify($tokenkey) {
        date_default_timezone_set("Europe/Paris");
         self::INIT();
      //  var_dump(self::$store);
        if (!isset(self::$store[$tokenkey])) return 0;
        $data = self::$store[$tokenkey];
        $token = new Token($data['user'], new DateTime($data['expire']["date"]));
        if ($token->isExpired()) {
            MiddleAgent::INIT();
          /*  MiddleAgent::attachEvent("onExpire",function($token)use($data){
                
            });*/
         /*   MiddleAgent::attachEvent("onExpire", 'handleTokenExpire');
            function handleTokenExpire($token) {
    error_log("Token expiré : " . $token);
}*/
            unset(self::$store[$tokenkey]);
            file_put_contents(self::$filStorage["current"],json_encode(self::$store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return -1;
        }
        return $data;
    }

    public static function revoke($token) {
        unset(self::$store[$token]);
        file_put_contents(self::$filStorage["current"],json_encode(self::$store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
class Token{
    private string $idUser;
    private array $data;
    private DateTime $expire;
    public function __construct($idUser,$expire){
        $this->idUser=$idUser;
        $this->expire=$expire;
        $this->data=[];
    }
    public function refresh(){
        date_default_timezone_set("Europe/Paris");
        $this->expire=(new DateTime())->modify('+15 minutes');
    }
    public function getRole(): ?string {
    return $this->data['role'] ?? null;
}
public function getUserId(): string {
    return $this->idUser;
}
public function getData($key=null){
    if(isset($key)){
       return $this->data[$key]??throw new Exception("No data for $key value");
    }else
        return $this->data;
}
    public function isExpired(): bool {
    return $this->expire < new DateTime();
}
    public function setData($key,$value){
        $this->data[$key]=$value;
    }
    public function getArray(){
        return["user"=>$this->idUser,"data"=>$this->data,"expire"=>$this->expire];
    }
    public function __toString()
    {
        return json_encode(["user"=>$this->idUser,"data"=>$this->data,"expire"=>$this->expire]);
    }
}
