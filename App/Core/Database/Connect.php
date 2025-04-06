<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database;

use App\Core\Component\ConfigLoader;

/**
 * And a class for connecting to the database.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
class Connect
{
    use ConfigLoader;

    /**
     * Save the resulting PDO object to the protected $pdo variable.
     */
    protected $pdo;

    /**
     * $instance stores a single instance of the class.
     */
    private static $instance = null;

    public function __construct()
    {
        $config = $this->configLoader('cdb', false);

        $dsnString = 'pgsql:host=' . $config->host . ' port=' . $config->port . ' dbname=' . $config->dbname;

        $this->pdo = new \PDO($dsnString, $config->user, $config->password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_NUM);
        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * Checks if an instance exists and creates it if it has not yet been created.
     * @return
     */
    public static function getInstance(): QueryBuilder
    {
        if (self::$instance === null)
            self::$instance = new QueryBuilder;

        return self::$instance;
    }

    /**
     * Disable object cloning
     * @return void
     */
    public function __clone(): void
    {
        throw new \Exception('Cloning the "Connect" object is prohibited by the kernel.');
    }

    /**
     * Prevents additional instances from being created via unserialize().
     * @return void
     */
    public function __wakeup(): void
    {
        throw new \Exception('Deserialization of object "Connect" is prohibited by the kernel.');
    }
}

?>