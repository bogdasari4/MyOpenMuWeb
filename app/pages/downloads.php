<?php

namespace App\Pages;

use App\Util;

class Downloads {

    private $data = array();

    public function __get($info) {
        $this->setInfo();
        return $this->data[$info];
    }

    public static function getInfo() {
        $info = new Downloads;
        return $info;
    }

    private function setInfo() {

        $config = Util::config('body');

        $this->data['page'] = [
            'downloads' => [
                'text' => __LANG['body']['page']['downloads'],
                'files' => $config['page']['downloads']['files']
            ]
        ];
    }

}

?>