<?php

namespace App\Config;

use mysqli;
use App\Config\Env;
use Exception;


class Database
{
    private static $connection = null;

    private function __construct()
    {
        // Private constructor to prevent instantiation
    }

    public static function getConnection()
    {
        if (self::$connection === null) {
            self::$connection = new mysqli(
                Env::get('DB_HOST'),
                Env::get('DB_USERNAME'),
                Env::get('DB_PASSWORD'),
                Env::get('DB_NAME')
            );
        }

        if (self::$connection->connect_error) {
            throw new Exception("MySQL Connection failed: " . self::$connection->connect_error);
        }

        return self::$connection;
    }
}
