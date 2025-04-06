<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages\Account;

use App\Core\Adapter\PageAdapter;
use App\Core\Component\RedirectTo;

/**
 * General information about the account.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Information extends PageAdapter
{
    use RedirectTo;

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
        $data = $this->readyQueries()->accountInfo()->information();

        if (!$data)
            $this->redirectTo('/logout');

        $data['email'] = $data['email'] ?? __LANG['body']['page']['account']['information']['emailempty'];

        return $data;
    }
}
?>