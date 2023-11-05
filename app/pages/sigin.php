<?php

namespace App\Pages;

use App\Util;
use App\Core\Auth\Validation;
use \Exception;

class SigIn extends \App\Core\Auth\SigIn {
    
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
     * @throws Exception
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