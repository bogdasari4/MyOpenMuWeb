<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Template;

use App\Core\Component\ConfigLoader;
use App\Core\Database\ReadyQueries;
use App\Core\{Session, Cache};

/**
 * An auxiliary class for working with blocks and the template body.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
abstract class AbstractTemplate
{
    use ConfigLoader;

    protected object $session;
    private ?object $cache = null;
    readonly protected array $config;

    public function __construct(protected object $app)
    {
        $this->session = new Session;
        $this->session->start();

        $this->config = [
            'body' => $this->configLoader('body')
        ];
    }

    /**
     * Ready function.
     * It is only used when you declare it.
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
    final protected function cache(array $config, bool $separate = false): Cache
    {
        if(is_null($this->cache) || $separate) {
            $this->cache = new Cache($config);
        }

        return $this->cache;
    }
}