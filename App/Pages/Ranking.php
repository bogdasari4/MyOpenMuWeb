<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Core\Adapter\PageAdapter;
use App\Core\Component\FormattedGet;

/**
 * Ranking page. Used to display the menu and all available ranking.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Ranking extends PageAdapter
{
    use FormattedGet;

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
        $subpageName = $this->formattedGet('subpage', 'character');

        $data['text'] = @__LANG['body']['page']['ranking'][$subpageName];

        foreach ($this->app->menucontroller->ranking as $key => $nav) {
            $data['nav'][$key] = [
                'active' => $subpageName == $key ? 'active' : '',
                'link' => $nav['link'],
                'name' => $nav['title'] ?? __LANG['body']['page']['ranking'][$key]['subtitle']
            ];
        }

        return $data;
    }
}
?>
