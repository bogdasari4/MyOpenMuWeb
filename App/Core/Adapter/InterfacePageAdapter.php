<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Adapter;

/**
 * Generic page adapter.
 * Creates a standard template for creating new or existing pages.
 * Provides access to `Session`, `Cache`, `Auth`, `Ready`, `Controller`,
 * as well as the configuration of the current country to which the adapter is connected
 * (if it exists, otherwise it will return an empty result).
 */
interface InterfacePageAdapter
{
    /**
     * Sets the standard for page creation.
     * `getInfo()` is a required function, it is used to load all page data for rendering.
     */
    public function getInfo(): array;
}