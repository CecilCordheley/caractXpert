<?php

namespace vendor\easyFrameWork\Core\Master;

use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\Router;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\Autoloader;
use vendor\easyFrameWork\Core\Master\AjaxPHPTranspiler;
use Exception;
abstract class EasyFrameWork
{
    public static $Racines = [];
    public static $appVar=[];
    public static function showClasses(){
        $classe=get_declared_classes();
        self::Debug($classe);

    }
    public static function INIT($configAccess="../vendor/easyFrameWork/Core/config/config.json")
    {
        if(session_id()==null)
            session_start();
     //    echo "<!--INIT EasyFrameWork-->";
        require_once("Autoloader.php");
        Autoloader::register();
       // echo $configAccess;
      //  EasyFrameWork::Debug($_SERVER);
        self::$Racines = json_decode(file_get_contents($configAccess), true)["racine"];
        //   EasyFrameWork::Debug(self::$Racines);
        self::$appVar=json_decode(file_get_contents(self::$Racines["dirAccess"]."/include/application.json"),true);
        
    }
    public static function getVar($name=null){
        if($name)
            return self::$appVar[$name];
        else
            return self::$appVar;
    }
public static function Debug(mixed $var, bool $exit = true,$logFile=false)
{
    // Convertir la variable en chaîne de caractères
    ob_start();
    print_r($var);
    $output = ob_get_clean();

    // Échapper les caractères spéciaux
    $output = htmlspecialchars($output);

    // Ajouter un formatage HTML pour les champs (exemple pour tableau ou objet)
    $formattedOutput = preg_replace(
        [
            '/\[([^\]]+)\]/',             // Capturer les clés (entre crochets)
            '/\(([^()]+)\)/'              // Optionnel : capturer les parenthèses (si besoin)
        ],
        [
            '<span style="color: blue; font-weight: bold;">[$1]</span>', // Ajouter une couleur aux clés
            '(<span style="color: green;">$1</span>)'                  // Ajouter une couleur pour les contenus parenthésés
        ],
        $output
    );

    // Encapsuler dans un bloc stylisé
    echo '<div style="background: #f9f9f9; border: 1px solid #ccc; padding: 10px; margin: 10px; font-family: monospace;">';
    echo '<pre>' . $formattedOutput . '</pre>';
    echo '</div>';
    if ($logFile) {
        file_put_contents($logFile, print_r($var, true), FILE_APPEND);
    }
    if ($exit) {
        exit;
    }
}

    public static function toCamelCase(string $input): string
    {
        return preg_replace_callback('/(?:^|_)([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $input);
    }
    private static $classes = [];

    // Méthode pour enregistrer une classe dans le tableau
    public static function registerClass(string $className, $classInstance) {
        self::$classes[$className] = $classInstance;
    }

    // Méthode pour obtenir une instance de classe à partir du nom de classe
    public static function getClassInstance($className) {
        if (isset(self::$classes[$className])) {
            return self::$classes[$className];
        } else {
            throw new \Exception("Classe '$className' non enregistrée.");
        }
    }
}
class EnvParser {
    private $envData;

    public function __construct($envFilePath) {
        if (!file_exists($envFilePath)) {
            throw new Exception("Le fichier $envFilePath n\'existe pas.");
        }

        // Lire le contenu du fichier .env
        $envContent = file_get_contents($envFilePath);

        // Diviser le contenu en lignes
        $lines = explode("\n", $envContent);

        // Parcourir chaque ligne et extraire les variables
        foreach ($lines as $line) {
            // Ignorer les lignes vides et les commentaires
            if (trim($line) !== '' && strpos(trim($line), '#') !== 0) {
                // Diviser chaque ligne en clé et valeur
                list($key, $value) = explode('=', $line, 2);

                // Supprimer les espaces et les guillemets de la valeur
                $value = trim($value);
                $value = trim($value, "'\"");
                
                // Ajouter la paire clé-valeur au tableau
                $this->envData[$key] = $value;
            }
        }
    }

