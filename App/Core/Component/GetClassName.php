<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Component;

/**
 * Get class name by namespace.
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
trait GetClassName
{
    /**
     * Get class name by namespace.
     * @param string $namespace
     * Namespaces.
     * @return string
     * Return the class name.
     */
    private function getClassName(string $namespace): string
    {
        if ($pos = strrpos($namespace, '\\')) {
            return substr($namespace, $pos + 1);
        }
        return $pos;
    }
}