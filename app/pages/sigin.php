<?php

namespace App\Pages;

use App\Util;
use App\Core\Auth\Validation;
use \Exception;

class SigIn extends \App\Core\Auth\SigIn {
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
     * @return SigIn
     */
    public static function getInfo() {
        $info = new SigIn;
        return $info;
    }

    /**
     * Summary of setInfo
     * @return void
     */
    private function setInfo(): void {
        
        if(isset($_SESSION['user']['isLogin'])) Util::redirect('/account');

        $config = Util::config('body');

        $this->data['page'] = [
            'sigin' => [
                'alert' => '',
                'text' => __LANG['body']['page']['sigin'],
                'config' => $config['page']['sigin']['validator']
            ]
        ];

        if(isset($_POST['submit'])) {
            try {

                $data = $_POST['sigin'];

                $data['loginName'] = Util::trimSChars($data['loginName']);
                if(!Validation::LoginName($data['loginName'], $config['page']['sigin']['validator']['loginName'])) throw new Exception(__LANG['body']['page']['sigin']['form']['loginName']['alert'], 2);

                $data['password'] = Util::trimSChars($data['password']);
                if(!Validation::password($data['password'], $config['page']['sigin']['validator']['password'])) throw new Exception(__LANG['body']['page']['sigin']['form']['password']['alert'], 2);

                if(!$this->authorization($data)) throw new Exception(__LANG['body']['page']['sigin']['form']['alert']['loginFailed'], 2);
                throw new Exception(__LANG['body']['page']['sigin']['form']['alert']['signingIn'], 1);
                
            } catch(Exception $alert) {
                $this->data['page']['sigin']['alert'] = str_replace(
                    ['{{ str_message }}', '{{ code_message }}'],
                    [$alert->getMessage(), $alert->getCode()],
                    file_get_contents(__ROOT . 'templates/' . __CONFIG['other']['template'] . '/alert.html')
                );
                if($alert->getCode() == 1) {
                    Util::redirect('/account', 2);
                }
            }
        }

    }

}

?>