<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Adapter;

use App\Core\Session;
use App\Core\Cache;

use App\Core\Database\PostgreSQL\ReadyStrings;
use App\Core\Auth;

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
abstract class PageAdapter implements InterfacePageAdapter
{
    /**
     * Loading sessions.
     * @var Session
     */
    protected Session $session;

    /**
     * Loading prepared queries to the database.
     * @var 
     */
    private ?object $ready = null;

    /**
     * Loading system cache.
     * @var 
     */
    private ?Cache $cache = null;

    /**
     * Loading helper classes to control authorization and verify user data.
     * @var 
     */
    private ?Auth $auth = null;

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
     * Ready function.
     * It is only used when you declare it.
     * @return Ready
     */
    final protected function ready()
    {
        if (is_null($this->ready)) {
            $this->ready = new ReadyStrings;
        }

        return $this->ready;
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

    /**
     * Auth function.
     * It is only used when you declare it.
     * @return Auth
     */
    final protected function auth(): Auth
    {
        if (is_null($this->auth)) {
            $this->auth = new Auth;
        }

        return $this->auth;
    }
}