<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Template;

use App\Alert;
use App\Core\Component\{RedirectTo, FormattedGet, GetLanguageCode};

use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Template rendering.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class RenderTemplate
{
    use RedirectTo, FormattedGet, GetLanguageCode;

    private object $body;

    private string $pageName;

    public function __construct(private object $app)
    {
        /**
         * For libraries that specify autoload information, Composer generates a _ROOT/App/Package/autoload.php file.
         * Start using the classes that those libraries provide without any extra work.
         * 
         * @see https://getcomposer.org/doc/01-basic-usage.md#autoloading
         */
        if(!@include_once('App/Package/autoload.php'))
            throw new \Exception('Can\'t find composer autoload file.');

        $this->body = new Body($this->app);
        $this->pageName = $this->formattedGet('page', __CONFIG_DEFAULT_PAGE);
    }

    /**
     * The main function is to render the page and display it using the Twig 3.x extension package.
     * @see https://twig.symfony.com/doc/3.x/
     * Uses 'FilesystemLoader' to load a template from the file system.
     * @throws Alert
     * Used for notifications.
     * @return void
     */
    public function render(): void
    {
        $this->getLanguage();

        try {
            $FSLoader = new FilesystemLoader(__ROOT_TEMP_ACTIVE);

            $content = [];
            $this->catchPage();

            if (!isset($this->app->pagescontroller->{$this->pageName})) {
                throw new Alert(0x23b, 'info', '/');
            }

            $pageController = $this->app->pagescontroller->{$this->pageName};
            $pageData['page'][$this->pageName] = $this->getPageData($pageController);
            $addPath = 'pages';

            if (isset($this->app->subpagescontroller->{$this->pageName})) {
                $subPageController = $this->app->subpagescontroller->{$this->pageName};
                $subPage = $this->formattedGet('subpage', array_key_first($subPageController));
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
        } finally {
            $twig = new Environment($FSLoader, ['debug' => false, 'charset' => 'UTF-8', 'autoescape' => 'html']);
            $twig->addExtension(new IntlExtension);
            $template = $twig->load('Body.html');
            $template->display(array_merge($this->body->getData(), ['content' => is_array($content) ? $twig->render($content['path'], $content['data']) : $content]));
        }
    }

    /**
     * We declare the page class and get the information to render the page block.
     * @param array $pageData
     * We receive page data.
     * @return array
     */
    private function getPageData(array $pageData): array
    {
        $pageClass = new $pageData['namespace']($this->app, $this->body->getPageConfig($this->pageName));
        return $pageClass->getInfo();
    }

    /**
     * For some changes, we intercept the page before it is rendered.
     * @return void
     */
    private function catchPage(): void
    {
        switch ($this->pageName) {
            case 'account':
                if($this->formattedGet('subpage', '') == 'getchar') {
                    $readyQueries = new ReadyQueries;
                    if($readyQueries->accountInfo()->changeCharacter($this->formattedGet('request', '', false)))
                        $this->redirectTo('/account');
                    
                    throw new Alert(0x28f, 'info', '/account');
                }
                break;

            case 'getlang':
                $this->getLanguage($this->formattedGet('subpage', __CONFIG_LANGUAGE_SET));
                $this->redirectTo();
                break;
        }
    }

    /**
     * Function for reading a language file using the language code. 
     * If '$languageCode' is specified, we enter the data into cookies and work with the cookie code 'LanguageCode'.
     * @param null|int $languageCode
     * Specifies which language code we will work with.
     * @example [English: 45], [Belorussian: 90], [German: 481], [Russian: 570], [Ukrainian: 720]
     * @return void
     */
    private function getLanguage(?int $languageCode = null): void
    {
        if (is_null($languageCode)) {
            $languageCode = $this->getLanguageCode();

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