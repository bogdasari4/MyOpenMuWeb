<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Component;

/**
 * A v4 UUID contains a 122-bit random number.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
trait Uuid
{
    /**
     * A v4 UUID contains a 122-bit random number.
     * @return string
     */
    private function uuid_generateV4(): string
    {
        $uuid = random_bytes(16);
        $uuid[6] = $uuid[6] & "\x0F" | "\x40";
        $uuid[8] = $uuid[8] & "\x3F" | "\x80";
        $uuid = bin2hex($uuid);

        return substr($uuid, 0, 8).'-'.substr($uuid, 8, 4).'-'.substr($uuid, 12, 4).'-'.substr($uuid, 16, 4).'-'.substr($uuid, 20, 12);
    }

    /**
     * Check if the string matches the expression `uuid`.
     * @param string $uuid
     * The string `uuid`.
     * @return bool
     */
    private function uuid_isValid(string $uuid): bool
    {
        if (preg_match('{^[0-9a-f]{8}(?:-[0-9a-f]{4}){2}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$}Di', $uuid)) 
            return true;
        return false;
        
    }
}