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

        $config = Util::config('body');

        $this->data['core'] = [
            'template' => [
                'text' => __LANG['body']['template'],
                'config' => [
                    'core' => __CONFIG,
                    'body' => $config
                ],
                'session' => [
                    'isLogin' => isset($_SESSION['user']['isLogin']) ? true : false
                ]
            ]
        ];

        if($this->data['core']['template']['session']['isLogin']) {
            $this->data['core']['template']['account'] = [
                'account' => __LANG['body']['block']['accountMenu']['menu'][0]['header'],
                'character' => __LANG['body']['block']['accountMenu']['menu'][1]['header']
            ];
            foreach($config['block']['accountMenu']['menu'] as $key => $menu) {
                foreach($menu as $subkey => $submenu) {
                    $this->data['core']['template']['account']['menu'][$key][] = [
                        'name' => __LANG['body']['block']['accountMenu']['menu'][$key][$subkey]['name'],
                        'link' => $submenu['link']
                    ];
                }
            }
        }

        foreach($config['block'] as $key => $block ) {
            $this->data['core']['template']['block'][$key] = $block['status'] ? $this->{$key}($block) : null;
        }
    }
}

?>