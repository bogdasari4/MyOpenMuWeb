<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App;

use Exception;

/**
 * A class with utilities that are used throughout the engine.
 * The operation of each function is described in detail below.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
class Util
{
    /**
     * Static function, converts special characters to HTML entities and preserves spaces (or other characters) at the beginning and end of the string.
     * @param string $string
     * String to convert.
     * @param int $flag
     * A bitmask of one or more of the following flags, which specify how to handle quotes, 
     * invalid code unit sequences and the used document type. The default is ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401.
     * @return string
     * Returns the converted string.
     * @see https://www.php.net/manual/en/function.trim.php, https://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function trimSChars(?string $string, int $flag = 0): ?string
    {
        if ($string === null)
            return null;
        
        $string = htmlspecialchars($string, $flag);
        $string = trim($string);

        return $string;
    }


    /**
     * Reads the file configuration in the transform and converts it into an array of data.
     * @param string $file
     * Specify $file to open the desired configuration file (cdb, core, head, body, openmu)
     * @param bool $associative
     * If `$associative` is set to `true`, JSON objects will be returned as associative arrays,
     * if `false` JSON objects will be returned as objects.
     * @throws \Exception
     * @return array|object
     */
    public static function config(string $file = 'core', bool $associative = true): array|object|bool
    {
        $configDir = __ROOT . 'App/Json/Config/' . $file . '.json';
        if (!@file_exists($configDir)) return false;
            throw new Exception(sprintf('Cannot find %s configuration file.', $file));

        $config = json_decode(file_get_contents($configDir), $associative);
        return $config;
    }

    /**
     * A function for opening a connection to an Internet socket and checking the status of the connection to the server.
     * Not recommended as it slows down the web server.
     * @param array $parse
     * Receives data to open a connection.
     * @return bool
     * If the connection is successful, we get `true`, otherwise `false`.
     */
    public static function parseStatusServer(array $parse): bool
    {
        $fp = @fsockopen($parse['host'], $parse['port'], $errno, $errstr, $parse['timeout']);
        if ($fp) {
            fclose($fp);
            return true;
        }

        return false;
    }

    /**
     * Page redirection function with delay option.
     * @param string $page
     * Redirect to the specified page.
     * @param int $delay
     * When specifying `$delay` greater than 0, redirection to the page is triggered after $time seconds.
     * @return void
     */
    public static function redirect(string $page = '/', int $delay = 0): never
    {
        if ($delay > 0) {
            header('Refresh:' . $delay . '; url=' . $page);
            exit;
        }

        header('Location:' . $page);
        exit;
    }

    /**
     * Function to get `html` link to render guild logo.
     * @param string $binary
     * A binary string containing the guild logo.
     * @param int $size
     * Proportional image size.
     * @return string
     * Returns an `html` <img> tag with an image of the guild.
     */
    public static function binaryToImageGuildLogo(string $binary, int $size = 40): string
    {
        return '<img src="/api/guildmark.php?data=' . $binary . '&size=' . urlencode($size) . '" width="' . $size . '" height="' . $size . '"/>';
    }

    /**
     * Uses a modified api `http://localhost/api/status` to parse detailed information about servers.
     * @param string|null $urlLine
     * Takes a URL string to read the server state. If the value is `null`, we get the serverInfo block configuration file.
     * @return array|bool
     * Get a data array. If it is impossible to read, get `false`
     * @see https://github.com/bogdasari4/MyOpenMuWeb/tree/main#api-modification-example
     * More information and example.
     */
    public static function parseAPIServer(?string $urlLine = null): array|bool
    {
        if (is_null($urlLine)) {
            $config['body'] = self::config('body');
            $urlLine = $config['body']['block']['serverInfo']['api']['url'];
        }

        if ($urlLine != '') {
            $apiString = @file_get_contents(filter_var($urlLine, FILTER_SANITIZE_URL));
            if ($apiString !== false) {
                if (($data = json_decode($apiString, true)) !== null) {
                    return $data;
                }
            }
        }

        return false;
    }
}
