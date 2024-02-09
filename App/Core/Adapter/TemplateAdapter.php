<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Adapter;

use App\Core\Cache;
use App\Core\Database\Ready;
use App\Util;
use App\Core\Session;

/**
 * An auxiliary class for working with blocks and the template body.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
abstract class TemplateAdapter
{
    private object $app;
    protected object $session;
    private ?object $ready = null;
    private ?object $cache = null;
    readonly protected array $config;

    public function __construct(object $core)
    {
        $this->app = $core;

        $this->session = new Session;
        $this->session->start();

        $this->config = [
            'body' => Util::config('body')
        ];
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
    final protected function cache(array $config, bool $separate = false): Cache
    {
        if(is_null($this->cache) || $separate) {
            $this->cache = new Cache($config);
        }

        return $this->cache;
    }
}