<?php

namespace App\Core\Template;

use App\Util;

class Body extends \App\Core\Template\Block {

    private $data = array();

    public function __get($info) {
        $this->setInfo();
        return $this->data[$info];
    }

    public static function getInfo() {
        $info = new Body;
        return $info;
    }

    private function setInfo() {

        $config = Util::config();

        $this->data['core'] = [
            'template' => [
                'config' => $config['body']
            ]
        ];

        foreach($config['body']['block'] as $key => $block ) {
            $this->data['core']['template']['block'][$key] = $block['status'] ? $this->{$key}($block) : null;
        }
    }
}

?>