<?php

namespace App\Core;

use App\Util;
use Exception;
use App\Core\Template\Body;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extra\Intl\IntlExtension;

class Handler {

    private $page;

    private function getPageData(): array {

        $this->page = (isset($_GET['page']) && $_GET['page'] != '') ? $_GET['page'] : 'main';
        $this->page = strtolower($this->page);
        $this->page = Util::trimSChars($this->page);
        
        $config = Util::config('core');
        if(isset($config['pages'][$this->page])) {

            if(!@include_once(__ROOT . 'app/pages/' . $this->page . '.php')) throw new Exception('');

            $pageData = $config['pages'][$this->page]::getInfo()->page;
            if(!@is_array($pageData)) throw new Exception('');
        } else {
            Util::redirect();
        }

        return $pageData;
    }

    public function renderPage() {
        switch(access) {
            case 'index':
                try {
                    $pageData = $this->getPageData();
                    $pageHTML = new FilesystemLoader(__ROOT . 'templates/openmu');
                    $twig = new Environment($pageHTML, ['charset' => 'utf-8']);
                    $twig->addExtension(new IntlExtension());

                    $templateHTML = $twig->load('body.html');
                    $templateHTML->display(
                        array_merge(
                            Body::getInfo()->core, 
                            [
                                'content' => $twig->render($this->page . '.html', $pageData)
                            ]
                        )
                    );
                } catch(Exception $e) {
                    echo $e->getMessage();
                }
                break;

            case 'api':
                break;
        }
    }
    
}

?>