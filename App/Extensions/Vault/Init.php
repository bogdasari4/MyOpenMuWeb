<?php

namespace App\Extensions;

use App\Assistant;
use App\Core\Adapter\ExtensionsAdapter;

class Vault extends ExtensionsAdapter {

    use Assistant;

    final protected function load(): void {

        // $this->controller('ConstantController')->add([mb_strtoupper($this->getClassName(self::class)) => __ROOT_APP_EXT . $this->getClassName(self::class) . DIRECTORY_SEPARATOR], '__ROOT_APP_EXT');
        // $this->controller('PagesController')->vault = ['namespace' => self::class . '\\Page', 'file' => __ROOT_APP_EXT_VAULT . 'Page.php'];
        // $this->controller('MenuController')->ranking = ['vote' => ['link' => 'vote', 'position' => 30, 'islogin' => false]];
    }
}

?>