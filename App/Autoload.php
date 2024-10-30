<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 * 
 * Autoloading Classes
 * @see https://www.php.net/manual/language.oop5.autoload.php
 */
final class Autoload
{
    public static function Register(): void
    {   
        /**
         * The spl_autoload_register() function registers any number of autoloaders, enabling for classes and interfaces to be automatically loaded if 
         * they are currently not defined. By registering autoloaders, PHP is given a last chance to load the class or interface before it fails with an error.
         * 
         * @see https://www.php.net/manual/function.spl-autoload-register.php
         */
        spl_autoload_register(function ($class): void {
            $file_path = __ROOT . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists($file_path))
                require $file_path;
        });
    }
}
