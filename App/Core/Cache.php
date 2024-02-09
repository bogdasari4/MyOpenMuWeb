<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Cache management class.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Cache
{
    private  $FSAdapter;

    private array $config;

    /**
     * Stores the cache item expiration and content as regular files in a collection of directories on a locally mounted filesystem.
     * https://symfony.com/doc/current/components/cache/adapters/filesystem_adapter.html
     * @param array $config
     * `subdir` A string used as the subdirectory of the root cache directory, 
     * where cache items will be stored.
     * `lifetime` The default lifetime (in seconds) for cache items that do not define their own lifetime, 
     * with a value 0 causing items to be stored indefinitely (i.e. until the files are deleted).
     */
    public function __construct(array $config)
    {
        $default = ['subdir' => '', 'setName' => bin2hex(random_bytes(5)), 'lifetime' => __CONFIG_DEFAULT_CACHE_LIFETIME];

        foreach ($default as $key => $value) {
            if (!isset($config['cache'][$key]) || (isset($config['cache'][$key]) && $config['cache'][$key] == '')) {
                $config['cache'][$key] = $value;
            }
        }
        
        $this->config = $config;
        $config = null;

        $this->FSAdapter = new FilesystemAdapter($this->config['cache']['subdir'], __CONFIG_DEFAULT_CACHE_LIFETIME, __ROOT_APP_CACHE);
    }

    /**
     * Now you can retrieve and delete cached data using this object.
     * @param callable $callback
     * The first argument is a PHP callable which is executed when the key is not found in the cache to generate and return the value.
     * @return array
     * We return the data as an array.
     */
    public function get(callable $callback): mixed
    {
        return $this->FSAdapter->get($this->config['cache']['setName'], function (ItemInterface $item) use ($callback) {
            $item->expiresAfter($this->config['cache']['lifetime']);
            return $callback($this->config);
        });
    }

    /**
     * Get complete information on the key, if the key `$key` is not specified, 
     * find information on the declared key `$this->config['cache']['getName']` in __construct function.
     * https://symfony.com/doc/current/components/cache/cache_items.html
     * @param mixed $key
     * The declared key for searching the cache.
     * @return CacheItem
     * We return the data as an object.
     */
    public function getInfo(mixed $key = null): CacheItem
    {
        return $this->FSAdapter->getItem($key ?? $this->config['cache']['getName']);
    }

    /**
     * We save the values using `getInfo()`.
     * @param CacheItemInterface $item
     * Interface for interacting with objects inside a cache.
     * @return bool
     */
    public function save(CacheItemInterface $item): bool
    {
        return $this->FSAdapter->save($item);
    }
}

?>