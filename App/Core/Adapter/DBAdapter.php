<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Adapter;

use App\Core\Database\QueryBuilder;

/**
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
class DBAdapter
{
    protected function queryBuilder(): QueryBuilder
    {
        return QueryBuilder::getInstance();
    }
}