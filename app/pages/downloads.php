<?php

namespace App\Pages;

use App\Util;

class Downloads {

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
     * @return Downloads
     */
    public static function getInfo() {
        $info = new Downloads;
        return $info;
    }

    /**
     * Summary of setInfo
     * @return void
     */
    private function setInfo(): void {

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