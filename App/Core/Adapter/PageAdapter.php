<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Adapter;

use App\Core\Database\ReadyQueries;
use App\Core\{Session, Cache};

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
abstract class PageAdapter extends DBAdapter implements InterfacePageAdapter
{
    /**
     * Loading sessions.
     * @var Session
     */
    protected Session $session;

    /**
     * Loading system cache.
     * @var 
     */
    private ?Cache $cache = null;

    /**
     * We transfer controllers, configuration and declare a session.
     * @param object $core
     * We pass controllers through a `$core` variable.
     * @param null|array $config
     * We pass the configuration through the `$config` variable.
     */
    public function __construct(protected object $app, protected ?array $config)
    {
        $this->session = new Session;
    }

    /**
     * @return array
     */
    abstract public function getInfo(): array;

    /**
     * readyQueries function.
     * Prepared queries to the database.
     * @return ReadyQueries
     */
    final protected function readyQueries(): ReadyQueries
    {
        return new ReadyQueries;
    }

    /**
     * Cache function.
     * It is only used when you declare it.
     * @return Cache
     */
    final protected function cache(?array $config = null): Cache
    {
        if (is_null($this->cache)) {
            $this->cache = new Cache($config ?? $this->config);
        }

        return $this->cache;
    }
}