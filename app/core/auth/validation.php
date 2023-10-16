<?php

namespace App\Core\Auth;

class Validation {

    
    /**
     * Summary of chars
     * @param string $string
     * @return bool
     */
    private static function chars(string $string): bool {
		if(preg_match("/^[A-Za-z0-9]+$/", $string)) {
            return true;
        }
        return false;
	}

    /**
     * Summary of LoginName
     * @param string $login
     * @param array $config
     * @return bool
     */
    public static function LoginName(string $login, array $config): bool {
        if($login != '') {
            if((strlen($login) >= $config['min_length']) && (strlen($login) <= $config['max_length']) && self::chars($login)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Summary of password
     * @param string $password
     * @param array $config
     * @return bool
     */
    public static function password(string $password, array $config): bool {
        if($password != '') {
            if((strlen($password) >= $config['min_length']) && (strlen($password) <= $config['max_length']) && self::chars($password)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Summary of EMail
     * @param string $email
     * @param array $config
     * @return bool
     */
    public static function EMail(string $email, array $config): bool {
        if(strlen($email) <= $config['max_length']) {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
        }

        return false;
    }

}

?>