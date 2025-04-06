<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Component;

/**
 * Return the formatted string.
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
trait FormattedGet
{

    /**
     * We get the data from the global variable `$_GET`. Let's format it.
     * @param string $typeGet
     * Page type. Currently there are three types: `page`, `subpage`, `request`.
     * @param int|string $pageName
     * We use the data in case of an empty `$typePage` value.
     * If `$pageName` is empty, we perform forwarding.
     * @param bool $strtolower
     * If `true`, convert the string to lowercase.
     * @return string
     * Return the formatted string.
     * @see https://www.php.net/manual/function.strtolower.php, https://www.php.net/manual/function.htmlspecialchars.php, https://www.php.net/manual/function.trim.php
     */
    private function formattedGet(string $typeGet, int|string $pageName, bool $strtolower = true): string
    {   
        if(isset($_GET[$typeGet]) && (is_string($_GET[$typeGet]) || is_int($_GET[$typeGet])) && $_GET[$typeGet] !== '') {
            $filteredInput = htmlspecialchars(trim($_GET[$typeGet]), ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
            if(strlen($filteredInput) < 30)
                $pageName = $filteredInput;
        }

        return $strtolower ? strtolower($pageName) : $pageName;
    }
}
