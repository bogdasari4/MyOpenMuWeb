<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core;

use App\Core\Auth\Validation;
/**
 * Generic `Auth` class, for compactness.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Auth
{
    /**
     * @return Validation
     */
    public function validation(): Validation
    {
        return new Validation;
    }
}