<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Controller;

use App\Util;

/**
 * Main controller for storing and declaring all the main constants.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class ConstantController
{
    /**
     * An array of system constants.
     * @var array
     */
    private array $map = [
        '__ROOT' => [
            'APP' => [
                'CACHE' => __ROOT . 'App/Cache/',
                'EXT' => __ROOT . 'App/Extensions/',
                'JSON' => [
                    'CONF' => __ROOT . 'App/Json/Config/',
                    'LANG' => __ROOT . 'App/Json/Language/'
                ],
                'PAGE' => __ROOT . 'App/Pages/'
            ],
            'TEMP' => __ROOT . 'Templates/'
        ]
    ];

    public function __construct()
    {
        $this->map['__CONFIG'] = Util::config('core');
        $this->defineConstant($this->map);
        
        $this->add(['ACTIVE' => __ROOT_TEMP . __CONFIG_DEFAULT_TEMPLATE . DIRECTORY_SEPARATOR], '__ROOT_TEMP');
        $this->add(['PAGES' => __ROOT_TEMP_ACTIVE . 'Pages/'], '__ROOT_TEMP_ACTIVE');
    }

    /**
     * The function declares a new constant taking an array of values. Where the key is the name of the constant.
     * @param array $defineData
     * Array of constants.
     * @param string $desiredKey
     * Inherit a constant. 
     * For example, if the constant is `TEST`, inheritance is `_THIS_IS`, we get `_THIS_IS_TEST`.
     * @return void
     */
    public function add(array $defineData, string $desiredKey = ''): void
    {
        $this->defineConstant($defineData, $desiredKey);
    }

    private function defineConstant(array $defineData, string $desiredKey = ''): void
    {
        foreach ($defineData as $key => $path) {
            if (is_array($path)) {
                $this->DefineConstant($path, $desiredKey ? implode('_', [$desiredKey, $key]) : $key);
                continue;
            }

            if (!defined(implode('_', [$desiredKey, $key]))) {
                define(implode('_', [$desiredKey, $key]), str_replace('/', DIRECTORY_SEPARATOR, $path));
            }
        }
    }

}

?>