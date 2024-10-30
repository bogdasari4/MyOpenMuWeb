<?php

namespace App\Core;

use App\Alert;
use App\Assistant;
use App\Util;

use App\Core\Template\Body;
use App\Core\Database\PostgreSQL\ReadyStrings;

use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

final class Handler
{
    use Assistant {
        spotGET as private render;
        spotGET as organize;
        spotGET as catchPage;
    }

    public const ACCESS_INDEX = 'index';

    public const ACCESS_INSTALL = 'install';

    private string $pageName;

    private object $app;

    private object $body;

    /**
     * Public function: renderPage
     * @throws \Exception
     * @return void
     * Render the page before output.
     */
    public function switch(string $access): void
    {
        switch ($access) {
            case self::ACCESS_INDEX:
                $this->app = new \stdClass;

                $controllers = ['ConstantController', 'MenuController', 'PagesController', 'SubPagesController'];
                foreach($controllers as $controller)
                {
                    $namespace = 'App\\Core\\Controller\\' . $controller;
                    $this->app->{mb_strtolower($controller)} = new $namespace;
                }

                $this->body = new Body($this->app);
                $this->pageName = $this->spotGET('page', __CONFIG_DEFAULT_PAGE);

                $this->catchPage();
                $this->render();
                break;

            case 'api':
                break;
            
            case 'install':
            default:
                break;
        }
    }

    private function render(): void
    {
        $this->getLanguage();

        try {
            $FSLoader = new FilesystemLoader(__ROOT_TEMP_ACTIVE);

            if (!isset($this->app->pagescontroller->{$this->pageName})) {
                throw new Alert(0x23b, 'info', '/');
            }

            $pageController = $this->app->pagescontroller->{$this->pageName};
            $pageData['page'][$this->pageName] = $this->getPageData($pageController);
            $addPath = 'pages';

            if (isset($this->app->subpagescontroller->{$this->pageName})) {
                $subPageController = $this->app->subpagescontroller->{$this->pageName};
                $subPage = $this->spotGET('subpage', array_key_first($subPageController));
                if (!isset($subPageController[$subPage])) {
                    throw new Alert(0x23b, 'info', '/' . $this->pageName . '/' . array_key_first($subPageController));
                }

                $pageController = $subPageController[$subPage];
                $pageData['page'][$this->pageName][$subPage] = $this->getPageData($pageController);
                $addPath = $this->pageName;

            }

            $FSLoader->addPath($pageController['template']['path'], $addPath);

            $content = ['path' => '@' . $addPath . DIRECTORY_SEPARATOR . $pageController['template']['name'], 'data' => $pageData];

            http_response_code(200);

        } catch (Alert $e) {
            $content = $e->getCalloutTemplate();
        }

        $twig = new Environment($FSLoader, ['debug' => false, 'charset' => 'UTF-8', 'autoescape' => 'html']);
        $twig->addExtension(new IntlExtension());
        $template = $twig->load('Body.html');
        $template->display(array_merge($this->body->getData(), ['content' => is_array($content) ? $twig->render($content['path'], $content['data']) : $content]));
    }

    private function getPageData(array $pageData): array
    {
        $pageClass = new $pageData['namespace']($this->app, $this->body->getPageConfig($this->pageName));
        return $pageClass->getInfo();
    }

    private function catchPage(): void
    {
        switch ($this->pageName) {
            case '':
                break;

            case 'account':
                if ($this->spotGET('subpage', '') == 'getchar') {
                    $readyStrings = new ReadyStrings;
                    $readyStrings->getAccountInfo()->getCharacter($this->spotGET('request', '', false));
                    Util::redirect('/account');
                }
                break;

            case 'getlang':
                $this->getLanguage($this->spotGET('subpage', __CONFIG_LANGUAGE_SET));
                Util::redirect();
                break;
        }
    }

    /**
     * Public function: getLanguage
     * Language handler.
     * @return bool
     */
    private function getLanguage(?int $languageCode = null): void
    {
        if (is_null($languageCode)) {
            $languageCode = isset($_COOKIE['LanguageCode']) ? $_COOKIE['LanguageCode'] : __CONFIG_LANGUAGE_SET;

            $languageFile = __ROOT_APP_JSON_LANG . $languageCode . DIRECTORY_SEPARATOR . 'Main.json';
            if (file_exists($languageFile)) {
                $language = json_decode(file_get_contents($languageFile), true);
                if ($language != null) {
                    define('__LANG', $language);
                    return;
                }
            }
        }

        if (is_int($languageCode)) {
            if (__CONFIG_LANGUAGE_SET != $languageCode || (isset($_COOKIE['LanguageCode']) && $_COOKIE['LanguageCode'] != $languageCode)) {
                setcookie('LanguageCode', $languageCode, time() + __CONFIG_LANGUAGE_EXPIRES, '/');
            }
        }
    }
}

?>