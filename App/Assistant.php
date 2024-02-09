<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App;

/**
 * The assistant class combines several functions into one.
 */
trait Assistant
{
    /**
     * Custom function checking and connecting a file to the engine with class checking.
     * @param mixed|null $namespace
     * Namespaces.
     * @param string $file
     * Full path to the file.
     * @return void
     */
    function requireFile(?string $namespace, string $file): void
    {
        $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
        if (file_exists($file)) {
            require_once($file);
            if (!is_null($namespace) && class_exists($namespace)) {
                
            }
        }
    }

    /**
     * Get class name by namespace.
     * @param string $namespace
     * Namespaces.
     * @return string
     * Return the class name.
     */
    function getClassName(string $namespace): string
    {
        if ($pos = strrpos($namespace, '\\')) {
            return substr($namespace, $pos + 1);
        }
        return $pos;
    }

    /**
     * We get the data from the global variable `$_GET`. Let's format it.
     * @param string $typePage
     * Page type. Currently there are three types: `page`, `subpage`, `request`.
     * @param int|string $pageName
     * We use the data in case of an empty `$typePage` value.
     * If `$pageName` is empty, we perform forwarding.
     * @param bool $strtolower
     * If `true`, convert the string to lowercase.
     * @return string
     * Return the formatted string.
     * @see https://www.php.net/manual/en/function.strtolower.php
     */
    function spotGET(string $typePage, int|string $pageName, bool $strtolower = true): string
    {
        if(isset($_GET[$typePage]) && $_GET[$typePage] != '') {
            $pageName = $_GET[$typePage];
            $pageName = $strtolower ? strtolower($pageName) : $pageName;
            $pageName = Util::trimSChars($pageName);
        }
        
        return $pageName;
    }
}
?>