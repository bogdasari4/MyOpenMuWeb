<?php

namespace App\Core\Auth;

use App\Core\PostgreSQL\Query;
use App\Core\PostgreSQL\Support;

class SignUp {

    /**
     * Summary of userCheck
     * @param string $login
     * @return bool
     */
    private function userCheck(string $login): bool {
        if(Query::getRow('SELECT * FROM data."Account" WHERE "LoginName" = :loginName', ['loginName' => $login])) {
            return false;
        }

        return true;
    }

    /**
     * Summary of createVault
     * @return string
     */
    private function createVault() {
        $uuid = Support::uuidv4();
        Query::insertRow('data."ItemStorage"', ['Id' => $uuid, 'Money' => 0]);
        return $uuid;
    }

    /**
     * Summary of createAccount
     * @param array $data
     * @return bool
     */
    protected function createAccount(array $data): bool {

        if($this->userCheck($data['loginName'])) {
            $data = [
                'Id' => Support::uuidv4(),
                'VaultId' => self::createVault(),
                'LoginName' => $data['loginName'],
                'PasswordHash' => password_hash($data['password'], PASSWORD_BCRYPT),
                'SecurityCode' => '',
                'EMail' => $data['email'],
                'RegistrationDate' => date('Y-m-d H:i:s'),
                'State' => 0,
                'TimeZone' => 0,
                'VaultPassword' => '',
                'IsVaultExtended' => 'false',
                'ChatBanUntil' => NULL
            ];

            if(Query::insertRow('data."Account"', $data)) {
                return true;
            }
        }

        return false;

    }

}

?>