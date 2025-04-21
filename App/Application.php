<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 * 
 * The initial class of the engine
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */

final class App
{
    /**
     * We specify access constants.
     */
    public const ACCESS_INDEX = 'index';

    public const ACCESS_API = 'api';
    
    public const ACCESS_INSTALL = 'install';

    /**
     * The initial class of the engine. We declare the first constants and set the php settings.
     * @param string $access
     * Access type: example [ACCESS_INDEX], [ACCESS_API], [ACCESS_INSTALL]
     * @author Bogdan Reva <tip-bodya@yandex.com>
     */
    public function loader(string $access = self::ACCESS_INDEX)
    {
        /**
         * Sets which PHP errors are reported.
         * 
         * Set '0' to turn off all error reporting.
         * Set 'E_ALL & ~E_NOTICE' to report all errors except E_NOTICE.
         * Set 'E_ALL' to report all PHP errors.
         * 
         * @see https://www.php.net/manual/function.error-reporting.php
         */
        error_reporting(E_ALL);

        /**
         * @see https://www.php.net/manual/function.ini-set.php
         */
        @ini_set('default_charset', 'utf-8');

        /**
         * @see https://www.php.net/manual/function.ob-start.php
         */
        @ob_start();

        define('__HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI');
        define('__SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://');
        define('__ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/');
        define('__RELATIVE_ROOT', (!empty($_SERVER['SCRIPT_NAME'])) ? str_ireplace(rtrim(str_replace('\\', '/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT) : '/');
        define('__BASE_URL', __SERVER_PROTOCOL . __HTTP_HOST . __RELATIVE_ROOT);
        define('__BASE_URL_ALT', __SERVER_PROTOCOL . __HTTP_HOST);
        define('__ACTUAL_URL', __SERVER_PROTOCOL . __HTTP_HOST . $_SERVER['REQUEST_URI']);

        /**
         * This class using for autoload classes from file paths.
         * 
         * @see https://www.php-fig.org/psr/psr-4/
         */
        if(!@include_once(__ROOT . 'App/Autoload.php'))
            throw new \Exception('Can\'t find engine autoload file.');

        Autoload::Register();

        /**
         * Switch the handler class to render the site template.
         */
        $Handler = new App\Core\Handler();
        $Handler->switch($access);
    }
}
?>