<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Alert;
use App\Core\Adapter\PageAdapter;
use App\Util;

/**
 * This page ends sessions, redirects to the main page and stops executing the current script.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class LogOut extends PageAdapter
{
    
    /**
     * The public function `getInfo()` provides data for rendering pages.
     * @return array
     * We return an array of data.
     */
    public function getInfo(): array
    {
        if (isset($this->session->user)) {
            if ($this->session->destruct())
                throw new Alert(0x287, 'success', '/');
        }

        Util::redirect();

        return [];
    }
}
