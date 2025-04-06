<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Alert;
use App\Core\Adapter\PageAdapter;
use App\Core\Component\{RedirectTo, Validations};

/**
 * Registration processing page.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class SignUp extends PageAdapter
{

    use RedirectTo, Validations;

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
            $this->redirectTo();

        $data['text'] = __LANG['body']['page']['signup'];
        $data['config'] = $this->config;

        if (isset($_POST['signup'])) {
            $signup = $_POST['signup'];

            foreach ($signup as $key => $value) {
                if($key == 'email') {
                    if (!$this->validateEmail($signup['email']) || !$this->validateStringLength($value, $this->config['validator'][$key]['minLength'], $this->config['validator'][$key]['maxLength']))
                        throw new Alert(0x2f2, 'warning', '/signup');
                    continue;
                }

                if (!$this->validateRegularExpression($value) || !$this->validateStringLength($value, $this->config['validator'][$key]['minLength'], $this->config['validator'][$key]['maxLength'])) {
                    throw new Alert(0x2f2, 'warning', '/signup');
                }
            }

            if ($this->readyQueries()->auth()->createAccount($signup)) {
                throw new Alert(0x291, 'success', '/');
            }

            throw new Alert(0x32b, 'info', '/signup');
        }

        return $data;
    }
}

?>
