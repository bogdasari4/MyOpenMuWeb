<?php

namespace App\Pages;

use App\Util;
use App\Core\Auth\Validation;
// use App\Session;
use Exception;

class SignUp extends \App\Core\Auth\SignUp {

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
     * @return SignUp
     */
    public static function getInfo() {
        $info = new SignUp;
        return $info;
    }

    /**
     * Summary of setInfo
     * @throws \Exception
     * @return void
     */
    private function setInfo(): void {

        if(isset($_SESSION['user']['isLogin'])) Util::redirect('/account');

        $config = Util::config('body');

        $this->data['page'] = [
            'signup' => [
                'alert' => '',
                'text' => __LANG['body']['page']['signup'],
                'config' => $config['page']['signup']['validator']
            ]
        ];

        if(isset($_POST['submit'])) {
            try {
                
                $data = $_POST['signup'];

                $data['loginName'] = Util::trimSChars($data['loginName']);
                if(!Validation::LoginName($data['loginName'], $config['page']['signup']['validator']['loginName'])) throw new Exception(__LANG['body']['page']['signup']['form']['loginName']['alert'], 2);

                $data['password'] = Util::trimSChars($data['password']);
                if(!Validation::password($data['password'], $config['page']['signup']['validator']['password'])) throw new Exception(__LANG['body']['page']['signup']['form']['password']['alert'], 2);

                $data['email'] = Util::trimSChars($data['email']);
                if(!Validation::EMail($data['email'], $config['page']['signup']['validator']['email'])) throw new Exception(__LANG['body']['page']['signup']['form']['email']['alert'], 2);

                if(!$this->createAccount($data)) throw new Exception(__LANG['body']['page']['signup']['form']['alert']['loginNamAlreadyExists'], 2);
                throw new Exception(__LANG['body']['page']['signup']['form']['alert']['accountCreated'], 1);

            } catch(Exception $alert) {
                
                $this->data['page']['signup']['alert'] = str_replace(
                    ['{{ str_message }}', '{{ code_message }}'],
                    [$alert->getMessage(), $alert->getCode()],
                    file_get_contents(__ROOT . 'templates/' . __CONFIG['other']['template'] . '/alert.html')
                );
            }
        }

        $this->data['page']['signup']['text']['form']['loginName']['help'] = sprintf(
            $this->data['page']['signup']['text']['form']['loginName']['help'], 
            $config['page']['signup']['validator']['loginName']['min_length'], 
            $config['page']['signup']['validator']['loginName']['max_length']
        );
        $this->data['page']['signup']['text']['form']['password']['help'] = sprintf(
            $this->data['page']['signup']['text']['form']['password']['help'],
            $config['page']['signup']['validator']['password']['min_length'],
            $config['page']['signup']['validator']['password']['max_length']
        );
    }
}

?>