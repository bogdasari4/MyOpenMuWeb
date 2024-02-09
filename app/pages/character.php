<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Util;
use App\Assistant;
use App\Core\Adapter\PageAdapter;

/**
 * Page for displaying information about the character.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Character extends PageAdapter
{
    use Assistant {
        spotGET as private setInfo;
    }

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
        $charName = $this->spotGET('subpage', '', false);
        if ($charName == '')
            Util::redirect('/ranking');

        $this->config['cache']['setName'] = sprintf($this->config['cache']['setName'], $charName);

        $data['text'] = __LANG['body']['page']['character'];
        $data['info'] = $this->cache()->get(function (array $config) {
            return $this->ready()->getCharacterInfo(substr($config['cache']['setName'], 6));
        });

        if (!$data['info'])
            Util::redirect('/ranking');


        return $data;
    }
}

?>