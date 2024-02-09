<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Adapter;

use App\Core\Entity\Template\Body;
use App\Core\Database\Ready;

/**
 * An abstract adapter class to simplify access to the `Handler` class.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
abstract class HandlerAdapter
{
    /**
     * All core controllers.
     * @var object
     */
    protected object|array $app;

    protected Body $body;

    /**
     * Loading prepared queries to the database.
     * @var 
     */
    private ?Ready $ready = null;

    final public function __construct(object|array $core)
    {
        $this->app = $core;
        $this->body = new Body($core);
    }

    /**
     * Provides simplified access to controllers.
     * @param string $typeController
     * Type of controller for output.
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

}

?>