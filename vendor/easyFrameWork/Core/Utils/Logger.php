<?php
namespace vendor\easyFrameWork\Core\Utils;

use vendor\easyFrameWork\Core\Master\GhostLog;

class Logger {
    private static ?GhostLog $log = null;

    public static function init(string $path, bool $secured = true) {
        if (self::$log === null) {
            self::$log = new GhostLog($path, $secured);
            self::$log->open("2aE53a9");
        }
    }

    public static function write(string $message) {
        if (!self::$log) {
            throw new \Exception("Logger not initialized");
        }
        self::$log->addEntries($message);
        self::$log->commit();
    }

    public static function getLog() {
        return self::$log;
    }
}
