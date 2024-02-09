<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Adapter;

use App\Core\Adapter\Interface\InterfacePage;
use App\Core\Cache;
use App\Core\Database\Ready;
use App\Core\Entity\Auth;
use App\Core\Session;

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
abstract class PageAdapter implements InterfacePage
{
    /**
     * All core controllers.
     * @var object
     */
    private object $app;

    /**
     * Configuration of the connected module or page.
     * @var 
     */
    protected ?array $config;

    /**
     * Loading sessions.
     * @var Session
     */
    protected Session $session;

    /**
     * Loading prepared queries to the database.
     * @var 
     */
    private ?Ready $ready = null;

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
    final public function __construct(object $core, ?array $config)
    {
        $this->app = $core;
        $this->config = $config;
        $this->session = new Session;
    }

    /**
     * Controller function.
     * It is only used when you declare it.
     * Makes it easier to access controllers.
     * @param string $typeController
     * Controller type.
     * @return null|object
     */
    final protected function controller(string $typeController): ?object
    {
        if (isset($this->app->core['controller'][$typeController])) {
            return $this->app->core['controller'][$typeController];
        }

        return null;
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
    final protected function ready(): Ready
    {
        if (is_null($this->ready)) {
            $this->ready = new Ready;
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