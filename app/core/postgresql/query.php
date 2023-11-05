<?php

namespace App\Core\PostgreSQL;

use App\Util;
use \PDO;

interface TQuery {
    public static function getRow(string $sql, array $prepare): mixed;
    public static function getRowAll(string $sql, array $prepare): array;
    public static function insertRow(string $table, array $prepare): bool;
    public static function updateRow(string $table, array $prepare, array|null $where = null): bool;
    public static function exec(string $sql, array|null $prepare = null): array|bool;
    public static function store(array $data): bool;

}

class Query extends \App\Core\PostgreSQL\Connect implements TQuery {

    /**
     * Public function getRow.
     * We get one result per query.
     * @param string $sql
     * @param array $prepare
     * @return mixed
     */
    public static function getRow(string $sql, array $prepare = []): mixed {

        $conn = self::get()->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute($prepare);

        $result = $stmt->fetch(PDO::FETCH_NUM);
        $stmt = null;
        $conn = null;

        return $result;
    }

    /**
     * Public function getRowAll.
     * Get all results for a query or a limited number of rows using a 'LIMIT' sentence.
     * @param string $sql
     * @param array $prepare
     * @return array
     */
    public static function getRowAll(string $sql, array $prepare = []): array {
        $conn = self::get()->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute($prepare);

        $result = $stmt->fetchAll(PDO::FETCH_NUM);
        $stmt = null;
        $conn = null;
        
        return $result;
    }

    /**
     * Public function insertRow.
     * Inserts new rows into the table.
     * @param string $table
     * @param array $prepare
     * @return bool
     */
    public static function insertRow(string $table, array $prepare): bool {
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
     * Public function updateRow
     * Updates the column values for the rows corresponding to the predicate. If no predicate is specified, column values for all rows are updated.
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

    /**
     * Public function exec.
     * Creates a result set.
     * @param string $sql
     * @param array|null $prepare
     * @return array|bool
     */
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

    /**
     * Summary of store
     * Executes a command line or character string.
     * @param array $data
     * @return bool
     */
    public static function store(array $data): bool {
        if($data['stmt']->execute($data['prepare'])) {
            $data['stmt'] = null;
            return true;
        }

        return false;
    }
}
?>