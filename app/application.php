<?php
    error_reporting(E_ALL);
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

    if(!@require_once('packets/autoload.php')) throw new Exception('5');

    if(!@require_once('util.php')) throw new Exception('4');
    if(!@\App\Util::getLanguage()) throw new Exception('z');



    if(!@include_once('core/handler.php')) throw new Exception('1');
    if(!@include_once('core/postgresql/database.php')) throw new Exception('2');
    if(!@include_once(__ROOT . 'app/core/template/main.php')) throw new Exception('');
    
    $Handler = new App\Core\Handler;
    $Handler->renderPage();
?>