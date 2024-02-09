<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages\Account;

use App\Core\Adapter\PageAdapter;

/**
 * The class controls the distribution of the stat.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class AddStats extends PageAdapter
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
        $data = $this->ready()->getAccountInfo()->addStats($this->config['addstats'], isset($_POST['addstats']) ? $_POST['addstats'] : null);
        $data['config'] = $this->config['addstats'];

        return $data;
    }
}
?>