    // Méthode pour récupérer une variable d'environnement
    public function get($key) {
        if (isset($this->envData[$key])) {
            return $this->envData[$key];
        } else {
            return null;
        }
    }
}
class Debug
{
}
class Cryptographer
{
    public const HASH_ALGO = [
        "MD2" => "md2",
        "MD4" => "md4",
        "MD5" => "md5",
        "SHA1" => "sha1",
        "SHA256" => "sha256",
        "SHA384" => "sha384",
        "SHA512" => "sha512",
        "RIPEMD128" => "ripemd128",
        "RIPEMD160" => "ripemd160",
        "RIPEMD256" => "ripemd256",
        "RIPEMD320" => "ripemd320",
        "WHIRLPOOL" => "whirlpool",
        "TIGER128,3" => "tiger128,3",
        "TIGER160,3" => "tiger160,3",
        "TIGER192,3" => "tiger192,3",
        "TIGER128,4" => "tiger128,4",
        "TIGER160,4" => "tiger160,4",
        "TIGER192,4" => "tiger192,4",
        "SNEFRU" => "snefru",
        "GOST" => "gost",
        "ADLER32" => "adler32",
        "CRC32" => "crc32",
        "CRC32B" => "crc32b",
        "HAVAL128,3" => "haval128,3",
        "HAVAL160,3" => "haval160,3",
        "HAVAL192,3" => "haval192,3",
        "HAVAL224,3" => "haval224,3",
        "HAVAL256,3" => "haval256,3",
        "HAVAL128,4" => "haval128,4",
        "HAVAL160,4" => "haval160,4",
        "HAVAL192,4" => "haval192,4",
        "HAVAL224,4" => "haval224,4",
        "HAVAL256,4" => "haval256,4",
        "HAVAL128,5" => "haval128,5",
        "HAVAL160,5" => "haval160,5",
        "HAVAL192,5" => "haval192,5",
        "HAVAL224,5" => "haval224,5",
        "HAVAL256,5" => "haval256,5"
    ];
    public function encrypt($string, $key)
    {
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $encryption_iv = '1234567891011121';
        $encryption = openssl_encrypt(
            $string,
            $ciphering,
            $key,
            0,
            $encryption_iv
        );
        return $encryption;
    }
    public function hashString(string $str, string $key = "", string $algo = "sha256"): string
    {
        $return = hash($algo, $str);
        if ($key != "") {
            return self::encrypt($return, $key);
        } else
            return $return;
    }
    public static function decrypt(string $content, string $key): string
    {
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $encryption_iv = '1234567891011121';
        $encryption = openssl_decrypt(
            $content,
            $ciphering,
            $key,
            0,
            $encryption_iv
        );
        return $encryption;
    }
}
class GhostLog{
    private string $file;
    private array $entries;
    private $key;
    private bool $secured;
    public function __construct(string $filename,$secured=true){
        if(!file_exists($filename)){
            throw new Exception("$filename doesn't exist in the current context");
        }
        $env=new EnvParser(EasyFrameWork::$Racines["dirAccess"]."./.env");
        $this->secured=$secured;
        $this->key= $env->get("GostLog_KEY");
        $this->file=$filename;
        $this->entries=[];
    }
    public function getEntries($mdp){
        if($this->key!=$mdp){
            throw new Exception("Not a valid GhostKey");
        }
        return $this->entries;
    }
    public function open($mdp){
        if($this->key!=$mdp){
            throw new Exception("Not a valid GhostKey");
        }
        $content=file_get_contents($this->file);
         if($this->secured)
            $this->secure($content,1);
        $this->entries=explode("\n",$content);
    }
    private function secure(string &$content,int $state=0){
        $crypt=new Cryptographer;
        if($state==0)
            $content=$crypt->encrypt($content,$this->key);
        else
            $content=$crypt->decrypt($content,$this->key);
    }
    public function commit(){
        $content=implode("\n",$this->entries);
        if($this->secured)
            $this->secure($content,0);
        return file_put_contents($this->file,$content);
    }
    public function addEntries($content){
        $this->entries[]=date("Y-m-d H:i:s")." - $content";
    }
}
