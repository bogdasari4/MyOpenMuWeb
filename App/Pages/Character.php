<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Core\Component\{FormattedGet, RedirectTo};
use App\Core\Adapter\PageAdapter;

/**
 * Page for displaying information about the character.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Character extends PageAdapter
{
    use RedirectTo, FormattedGet;

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
        $charName = $this->formattedGet('subpage', '', false);
        if ($charName == '')
            $this->redirectTo('/ranking');

        $this->config['cache']['setName'] = sprintf($this->config['cache']['setName'], $charName);

        $data['text'] = __LANG['body']['page']['character'];
        $data['info'] = $this->cache()->get(function (array $config) {
            return $this->readyQueries()->characterInfo()->fullCharacterInfo(substr($config['cache']['setName'], 6));
        });

        if (!$data['info'])
            $this->redirectTo('/ranking');


        return $data;
    }
}

?>
