<?php

namespace APP\Core\PostgreSQL;

class Support {

    public static function uuidv4(int $length = 16): string {

        $data = random_bytes($length);

        assert(strlen($data) == 16);
        
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

}

?>