<?php

namespace App\Pages;

use App\Util;

class LogOut {
    
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
        session_destroy();
        Util::redirect();
    }
}