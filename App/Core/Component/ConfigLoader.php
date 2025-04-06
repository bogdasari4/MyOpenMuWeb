<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Component;

/** 
 * Reads the file configuration in the transform and converts it into an array of data.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
trait ConfigLoader
{

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
    private function configLoader(string $file = 'core', bool $associative = true): array|object
    {
        $configDir = __ROOT . 'App/Json/Config/' . $file . '.json';
        
        if (!@file_exists($configDir))
            throw new \Exception(sprintf('Cannot find %s configuration file.', $file));

        if(($contents = file_get_contents($configDir)) === false || empty($contents))
            throw new \Exception(sprintf('Unable to read %s file, it may be empty.', $file));

        if(($configData = json_decode($contents, $associative)) === null || empty($configData && (is_array($configData) || is_object($configData))))
            throw new \Exception(sprintf('Unable to decode %s file, possibly getting empty result or result is not array or object.', $file));

        return $configData;
    }

}