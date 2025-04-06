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
 * User login page.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class SigIn extends PageAdapter
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


        $data['text'] = __LANG['body']['page']['sigin'];
        $data['config'] = $this->config;

        if (isset($_POST['sigin'])) {
            $sigin = $_POST['sigin'];

            foreach ($sigin as $key => $value) {
                if (!$this->validateRegularExpression($value) || !$this->validateStringLength($value, $this->config['validator'][$key]['minLength'], $this->config['validator'][$key]['maxLength'])) {
                    throw new Alert(0x2ee, 'warning', '/sigin');
                }
            }

            if ($this->readyQueries()->auth()->authorization($sigin)) {
                throw new Alert(0x2f5, 'success', '/account');
            }

            throw new Alert(0x1ac, 'warning', '/sigin');
        }

        return $data;
    }

}

?>
