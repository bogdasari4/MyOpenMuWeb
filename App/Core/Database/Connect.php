<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database;

use App\Util;
use \PDO;

/**
 * A class for connecting to a database via `\PDO`, using a constant with a type key `__CONFIG_DEFAULT_DATABASE`.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
class Connect
{
    protected PDO $pdo;

    public function __construct()
    {
        $config['cdb'] = Util::config('cdb');
        $connectString = match (__CONFIG_DEFAULT_DATABASE) {
            'postgresql' => 'pgsql:host=' . $config['cdb']['host'] . ' port=' . $config['cdb']['port'] . ' dbname=' . $config['cdb']['dbname'],
            'mssql' => 'sqlsrv:Server=' . $config['cdb']['host'] . ', ' . $config['cdb']['host'] . ';Database=' . $config['cdb']['dbname']
        };

        $this->pdo = new PDO($connectString, $config['cdb']['user'], $config['cdb']['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}

?>