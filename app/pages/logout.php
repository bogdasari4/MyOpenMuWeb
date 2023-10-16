<?php

namespace App\Pages;

use App\Util;

class LogOut {
    
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
     * @return LogOut
     */
    public static function getInfo() {
        $info = new LogOut;
        return $info;
    }

    /**
     * Summary of setInfo
     * @return void
     */
    private function setInfo(): void {
        session_destroy();
        Util::redirect();
    }
}