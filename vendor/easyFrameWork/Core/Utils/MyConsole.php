<?php
    namespace vendor\easyFrameWork\Core\Utils;

use Exception;

    class MyConsole
{
    private static $commands = [];

    public static function register($name, $callback)
    {
        if (!is_callable($callback)) {
            throw new Exception("La commande $name doit être une fonction valide.");
        }
        self::$commands[$name] = $callback;
    }
    public static function run($name, ...$args)
    {
        if (!isset(self::$commands[$name])) {
            throw new Exception("Commande inconnue : $name");
        }
        return call_user_func_array(self::$commands[$name], $args);
    }

    public static function list()
    {
        return array_keys(self::$commands);
    }
}
