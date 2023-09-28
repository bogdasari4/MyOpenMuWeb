<?php

namespace App\Core\PostgreSQL;

use \PDO;

class Query extends \App\Core\PostgreSQL\Connect {

    /**
     * Summary of getRow
     * @param string $sql
     * @param array $prepare
     * @return mixed
     */
    public static function getRow(string $sql, array $prepare):mixed {

        $stmt = self::get()->connect()->prepare($sql);
        $stmt->execute($prepare);

        return $stmt->fetch(PDO::FETCH_NUM);
    }

    /**
     * Summary of getRowAll
     * @param string $sql
     * @param array $prepare
     * @return array
     */
    public static function getRowAll(string $sql, array $prepare):array {
        $stmt = self::get()->connect()->prepare($sql);
        $stmt->execute($prepare);

        return $stmt->fetchAll(PDO::FETCH_NUM);
    }
}
?>