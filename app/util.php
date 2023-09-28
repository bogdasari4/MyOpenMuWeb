<?php

namespace App;

class Util {

    /**
     * Summary of trimSChars
     * @param string $string
     * @param int $optional
     * @return string
     */
    public static function trimSChars(string $string, int|null $flag = 0): string {
        $string = htmlspecialchars($string, $flag);
        $string = trim($string);

        return $string;
    }

    /**
     * Summary of config
     * @param string|null $key
     * @param bool|null $associative
     * @return mixed
     */
    public static function config(string|null $key = null, bool|null $associative = true): mixed {
        $config = json_decode(file_get_contents(__ROOT . 'app/json/config.json'), $associative);

        if($key == null) return $config;
        return $config[$key];
    }

    /**
     * Summary of readCache
     * @param array $config
     * @return array|bool
     */
    public static function readCache(array $config): array|bool {
        if(file_exists(__ROOT . 'app/cache/' . $config['name'])) {

            $result = [];
            $cache = explode('#', file_get_contents(__ROOT . 'app/cache/' . $config['name']));

            foreach($cache as $row) {
                $result[] = explode('|', $row);
            }
        }

        $result[0][0] = isset($result[0][0]) ? $result[0][0] : 0;
        return $result;
    }

    /**
     * Summary of writeCache
     * @param array $config
     * @param array $data
     * @return bool
     */
    public static function writeCache(array $config, array $data): bool {

        $cache = time() . '#';
        if($data != null) {

            $countData = count($data);
            $i = 0;
            foreach($data as $row) {
                $cache .= implode('|', $row);
                if(++$i !== $countData) {
                    $cache .= '#';
                }
            }
        }
        
        if(file_put_contents(__ROOT . 'app/cache/' . $config['name'], $cache)) {
            return true;
        }

        return false;
    }

    /**
     * Summary of parseStatusServer
     * @return array
     */
    public static function parseStatusServer() {

        $config = self::config('body');

        foreach($config['block']['serverInfo']['parse'] as $key => $value) {
            
            $data[$key] = [0];
            $fp = @fsockopen ($value['host'], $value['port'], $errno, $errstr, $value['timeout']);
            if($fp) {
                $data[$key] = [1];
                fclose($fp);
            }
        }

        return $data;
    }

    /**
     * Summary of redirect
     * @param string $page
     * @return void
     */
    public static function redirect(string $page = '/'): void {
        header('Location:' . $page);
    }

    /**
     * Summary of binaryToImageGuildLogo
     * @param string $binary
     * @param int $size
     * @return string
     */
    public static function binaryToImageGuildLogo(string $binary, int $size = 40): string {
        return '<img src="/api/guildmark.php?data=' . $binary . '&size=' . urlencode($size) . '" width="' . $size . '" height="' . $size .'"/>';
    }

    /**
     * Summary of getLanguage
     * @param int $code
     * @return array|bool
     */
    public static function getLanguage(int $code = 0): bool {
        if(!$code) {
            if(isset($_COOKIE['language_code']) || $_COOKIE['language_code'] == '') {
                $config = self::config('core');
                setcookie('language_code', $config['langugage']['default'], time() + $config['langugage']['expires'], '/');
                $code = $config['langugage']['default'];
            } else {
                $code = $_COOKIE['language_code'];
            }
        }

        $langFile = __ROOT . 'app/json/language/' . $code . '/main.json';
        if(file_exists($langFile)) {
            $lang = json_decode(file_get_contents($langFile), true);
            if($lang) {
                define('__LANG', $lang);
                return true;
            }
        }

        return false;
    }
}