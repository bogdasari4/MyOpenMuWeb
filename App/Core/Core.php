<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App;

use App\Assistant;
use App\Util;

/**
 * The core of the `MyOpenMuWeb` engine. 
 * Used to includes and evaluates the specified file,
 * checks whether the specified class has been declared.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Core
{
    use Assistant {
        requireFile as private;
        getClassName as private;
    }

    private const INTERFACE_ADAPTER = __ROOT . 'App/Core/Adapter/Interface/';
    private const INTERFACE_DATABASE = __ROOT . 'App/Core/Database/Interface/';
    public object|array $core;

    public function __construct()
    {
        $this->loadCoreClass();

        $this->requireFile('\\App\\Core\\Handler', __ROOT . 'App/Core/Handler.php');
        $Handler = new \App\Core\Handler($this);
        $Handler->render();
    }

    private function loadCoreClass(): void
    {
        $coreClasses = [
            '\\App\\Core\\Cache' => ['file' => 'App/Core/Cache.php'],
            '\\App\\Core\\Session' => ['file' => 'App/Core/Session.php'],
        ];

        foreach ($coreClasses as $namespace => $other) {
            $this->requireFile($namespace, __ROOT . $other['file']);
        }

        $this->setControllerClass();
    }

    private function setControllerClass(): void
    {
        $controllerClasses = [
            '\\App\\Core\\Controller\\ConstantController' => ['file' => 'App/Core/Controller/ConstantController.php', 'core' => true],
            '\\App\\Core\\Controller\\MenuController' => ['file' => 'App/Core/Controller/MenuController.php', 'core' => true],
            '\\App\\Core\\Controller\\PagesController' => ['file' => 'App/Core/Controller/PagesController.php', 'core' => true],
            '\\App\\Core\\Controller\\SubPagesController' => ['file' => 'App/Core/Controller/SubPagesController.php', 'core' => true]
        ];

        foreach ($controllerClasses as $namespace => $other) {
            $this->requireFile($namespace, __ROOT . $other['file']);
            if ($other['core']) {
                $this->core['controller'][$this->getClassName($namespace)] = new $namespace;
            }
        }

        $this->setAdapterClass();
    }

    private function setAdapterClass(): void
    {
        $adapterClasses = [
            '\\App\\Core\\Adapter\\ExtensionsAdapter' => ['file' => 'App/Core/Adapter/ExtensionsAdapter.php'],
            '\\App\\Core\\Adapter\\PageAdapter' => ['file' => 'App/Core/Adapter/PageAdapter.php'],
            '\\App\\Core\\Adapter\\TemplateAdapter' => ['file' => 'App/Core/Adapter/TemplateAdapter.php'],
            '\\App\\Core\\Adapter\\HandlerAdapter' => ['file' => 'App/Core/Adapter/HandlerAdapter.php']
        ];

        foreach (glob(self::INTERFACE_ADAPTER . 'Interface*.php', GLOB_NOSORT) as $file) {
            $this->requireFile(null, $file);
        }

        foreach ($adapterClasses as $namespace => $other) {
            $this->requireFile($namespace, __ROOT . $other['file']);
        }

        $this->setDatabaseClass();
    }

    private function setDatabaseClass(): void
    {
        $databaseClasses = [
            '\\App\\Core\\Database\\Connect' => ['file' => 'App/Core/Database/Connect.php'],
            '\\App\\Core\\Database\\Query' => ['file' => 'App/Core/Database/Query.php'],
            '\\App\\Core\\Database\\Uuid' => match (__CONFIG_DEFAULT_DATABASE) {
                'postgresql' => ['file' => 'App/Core/Database/Postgresql/Uuid.php']
            },
            '\\App\\Core\\Database\\Ready' => match (__CONFIG_DEFAULT_DATABASE) {
                'postgresql' => ['file' => 'App/Core/Database/Postgresql/Ready.php']
            }
        ];

        foreach (glob(self::INTERFACE_DATABASE . 'Interface*.php', GLOB_NOSORT) as $file) {
            $this->requireFile(null, $file);
        }

        foreach ($databaseClasses as $namespace => $other) {
            $this->requireFile($namespace, __ROOT . $other['file']);
        }

        $this->setAuthClass();
    }

    private function setAuthClass(): void
    {
        $authClasses = [
            '\\App\\Core\\Entity\\Auth\\Validation' => ['file' => 'App/Core/Entity/Auth/Validation.php'],
            '\\App\\Core\\Entity\\Auth' => ['file' => 'App/Core/Entity/Auth/Auth.php']
        ];

        foreach ($authClasses as $namespace => $other) {
            $this->requireFile($namespace, __ROOT . $other['file']);
        }

        $this->loadExtensions();
    }

    private function loadExtensions(): void
    {
        $extensionsList = Util::config('extensions');
        foreach ($extensionsList as $namespace => $value) {
            if (isset($value['status']) && $value['status']) {
                if (!isset($value['loadfile'])) {
                    $value['loadfile'] = 'Init.php';
                }

                $this->requireFile($namespace, __ROOT_APP_EXT . $this->getClassName($namespace) . DIRECTORY_SEPARATOR . $value['loadfile']);
                new $namespace($this);
            }
        }

        $this->setTemplateClass();
    }

    private function setTemplateClass(): void
    {
        $templateClasses = [
            '\\App\\Core\\Entity\\Template\\Block' => ['file' => 'App/Core/Entity/Template/Block.php'],
            '\\App\\Core\\Entity\\Template\\Body' => ['file' => 'App/Core/Entity/Template/Body.php']
        ];

        foreach ($templateClasses as $namespace => $other) {
            $this->requireFile($namespace, __ROOT . $other['file']);
        }
    }
}

?>