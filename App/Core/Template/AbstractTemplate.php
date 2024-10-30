<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Template;

use App\Util;
use App\Core\Session;

use App\Core\Cache;
use App\Core\Database\PostgreSQL\ReadyStrings;

/**
 * An auxiliary class for working with blocks and the template body.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
abstract class AbstractTemplate
{
    protected object $session;
    private ?object $ready = null;
    private ?object $cache = null;
    readonly protected array $config;

    public function __construct(protected object $app)
    {
        $this->session = new Session;
        $this->session->start();

        $this->config = [
            'body' => Util::config('body')
        ];
    }

    /**
     * Ready function.
     * It is only used when you declare it.
     * @return ReadyStrings
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
    final protected function cache(array $config, bool $separate = false): Cache
    {
        if(is_null($this->cache) || $separate) {
            $this->cache = new Cache($config);
        }

        return $this->cache;
    }
}