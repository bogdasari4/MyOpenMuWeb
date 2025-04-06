<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database;

/**
 * A class with prepared query types.
 */
interface InterfaceQueryBuilder
{
    /**
     * Returns the first matching value.
     * See the documentation for more details.
     * @see https://www.php.net/manual/pdostatement.fetch.php
     */
    public function getRow(string $table, array $conditions = [], string $fields = '*'): mixed;

    /**
     * Returns an array containing the rows that remain in the result set.
     * See the documentation for more details.
     * @see https://www.php.net/manual/pdostatement.fetchall.php
     */
    public function getRowsAll(string $sql, array $prepare = []): mixed;

    /**
     * insertRow (INSERT) inserts new rows into a table.
     * One can insert one or more rows specified by value expressions,
     * or zero or more rows resulting from a query.
     */
    public function insertRow(string $table, array $params): bool;

    /**
     * Updates the column values for the rows corresponding to the predicate.
     * If no predicate is specified, column values for all rows are updated.
     */
    public function updateRow(string $table, array $params, array|null $where = null): bool;

    /**
     * An arbitrary request to the database, without output of the result.
     */
    public function exec(string $sql, array $params = [], bool $returnRowsAffected = false): mixed;
}
?>