<?php

namespace App\Core\PostgreSQL;

use App\Util;
use \PDO;

class Query extends \App\Core\PostgreSQL\Connect {

    /**
     * Summary of getRow
     * @param string $sql
     * @param array $prepare
     * @return mixed
     */
    public static function getRow(string $sql, array $prepare):mixed {

        $conn = self::get()->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute($prepare);

        $result = $stmt->fetch(PDO::FETCH_NUM);
        $stmt = null;
        $conn = null;

        return $result;
    }

    /**
     * Summary of getRowAll
     * @param string $sql
     * @param array $prepare
     * @return array
     */
    public static function getRowAll(string $sql, array $prepare):array {
        $conn = self::get()->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute($prepare);

        $result = $stmt->fetchAll(PDO::FETCH_NUM);
        $stmt = null;
        $conn = null;
        
        return $result;
    }

    /**
     * Summary of insertRow
     * @param string $table
     * @param array $prepare
     * @return bool
     */
    public static function insertRow( string $table, array $prepare): bool {
        foreach($prepare as $key => $vlaue) {
            $key = Util::trimSChars($key);
            $columns[] = '"' . $key . '"';
            $key = strtolower($key);
            $keys[] = ':' . $key;
            $data[$key] = Util::trimSChars($vlaue);
        }

        $conn = self::get()->connect();
        $stmt = $conn->prepare('INSERT INTO ' . $table . ' (' . implode(', ', $columns) .') VALUES (' . implode(', ', $keys) . ')');
        if($stmt->execute($data)) {
            $stmt = null;
            return true;
        }
        return false;
    }

    /**
     * Summary of updateRow
     * @param string $table
     * @param array $prepare
     * @param array|null $where
     * @return bool
     */
    public static function updateRow(string $table, array $prepare, array|null $where = null): bool {
        foreach($prepare as $key => $vlaue) {
            $columns[] = '"' . $key . '"=:' . strtolower($key);
            $data[strtolower($key)] = $vlaue;
        }

        $conn = self::get()->connect();
        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $columns);
        if($where != null) {
            $columns = [];
            foreach($where as $key => $vlaue) {
                $columns[] = '"' . $key . '"=:' . strtolower($key);
                $data[strtolower($key)] = $vlaue;
            }
            $sql .= ' WHERE ' . implode(' AND ', $columns);
        }
        
        $stmt = $conn->prepare($sql);
        if($stmt->execute($data)) {
            $stmt = null;
            return true;
        }
        return false;
    }

    public static function exec(string $sql, array|null $prepare = null): array|bool {
        if($conn = self::get()->connect()) {

            if($data['stmt'] = $conn->prepare($sql)) {
                if($prepare != null) {
                    $data['prepare'] = $prepare;
                }
                return $data;
            }
            
        }

        return false;
    }

    public static function store(array $data): bool {
        if($data['stmt']->execute($data['prepare'])) {
            $data['stmt'] = null;
            return true;
        }

        return false;
    }
}
?>