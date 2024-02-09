<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages\Account;

use App\Core\Adapter\PageAdapter;
use App\Core\Database\Uuid;

/**
 * The class controls the movement of the selected character.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Teleport extends PageAdapter
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
        $data = $this->ready()->getAccountInfo()->teleport();

        if (isset($_POST['teleport'])) {
            $teleport = $_POST['teleport'];

            $this->ready()->getAccountInfo()->teleport($teleport);
        }

        return $data;
    }
}