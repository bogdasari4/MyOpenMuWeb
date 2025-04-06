<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database;

use App\Core\Database\Connect;

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class QueryBuilder extends Connect implements InterfaceQueryBuilder
{

    /**
     * @param string $table
     * Table for work.
     * @param array $conditions
     * Сonditions for data sampling
     * @param  string $fields
     * The output fields we need are "*" (all) by default.
     * @return mixed
     */
    public function getRow(string $table, array $conditions = [], string $fields = '*'): mixed
    {
        $sql = 'SELECT ' . $fields . ' FROM ' . $table;
        $params = [];

        if(!empty($conditions)) {
            $where = [];
            foreach($conditions as $key => $value) {
                $where[] = sprintf('"%s" = :%s', $key, strtolower($key));
                $params[strtolower($key)] = $value;
            }

            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch();
    }

    /**
     * @param string $table
     * Table for work.
     * @param array $conditions
     * Сonditions for data sampling
     * @param  string $fields
     * The output fields we need are "*" (all) by default.
     * @param int $limit
     * Set the limit for data sampling. Default is "1".
     * @return mixed
     */
    public function getRowsAll(string $table, array $conditions = [], string $fields = '*', ?int $limit = null): mixed
    {
        $sql = 'SELECT ' . $fields . ' FROM ' . $table;
        $params = [];

        if(!empty($conditions)) {
            $where = [];
            foreach($conditions as $key => $value) {
                $where[] = sprintf('"%s" = :%s', $key, strtolower($key));
                $params[strtolower($key)] = $value;
            }

            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        if($limit !== null) {
            $sql .= ' LIMIT :limit';
            $params[':limit'] = $limit;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * @param string $table
     * The name (optionally schema-qualified) of an existing table.
     * @param array $params
     * They use array data, where the key is the name of the column to which the value is passed.
     * @return bool
     * @see https://www.postgresql.org/docs/current/sql-insert.html
     */
    public function insertRow(string $table, array $params): bool
    {
        $sql = 'INSERT INTO ' . $table . ' ("' . implode('", "', array_keys($params)) . '") VALUES (:' . implode(', :', array_keys($params)) . ')';
        print_r($sql);
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * @param string $table
     * The name (optionally schema-qualified) of an existing table.
     * @param array $params
     * They use array or object data, where the key is the name of the column to which the value is passed.
     * @param array|null $where
     * The WHERE clause is used to filter records.
     * They use array or object data,
     * where the key is the name of the column to which the value is passed.
     * @return bool
     */
    public function updateRow(string $table, array $params, array|null $where = null): bool
    {
        foreach($params as $key => $vlaue) {
            $columns[] = '"' . $key . '"=:' . strtolower($key);
            $data[strtolower($key)] = $vlaue;
        }

        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $columns);
        if($where !== null) {
            $columns = [];
            foreach($where as $key => $vlaue) {
                $columns[] = '"' . $key . '"=:' . strtolower($key);
                $data[strtolower($key)] = $vlaue;
            }
            $sql .= ' WHERE ' . implode(' AND ', $columns);
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * @param string $sql
     * Ready `SQL` query string.
     * @param array|null $params
     * They use array or object data, where the key is the name of the column to which the value is passed.
     * @param bool $returnRowsAffected
     * The false value indicates that the SELECT query is being used.
     * The value true indicates that the INSERT, UPDATE, DELETE query is to be used.
     * @param bool $fetchAll
     * Toggles fetchAll or fetch mode;
     * Warning! Only works when returnRowsAffected is false.
     * @return array|bool
     */
    public function exec(string $sql, array $params = [], bool $returnRowsAffected = false, bool $fetchAll = true): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if($returnRowsAffected)
            return $stmt->rowCount();

        if($fetchAll)
            return $stmt->fetchAll();

        return $stmt->fetch();
    }
}
?>