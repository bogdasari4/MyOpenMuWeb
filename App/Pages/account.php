<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Alert;
use App\Assistant;
use App\Core\Adapter\PageAdapter;
use App\Util;

/**
 * Page for account management and menu provision.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Account extends PageAdapter
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
        if (!isset($this->session->user))
            Util::redirect();

        $subpageName = $this->spotGET('subpage', 'information');

        $data['text'] = @__LANG['body']['page']['account'][$subpageName];

        foreach ($this->controller('MenuController')->account as $keyType => $type) {
            if (array_key_exists($subpageName, $type)) {
                if ($keyType == 'character' && !isset($this->session->character)) {
                    throw new Alert(0x3c9, 'info');
                }

                foreach ($type as $keyValue => $value) {
                    $data['nav'][$keyValue] = [
                        'active' => $subpageName == $keyValue ? 'active' : '',
                        'link' => $value['link'],
                        'name' => __LANG['body']['page']['account']['nav'][$keyType][$keyValue]
                    ];
                }
                break;
            }
        }

        return $data;
    }
}
?>