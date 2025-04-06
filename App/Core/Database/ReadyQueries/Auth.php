<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database\ReadyQueries;

use App\Core\Adapter\DBAdapter;
use App\Core\Component\Uuid;
use App\Core\Session;
use DateTime;

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Auth extends DBAdapter
{
    use Uuid;

    public function createAccount(array $data): bool
    {
        if($this->queryBuilder()->getRow('data."Account"', ['LoginName' => $data['loginName']]) === false) {
            $vaultUuid = $this->uuid_generateV4();
            if($this->queryBuilder()->insertRow('data."ItemStorage"', ['Id' => $vaultUuid, 'Money' => 0])) {
                $params = [
                    'Id' => $this->uuid_generateV4(),
                    'VaultId' => $vaultUuid,
                    'LoginName' => $data['loginName'],
                    'PasswordHash' => password_hash($data['password'], PASSWORD_BCRYPT),
                    'SecurityCode' => '',
                    'EMail' => $data['email'],
                    'RegistrationDate' => date('Y-m-d H:i:s.u O'),
                    'State' => 0,
                    'TimeZone' => 0,
                    'VaultPassword' => '',
                    'IsVaultExtended' => 'false',
                    'ChatBanUntil' => NULL
                ];

                return $this->queryBuilder()->insertRow('data."Account"', $params);
            }
        }

        return false;
    }

    public function authorization(array $data): bool
    {
        if(($user = $this->queryBuilder()->getRow('data."Account"', ['LoginName' => $data['loginName']], '"Id", "LoginName", "PasswordHash", "EMail", "RegistrationDate"')) !== false) {
            if(password_verify($data['password'], $user[2])) {
                $datetime = new DateTime($user[4]);

                $session = new Session;
                $session->user = [
                    'id' => $user[0],
                    'loginName' => $user[1],
                    'email' => $user[3] ?? null,
                    'registrationDate' => $datetime->format('H:i d-m-Y')
                ];

                return true;
            }
        }

        return false;
    }
}