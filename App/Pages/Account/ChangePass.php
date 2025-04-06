<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages\Account;

use App\Alert;
use App\Core\Adapter\PageAdapter;
use App\Core\Component\Validations;

/**
 * The class controls password changes.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class ChangePass extends PageAdapter
{

    use Validations;

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

        if(isset($_POST['changepass'])) {
            $changepass = $_POST['changepass'];

            if ($changepass['password'] == $changepass['newpassword'])
                throw new Alert(0x14b, 'info', '/account/changepass');

            if ($changepass['newpassword'] != $changepass['renewpassword'])
                throw new Alert(0x3b4, 'info', '/account/changepass');

            if (!$this->validateRegularExpression($changepass['newpassword']))
                throw new Alert(0x1af, 'info', '/account/changepass');

            if (!$this->validateStringLength($changepass['newpassword'], $this->config['changepass']['validator']['password']['minLength'], $this->config['changepass']['validator']['password']['maxLength']))
                throw new Alert(0x1af, 'info', '/account/changepass');
                
            if ($this->readyQueries()->accountInfo()->changePass($changepass)) 
                throw new Alert(0x2ae, 'success', '/logout');

            throw new Alert(0x25a, 'warning', '/account/changepass');
            
        }

        return $data;
    }
}