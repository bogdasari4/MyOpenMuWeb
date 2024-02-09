<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database\Interface;

/**
 * A class with prepared query types.
 */
interface InterfaceQuery
{
    /**
     * getRow (SELECT, TABLE, WITH) retrieve row from a table or view.
     */
    public function getRow(string $sql, array $prepare = []): mixed;

    /**
     * getRowAll (SELECT, TABLE, WITH) retrieve rows from a table or view.
     */
    public function getRowAll(string $sql, array $prepare = []): mixed;

    /**
     * insertRow (INSERT) inserts new rows into a table.
     * One can insert one or more rows specified by value expressions,
     * or zero or more rows resulting from a query.
     */
    public function insertRow(string $table, array|object $prepare): bool;

    /**
     * Updates the column values for the rows corresponding to the predicate.
     * If no predicate is specified, column values for all rows are updated.
     */
    public function updateRow(string $table, array $prepare, array|null $where = null): bool;

    /**
     * An arbitrary request to the database, without output of the result.
     */
    public function exec(string $sql, array|null $prepare = null): array|bool;

    /**
     * Execute an `exec` request.
     */
    public function store(array $data): bool;
}
?>