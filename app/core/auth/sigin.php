<?php

namespace App\Core\Auth;

use App\Core\PostgreSQL\Query;

class SigIn {

    protected function authorization(array $data): bool {
        if($account = Query::getRow('SELECT "Id", "LoginName", "PasswordHash" FROM data."Account" WHERE "LoginName" = :loginName', ['loginName' => $data['loginName']])) {
            if(password_verify($data['password'], $account[2])) {
                $_SESSION['user'] = [
                    'isLogin' => true,
                    'loginId' => $account[0],
                    'loginName' => $account[1]
                ];

                $this->getRandChar();
                
                return true;
            }
        }   

        return false;
    }

    private function getRandChar(): void {
        if($char = Query::getRow(
            'SELECT "Id", "Name"
            FROM data."Character"
            WHERE "AccountId" = :id',
            [
                'id' => $_SESSION['user']['loginId']
            ]
        )) {
            $_SESSION['user']['character'] = [
                'id' => $char[0],
                'name' => $char[1]
            ];
        }
    }

}

?>