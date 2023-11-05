<?php

namespace App\Pages;

use App\Util;

class Main {

    /**
     * An array of data prepared in this class.
     * @var array
     */
    private array $data = ['page' => []];

    /**
     * When the __get() magic method is called, data will be read from this class.
     * @param string $info
     * The parameter takes the value 'page' automatically in the handler class.
     * 
     * @return array
     * We return an array of data.
     */
    public function __get(string $info): array {
        $this->setInfo();
        return $this->data[$info];
    }

    /**
     * Preparing a data array.
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