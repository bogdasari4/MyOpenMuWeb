<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages\Account;

use App\Alert;
use App\Util;
use App\Core\Adapter\PageAdapter;

/**
 * The class controls password changes.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class ChangePass extends PageAdapter
{
    /**
     * The public function `getInfo()` provides data for rendering pages.
     * @return array
     * We return an array of data.
     */
    public function getInfo(): array
    {
        return $this->setInfo();
    }

    /**
     * The private function `setInfo()` collects information into a data array.
     * @return array
     * We return an array of data.
     */
    private function setInfo(): array
    {
        $data = [];

        $data['config'] = $this->config['changepass'];

        if (isset($_POST['changepass'])) {
            $changepass = $_POST['changepass'];

            if ($changepass['password'] == $changepass['newpassword'])
                throw new Alert(0x14b, 'info', '/account/changepass');

            if ($changepass['newpassword'] != $changepass['renewpassword'])
                throw new Alert(0x3b4, 'info', '/account/changepass');

            if (!$this->auth()->validation()->regularExpression($changepass['newpassword']))
                throw new Alert(0x1af, 'info', '/account/changepass');

            if (!$this->auth()->validation()->stringLength($changepass['newpassword'], $this->config['changepass']['validator']['password']['minLength'], $this->config['changepass']['validator']['password']['maxLength']))
                throw new Alert(0x1af, 'info', '/account/changepass');
                
            if ($this->ready()->getAccountInfo()->changePass($changepass)) {
                throw new Alert(0x2ae, 'success', '/logout');
            }

            throw new Alert(0x25a, 'warning', '/account/changepass');
            
        }

        return $data;
    }
}