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
    /**
     * @param object $app
     * Provides simplified access to controllers
     */
    final public function __construct(protected object $app)
    {
        $this->load();
    }

    /**
     * Required feature. Use it to load the extension.
     * @return void
     */
    abstract protected function load(): void;
}

?>