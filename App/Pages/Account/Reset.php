<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages\Account;

use App\Core\Adapter\PageAdapter;

/**
 * The main page for processing system resets.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Reset extends PageAdapter
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
        $data = $this->readyQueries()->accountInfo()->reset($this->config['reset'], isset($_POST['reset']) ? true : false);

        return $data;
    }
}