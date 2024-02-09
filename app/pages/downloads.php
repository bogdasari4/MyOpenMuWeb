<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Core\Adapter\PageAdapter;

/**
 * The page displays a list of files to download.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Downloads extends PageAdapter
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
        $data['text'] = __LANG['body']['page']['downloads'];
        $data['files'] = $this->config['files'];

        return $data;
    }

}

?>