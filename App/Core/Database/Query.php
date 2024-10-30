<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database;

use App\Core\Database\Connect;
use App\Util;
use \PDO;

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Query extends Connect implements InterfaceQuery
{
    /**
     * @param string $sql
     * Ready `SQL` query string.
     * @param array $prepare
     * They use array or object data, where the key is the name of the column to which the value is passed.
     * @return mixed
     */
    public function getRow(string $sql, array $prepare = []): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($prepare);

        $result = $stmt->fetch(PDO::FETCH_NUM);
        $stmt = null;

        return $result;
    }

    /**
     * @param string $sql
     * Ready `SQL` query string.
     * @param array $prepare
     * They use array or object data, where the key is the name of the column to which the value is passed.
     * @return mixed
     */
    public function getRowAll(string $sql, array $prepare = []): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($prepare);

        $result = $stmt->fetchAll(PDO::FETCH_NUM);

        $stmt = null;

        return $result;
    }

    /**
     * @param string $table
     * The name (optionally schema-qualified) of an existing table.
     * @param array $prepare
     * They use array or object data, where the key is the name of the column to which the value is passed.
     * @return bool
     * @see https://www.postgresql.org/docs/current/sql-insert.html
     */
    public function insertRow(string $table, array|object $prepare): bool
    {
        foreach ($prepare as $key => $vlaue) {
            $key = Util::trimSChars($key);
            $columns[] = '"' . $key . '"';
            $key = strtolower($key);
            $keys[] = ':' . $key;
            $data[$key] = Util::trimSChars($vlaue);
        }

        $stmt = $this->pdo->prepare('INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $keys) . ')');
        if ($stmt->execute($data)) {
            $stmt = null;
            return true;
        }
        return false;
    }

    /**
     * @param string $table
     * The name (optionally schema-qualified) of an existing table.
     * @param array $prepare
     * They use array or object data, where the key is the name of the column to which the value is passed.
     * @param array|null $where
     * The WHERE clause is used to filter records.
     * They use array or object data,
     * where the key is the name of the column to which the value is passed.
     * @return bool
     */
    public function updateRow(string $table, array $prepare, array|null $where = null): bool
    {
        foreach ($prepare as $key => $vlaue) {
            $columns[] = '"' . $key . '"=:' . strtolower($key);
            $data[strtolower($key)] = $vlaue;
        }

        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $columns);
        if ($where != null) {
            $columns = [];
            foreach ($where as $key => $vlaue) {
                $columns[] = '"' . $key . '"=:' . strtolower($key);
                $data[strtolower($key)] = $vlaue;
            }
            $sql .= ' WHERE ' . implode(' AND ', $columns);
        }

        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute($data)) {
            $stmt = null;
            return true;
        }
        return false;
    }

    /**
     * @param string $sql
     * Ready `SQL` query string.
     * @param array|null $prepare
     * They use array or object data, where the key is the name of the column to which the value is passed.
     * @return array|bool
     */
    public function exec(string $sql, array|null $prepare = null): array|bool
    {
        if ($data['stmt'] = $this->pdo->prepare($sql)) {
            if ($prepare != null) {
                $data['prepare'] = $prepare;
            }
            return $data;
        }

        return false;
    }

    /**
     * @param array $data
     * Prepared request data.
     * @return bool
     */
    public function store(array $data): bool
    {
        if ($data['stmt']->execute($data['prepare'])) {
            $data['stmt'] = null;
            return true;
        }

        return false;
    }
}
?>