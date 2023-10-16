<?php

namespace App\Core\PostgreSQL;

use App\Util;
use \PDO;

class Connect {

    private static $conn = null;

    protected function __construct() {

    }

    protected static function get()
    {
        if (null === static::$conn) {
            static::$conn = new self();
        }

        return static::$conn;
    }

    protected static function connect() {
        $config = Util::config('cdb');

        $connectString = 'pgsql:host=' . $config['cdb']['host'] . ' port=' . $config['cdb']['port'] . ' dbname=' . $config['cdb']['dbname'];
        $pdo = new PDO($connectString, $config['cdb']['user'], $config['cdb']['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        return $pdo;
    }
}

?>