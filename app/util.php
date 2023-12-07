<?php

namespace App\Interface;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

interface Util {
    public static function trimSChars(string $string, int|null $flag = 0): string;
    public static function config(string $file = 'core', bool $associative = true): array|object;
    public static function parseStatusServer(array $parse): bool;
    public static function redirect(string $page = '/', int $time = 0): void;
    public static function binaryToImageGuildLogo(string $binary, int $size = 40): string;
    public static function cache(string $subdir = '', int $expires = 0): FilesystemAdapter;
    public static function parseAPIServer(): array|bool;

}

namespace App;

use Exception;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Util implements \App\Interface\Util {

    /**
     * Public static function: trimSChars
     * Static function, converts special characters to HTML entities and preserves spaces (or other characters) at the beginning and end of the string.
     * @param string $string
     * String.
     * 
     * @param int $optional
     * A bitmask of one or more of the following flags, which specify how to handle quotes, 
     * invalid code unit sequences and the used document type. The default is ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401.
     * https://www.php.net/manual/en/function.htmlspecialchars.php
     * 
     * @return string
     */
    public static function trimSChars(string $string, int|null $flag = 0): string {
        $string = htmlspecialchars($string, $flag);
        $string = trim($string);

        return $string;
    }

 
    /**
     * Public static function: config
     * Reads the file configuration in the transform and converts it into an array of data.
     * @param string $file
     * Specify $file to open the desired configuration file (cdb, core, head, body, openmu)
     * 
     * @param bool $associative
     * If $associative is set to true, JSON objects will be returned as associative arrays; if false, JSON objects will be returned as objects.
     * 
     * @throws \Exception
     * @return array|object
     */
    public static function config(string $file = 'core', bool $associative = true): array|object {
        $configDir = __ROOT . 'app/json/config/' . $file . '.json';
        if(!@file_exists($configDir)) throw new Exception(sprintf('Cannot find %s configuration file.', $file));

        $config = json_decode(file_get_contents($configDir), $associative);
        return $config;
    }

    /**
     * Summary of parseStatusServer
     * @param array $parse
     * @return bool
     */
    public static function parseStatusServer(array $parse): bool {
        $fp = @fsockopen ($parse['host'], $parse['port'], $errno, $errstr, $parse['timeout']);
        if($fp) {
            fclose($fp);
            return true;
        }

        return false;
    }

    /**
     * Public static function: redirect
     * @param string $page
     * Redirect to the specified page.
     * 
     * @param int $time
     * When specifying $time greater than 0, redirection to the page is triggered after $time seconds.
     * 
     * @return void
     */
    public static function redirect(string $page = '/', int $time = 0): void {
        if($time) {
            header('Refresh:' . $time . '; url=' . $page);
        }

        header('Location:' . $page);
    }

    /**
     * Public static function: binaryToImageGuildLogo
     * @param string $binary
     * A binary string containing the guild logo.

     * @param int $size
     * Proportional image size.
     * 
     * @return string
     * Returns an html <img> tag with an image of the guild.
     */
    public static function binaryToImageGuildLogo(string $binary, int $size = 40): string {
        return '<img src="/api/guildmark.php?data=' . $binary . '&size=' . urlencode($size) . '" width="' . $size . '" height="' . $size .'"/>';
    }

    /**
     * Public static function: cache
     * @param string $subdir
     * A string used as the subdirectory of the root cache directory, 
     * where cache items will be stored.
     * 
     * @param int $expires
     * The default lifetime (in seconds) for cache items that do not define their own lifetime, 
     * with a value 0 causing items to be stored indefinitely (i.e. until the files are deleted).
     * 
     * @return FilesystemAdapter
     * Stores the cache item expiration and content as regular files in a collection of directories on a locally mounted filesystem.
     * https://symfony.com/doc/current/components/cache/adapters/filesystem_adapter.html
     */
    public static function cache(string $subdir = '', int $expires = 0): FilesystemAdapter {

        /**
         * @var string
         * The main cache directory (the application needs read-write permissions on it),
         * if none is specified, a directory is created inside the system temporary directory.
         */
        (string) $directory = __ROOT . 'app/cache/';

        return new FilesystemAdapter($subdir, $expires, $directory);
    }

    /**
     * Public static function: parseAPIServer
     * @return array|bool
     * Uses a modified api `http://localhost/api/status` to parse detailed information about servers.
     * More information and example: 
     * https://github.com/bogdasari4/MyOpenMuWeb/tree/main#api-modification-example
     */
    public static function parseAPIServer(): array|bool {
        $config['body'] = self::config('body');
        if($apiString = file_get_contents($config['body']['block']['serverInfo']['api']['url'])) {
            $data = json_decode($apiString, true);
            return $data;
        }

        return false;
    }
}