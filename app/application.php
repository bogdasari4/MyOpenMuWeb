<?php
    /**
     * Sets which PHP errors are reported.
     * 
     * Set '0' to turn off all error reporting.
     * Set 'E_ALL & ~E_NOTICE' to report all errors except E_NOTICE.
     * Set 'E_ALL' to report all PHP errors.
     */
    error_reporting(0);

    @ini_set('default_charset', 'utf-8');
    @ob_start();
    session_start();

    define('__HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI');
    define('__SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://');
    define('__ROOT', str_replace('\\','/', dirname(dirname(__FILE__))).'/');
    define('__RELATIVE_ROOT', (!empty($_SERVER['SCRIPT_NAME'])) ? str_ireplace(rtrim(str_replace('\\','/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT) : '/');
    define('__BASE_URL', __SERVER_PROTOCOL . __HTTP_HOST . __RELATIVE_ROOT);
    define('__BASE_URL_ALT', __SERVER_PROTOCOL . __HTTP_HOST);
    define('__ACTUAL_URL', __SERVER_PROTOCOL . __HTTP_HOST . $_SERVER['REQUEST_URI']);


    if(!@require_once(__ROOT . 'app/packets/autoload.php')) throw new Exception('The autoload file used by composer could not be loaded.');
    if(!@include_once(__ROOT . 'app/util.php')) throw new Exception('Failed to load Util class.');

    define('__CONFIG', App\Util::config('core'));

    if(!@include_once(__ROOT . 'app/core/handler.php')) throw new Exception('The page handler file failed to execute.');

    $Handler = new App\Core\Handler;
    if(!$Handler->getLanguage()) throw new Exception('Failed to load language pack.');

    /**
     * Sets the default time zone for all date/time functions in the script.
     * https://www.php.net/manual/ru/timezones.php
     */
    date_default_timezone_set(__CONFIG['other']['default_timezone']);

    if(!@include_once(__ROOT . 'app/core/postgresql/database.php')) throw new Exception(__LANG['exception']['application']['database']);
    if(!@include_once(__ROOT . 'app/core/template/main.php')) throw new Exception(__LANG['exception']['application']['main']);
    if(!@include_once(__ROOT . 'app/core/auth/controller.php')) throw new Exception(__LANG['exception']['application']['auth']);

    $Handler->renderPage();
    
?>