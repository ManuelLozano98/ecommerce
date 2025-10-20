<?php

namespace App\Config;

use Dotenv\Dotenv;

class Env
{
    private static $env = null;

    public static function get($key)
    {
        if (self::$env === null) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
            self::$env = $_ENV;
        }
        return self::$env[$key] ?? null;
    }
}
