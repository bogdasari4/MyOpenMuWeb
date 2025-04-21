<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core;

use App\Core\Component\{ConfigLoader, FormattedGet};
use App\Core\Template\RenderTemplate;

/**
 * Access handler.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Handler
{
    use ConfigLoader, FormattedGet;

    private object $app;

    /**
     * We change the operating mode of the handler.
     * @param string $access
     * Access type: example [ACCESS_INDEX], [ACCESS_API], [ACCESS_INSTALL]
     * @return void
     */
    public function switch(string $access): void
    {
        $this->app = new \stdClass;

        switch ($access) {
            case 'index':

                $this->controllerLoader(['ConstantController', 'MenuController', 'PagesController', 'SubPagesController']);
                $this->loadExtensions();

                $template = new RenderTemplate($this->app);
                $template->render();
                break;

            case 'api':
                $this->controllerLoader(['ApiController']);
                if(isset($this->app->apicontroller->{$this->formattedGet('path')})) {
                    new $this->app->apicontroller->{$this->formattedGet('path')}['namespace'];
                }
                break;
        }
    }

    private function controllerLoader(array $className): void
    {
        foreach($className as $controller)
        {
            $namespace = 'App\\Core\\Controller\\' . $controller;
            $this->app->{mb_strtolower($controller)} = new $namespace;
        } 
    }

    /**
     * The function declares extension classes from the 'extensions.json' list.
     * @return void
     */
    private function loadExtensions(): void
    {
        foreach($this->configLoader('extensions', false) as $key => $value)
        {
            if(!$value->status)
                continue;

            $extension = $key . '\\' . $value->loadfile;
            new $extension($this->app);
        }
    }
}

?>