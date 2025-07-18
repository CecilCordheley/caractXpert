<?php
namespace vendor\easyFrameWork\Core\Master;

use Exception;
use vendor\easyFrameWork\Core\Master\Cryptographer;
abstract class EasyGlobal{
    public static function createSessionManager():SessionManager
    {
        return new SessionManager();
    }
    /**
     * Retourne une instance de Request permetant de gérer les variables GET
     */
    public static function createRequestManager():Request{
        return new Request();
    }
    /**
     * Retourne une instance de ServerInfo permetant de retrouver les informations de la variable $_SERVER
     */
    public static function createServerInfo():ServerInfo{
        return new ServerInfo($_SERVER);
    }
    /**
     * retourne une instance de Query permetant des gérer les variables POST
     */
    public static function createQuery():Query{
        return new Query();
    }
    public static function createCookiesManager():CookieManager{
        return new CookieManager();
    }
}
/**
 * Récupère les informations relatives au Server
 */
class ServerInfo
{
    private $serverData;

    public function __construct(array $serverData)
    {
        $this->serverData = $serverData;
    }
    public function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
     //   echo "POP";
    //   var_dump($headers);
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
            if (preg_match('/OAuth\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
    public function getServerData(string $key)
    {
        return $this->serverData[$key] ?? null;
    }

    public function getSelf():string
    {
        return $this->getServerData("PHP_SELF");
    }

    public function getScriptName():string
    {
        return $this->getServerData("SCRIPT_NAME");
    }

    public function getScriptFilename():string
    {
        return $this->getServerData("SCRIPT_FILENAME");
    }

    public function getPath():string
    {
        return $this->getServerData("PATH_TRANSLATED");
    }

    public function getRoot():string
    {
        return $this->getServerData("DOCUMENT_ROOT");
    }

    public function getRequestFloatTime():string
    {
        return $this->getServerData("REQUEST_TIME_FLOAT");
    }

    public function getRacine():string
    {
        return explode('/', $this->getScriptName())[1] ?? null;
    }

    public function getRequestTime():string
    {
        return $this->getServerData("REQUEST_TIME");
    }

    public function getIpAddress():string
    {
        return $this->getServerData("REMOTE_ADDR") ?? $this->getServerData("SERVER_ADDR");
    }
}
/**
 * Permet de gérer les variables $_GET
 */
class Request
{
    private $getData;

    public function __construct()
    {
        $this->getData = $_GET;
    }

    public function getAll(): array
    {
        return $this->getData ?? null;
    }

    public function hasGetValues(): bool
    {
        return !empty($this->getData);
    }

    public function get($key)
    {
        if (is_string($key) && array_key_exists($key, $this->getData)) {
            return $this->getData[$key];
        } elseif (is_array($key) && count($key) == 2 && isset($this->getData[$key[0]][$key[1]])) {
            return $this->getData[$key[0]][$key[1]];
        } else {
            throw new Exception("Invalid key provided.");
        }
    }
}
/**
 * Permet de gérer les variable $_POST
 */
class Query
{
    private $postData;

    public function __construct()
    {
        if(isset($_POST))
            $this->postData = $_POST;
        else{
            $json=file_get_contents('php://input');
            if(isset($json)){
                $this->postData=json_decode($json);
            }
        }
    }

    public function getAll(): array
    {
        return [
            "date" => date("Y-m-d"),
            "values" => $this->postData
        ];
    }

    public function hasPostValues(): bool
    {
        return !empty($this->postData);
    }

    public function get($key)
    {
        if (is_string($key) && array_key_exists($key, $this->postData)) {
            return $this->postData[$key];
        } elseif (is_array($key) && count($key) == 2 && isset($this->postData[$key[0]][$key[1]])) {
            return $this->postData[$key[0]][$key[1]];
        } else {
            throw new Exception("Invalid key provided.");
        }
    }
}
class CookieManager
{
    public function getAll()
    {
        return $_COOKIE ?? null;
    }

    public function get($key): mixed
    {
        return $_COOKIE[$key] ?? null;
    }

    public function set($key, $value, $expiration = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {
        setcookie($key, $value, $expiration, $path, $domain, $secure, $httponly);
    }
}

class SessionManager
{
    const PRIVATE_CONTEXT = "private";
    const PUBLIC_CONTEXT = "public";
    public function getId(): string
    {
        return session_id() ?? null;
    }
    public function getAll(): array
    {
        return $_SESSION ?? null;
    }
    public function delete(string $key, string $context = self::PUBLIC_CONTEXT): void
    {
        if (isset($_SESSION[$context][$key])) {
            unset($_SESSION[$context][$key]);
        } else
            throw new Exception("$key doesn't exist in $context context");
    }
    public function sessionExist(): bool
    {
        return session_id() != null;
    }
    public function clean(): void
    {
        if (self::sessionExist())
            session_destroy();
    }
    public function get(string $key, string $context = self::PUBLIC_CONTEXT): mixed
    {
        if (self::sessionExist())
            return $_SESSION[$context][$key] ?? null;
        else
            throw new Exception("Pas de session activée");
    }
    private function setPrivate($key, $value): void
    {
        if (!isset($_SESSION["private"])) {
            $_SESSION['private'] = [];
        }
        $crypt=new Cryptographer();
        $_SESSION['private'][$key] = $crypt->encrypt($value, session_id());
    }
    private function setPublic($key, $value): void
    {
        if (!isset($_SESSION["public"])) {
            $_SESSION['public'] = [];
        }

        $_SESSION['public'][$key] = $value;
    }
    public function set($key, $value, $context = self::PUBLIC_CONTEXT): void
    {
        if ($context == self::PRIVATE_CONTEXT) {
            self::setPrivate($key, $value);
        } else {
            self::setPublic($key, $value);
        }
    }
}