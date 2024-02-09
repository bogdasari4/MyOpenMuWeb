<?php
class App
{
    public function getLoader()
    {
        /**
         * Sets which PHP errors are reported.
         * 
         * Set '0' to turn off all error reporting.
         * Set 'E_ALL & ~E_NOTICE' to report all errors except E_NOTICE.
         * Set 'E_ALL' to report all PHP errors.
         */
        error_reporting(E_ALL);

        @ini_set('default_charset', 'utf-8');
        @ob_start();

        define('__HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI');
        define('__SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://');
        define('__ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/');
        define('__RELATIVE_ROOT', (!empty($_SERVER['SCRIPT_NAME'])) ? str_ireplace(rtrim(str_replace('\\', '/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT) : '/');
        define('__BASE_URL', __SERVER_PROTOCOL . __HTTP_HOST . __RELATIVE_ROOT);
        define('__BASE_URL_ALT', __SERVER_PROTOCOL . __HTTP_HOST);
        define('__ACTUAL_URL', __SERVER_PROTOCOL . __HTTP_HOST . $_SERVER['REQUEST_URI']);

        $classMap = [
            null => 'App/Packets/autoload.php',
            '\\App\\Alert' => 'App/Alert.php',
            '\\App\\Assistant' => 'App/Assistant.php',
            '\\App\\Util' => 'App/Util.php',
            '\\App\\Core' => 'App/Core/Core.php'
        ];

        foreach ($classMap as $namespace => $file) {
            if (file_exists($file)) {
                require_once($file);
                if($namespace == '\\App\\Core') {
                    new $namespace;
                }
            }
        }
    }
}
?>