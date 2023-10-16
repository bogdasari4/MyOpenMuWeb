<?php
    error_reporting(E_ALL);
    #error_reporting(E_ERROR | E_PARSE);
    date_default_timezone_set('Europe/Moscow');
    @ini_set('default_charset', 'utf-8');
    ob_start();

    define('__HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI');
    define('__SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://');
    define('__ROOT', str_replace('\\','/', dirname(dirname(__FILE__))).'/');
    define('__RELATIVE_ROOT', (!empty($_SERVER['SCRIPT_NAME'])) ? str_ireplace(rtrim(str_replace('\\','/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT) : '/');
    define('__BASE_URL', __SERVER_PROTOCOL.__HTTP_HOST.__RELATIVE_ROOT);
    define('__BASE_URL_ALT', __SERVER_PROTOCOL.__HTTP_HOST);
    define('__ACTUAL_URL', __SERVER_PROTOCOL.__HTTP_HOST.$_SERVER['REQUEST_URI']);


    if(!@require_once(__ROOT . 'app/packets/autoload.php')) throw new Exception('The autoload file used by composer could not be loaded.');

    session_start();

    if(!@include_once(__ROOT . 'app/util.php')) throw new Exception('Failed to load Util class.');

    if(!@include_once(__ROOT . 'app/core/handler.php')) throw new Exception('The page handler file failed to execute.');
    if(!@include_once(__ROOT . 'app/core/postgresql/database.php')) throw new Exception(__LANG['exception']['application']['database']);
    if(!@include_once(__ROOT . 'app/core/template/main.php')) throw new Exception(__LANG['exception']['application']['main']);
    if(!@include_once(__ROOT . 'app/core/auth/controller.php')) throw new Exception(__LANG['exception']['application']['auth']);

    $Handler = new App\Core\Handler;
    $Handler->renderPage();
    
?>