<?php

namespace App;

use Exception;

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
     * @param string $file
     * @param bool $associative
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
     * @param array $config
     * @return array
     */
    public static function parseStatusServer(array $parse): array {

        foreach($parse as $key => $value) {
            
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
     * @param int $time
     * @return void
     */
    public static function redirect(string $page = '/', int $time = 0): void {
        if($time) {
            header('Refresh:' . $time . '; url=' . $page);
        }

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
}