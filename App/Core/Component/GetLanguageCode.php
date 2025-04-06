<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Component;

/**
  * @author Bogdan Reva <tip-bodya@yandex.com>
  */
trait GetLanguageCode
{
    /**
     * We get the code of the selected language.
     * If the language is not selected, we use `__CONFIG_LANGUAGE_SET`.
     * @return int
     */
    private function getLanguageCode(): int
    {
        return  $_COOKIE['LanguageCode'] ?? __CONFIG_LANGUAGE_SET;
    }
}