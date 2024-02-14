<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Alert;
use App\Core\Adapter\PageAdapter;
use App\Util;

/**
 * Registration processing page.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class SignUp extends PageAdapter
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
        if (isset($this->session->user))
            Util::redirect();

        $data['text'] = __LANG['body']['page']['signup'];
        $data['config'] = $this->config;

        if (isset($_POST['signup'])) {
            $signup = $_POST['signup'];

            foreach ($signup as $key => $value) {
                if($key == 'email') {
                    if (!$this->auth()->validation()->email($signup['email']) || !$this->auth()->validation()->stringLength($value, $this->config['validator'][$key]['minLength'], $this->config['validator'][$key]['maxLength']))
                        throw new Alert(0x2f2, 'warning', '/signup');
                    continue;
                }

                if (!$this->auth()->validation()->regularExpression($value) || !$this->auth()->validation()->stringLength($value, $this->config['validator'][$key]['minLength'], $this->config['validator'][$key]['maxLength'])) {
                    throw new Alert(0x2f2, 'warning', '/signup');
                }
            }

            if ($this->ready()->createAccount($signup)) {
                throw new Alert(0x291, 'success', '/');
            }

            throw new Alert(0x32b, 'info', '/signup');
        }

        return $data;
    }
}

?>
