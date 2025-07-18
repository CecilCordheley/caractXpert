<?php
namespace vendor\easyFrameWork\Core\Master;

class Autoloader
{
    /**
     * Enregistre notre autoloader
     */
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
     * Charge le fichier correspondant à notre classe si celui-ci existe
     * @param string $class Le nom complet de la classe à charger
     */
    public static function autoload(string $class)
{
    $class = str_replace('\\', '/', $class);
    $baseDir = dirname(__DIR__, 4); // remonte à la racine depuis vendor/easyFrameWork/Core/Master/
    $file = $baseDir . '/' . $class . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    } else {
        error_log("Autoload failed for $file");
    }
}

}
