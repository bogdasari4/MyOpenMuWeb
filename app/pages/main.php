<?php

namespace App\Pages;

use App\Util;

class Main {

    /**
     * Summary of data
     * @var array
     */
    private $data = ['page' => []];

    /**
     * Summary of __get
     * @param string $info
     * @return array
     */
    public function __get(string $info): array {
        $this->setInfo();
        return $this->data[$info];
    }

    /**
     * Summary of getInfo
     * @return Main
     */
    public static function getInfo() {
        $info = new Main;
        return $info;
    }

    /**
     * Summary of setInfo
     * @return void
     */
    private function setInfo(): void {
        $config = Util::config('body');

        $this->data['page'] = [
            'main' => [
                'text' => __LANG['body']['page']['main']
            ]
        ];

        $xml = simplexml_load_file($config['page']['main']['url']);
        $i = 0;
        foreach($xml->channel->item as $item) {

            $this->data['page']['main']['items'][] = [
                'title' => (string) $item->title,
                'description' => strip_tags((string) $item->description),
                'media' => (string) $item->children('media', true)->content->attributes(),
                'auth' => (string) $item->children('dc', true)->creator,
                'date' => date('d.h.Y', strtotime((string) $item->pubDate)),
                'link' => (string) $item->link
            ];

            if(++$i == 5) break;
        }
    }
}

?>