<?php

namespace App\Core;

use App\Util;
use Exception;
use App\Core\Template\Body;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extra\Intl\IntlExtension;

interface THandler {
    public function getLanguage(): bool;
    public function renderPage(): void;
}

class Handler implements THandler {

    /**
     * @var string
     * A formatted string containing the name page.
     */
    private string $page = '';

    /**
     * Public function: getLanguage
     * Language handler.
     * @return bool
     */
    public function getLanguage(): bool {

        $code = 0;

        if(!$code) $code = isset($_COOKIE['language_code']) ? $_COOKIE['language_code'] : __CONFIG['langugage']['default'];

        $langFile = __ROOT . 'app/json/language/' . $code . '/main.json';
        if(file_exists($langFile)) {
            $lang = json_decode(file_get_contents($langFile), true);
            if($lang) {
                define('__LANG', $lang);
                return true;
            }
        }

        return false;
    }

    /**
     * Public function: renderPage
     * @throws \Exception
     * @return void
     * Render the page before output.
     */
    public function renderPage(): void {
        switch(access) {
            case 'index':
                $this->page = (isset($_GET['page']) && $_GET['page'] != '') ? $_GET['page'] : 'main';
                $this->page = strtolower($this->page);
                $this->page = Util::trimSChars($this->page);

                if($this->page == 'getlang') {
                    if(isset($_GET['subpage']) && $_GET['subpage'] != '' && is_numeric($_GET['subpage'])) {
                        $code = Util::trimSChars($_GET['subpage']);
                        if(isset(__CONFIG['langugage']['code'][$code])) {
                            if((isset($_COOKIE['language_code']) && $_COOKIE['language_code'] != $code) || __CONFIG['langugage']['default'] != $code) {
                                setcookie('language_code', $code, time() + __CONFIG['langugage']['expires'], '/');
                            }
        
                            Util::redirect();
                        }
                    }
                }

                $templateData = Body::getInfo()->core;

                $pageData = $this->getPageData();
                
                if(!@file_exists(__ROOT . 'templates/' . __CONFIG['other']['template'])) throw new Exception(sprintf(__LANG['exception']['handler']['template'], __CONFIG['other']['template']));
                $pageHTML = new FilesystemLoader(__ROOT . 'templates/' . __CONFIG['other']['template']);
                $twig = new Environment($pageHTML, ['charset' => 'utf-8']);
                $twig->addExtension(new IntlExtension());

                if(!@file_exists(__ROOT . 'templates/' . __CONFIG['other']['template'] . '/' . $this->page . '.html')) throw new Exception(sprintf(__LANG['exception']['handler']['page'], $this->page));

                if(isset($templateData['template']['config']['body']['block']['siginForm']) && ($this->page == 'sigin') || $templateData['template']['session']['isLogin']) {
                    $templateData['template']['config']['body']['block']['siginForm']['status'] = false;
                }
                if(!isset($_SESSION['user']['isLogin'])) {
                    $templateData['template']['config']['body']['block']['accountMenu']['status'] = false;
                }

                $templateHTML = $twig->load('body.html');
                $templateHTML->display(
                    array_merge(
                        $templateData, 
                        [
                            'content' => $twig->render($this->page . '.html', $pageData)
                        ]
                    )
                );
                break;

            case 'api':
                break;
        }
    }

    /**
     * Private function: getPageData
     * Used only within its class.
     * @throws \Exception
     * @return array
     * We generate the page data.
     */
    private function getPageData(): array {
        
        if(!isset(__CONFIG['pages'][$this->page])) throw new Exception(__LANG['exception']['handler']['key']); 

            
        if(!@include_once(__ROOT . 'app/pages/' . $this->page . '.php')) throw new Exception(sprintf(__LANG['exception']['handler']['include'], $this->page));
        if(!@class_exists(__CONFIG['pages'][$this->page])) throw new Exception(sprintf(__LANG['exception']['handler']['class'], __CONFIG['pages'][$this->page]));
        $pageClass =__CONFIG['pages'][$this->page];
        $pageClass = new $pageClass;
        $pageData = $pageClass->__get('page');
        if(!@is_array($pageData)) throw new Exception(__LANG['exception']['handler']['is_array']);
 
        return $pageData;
    }
}

?>