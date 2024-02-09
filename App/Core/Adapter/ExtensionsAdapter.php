<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Adapter;

/**
 * Main abstract class for providing extension access to the controller.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
abstract class ExtensionsAdapter
{
    private object $app;

    final public function __construct(object $core)
    {
        $this->app = $core;
        $this->load();
    }

    /**
     * Required feature. Use it to load the extension.
     * @return void
     */
    abstract protected function load(): void;

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
}

?>