<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Alert;
use App\Core\Adapter\PageAdapter;
use App\Core\Component\{FormattedGet, RedirectTo};

/**
 * Page for account management and menu provision.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Account extends PageAdapter
{

    use FormattedGet, RedirectTo;

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
            $this->redirectTo();

        $subpageName = $this->formattedGet('subpage', 'information');

        $data['text'] = @__LANG['body']['page']['account'][$subpageName];

        foreach ($this->app->menucontroller->account as $keyType => $type) {
            if (array_key_exists($subpageName, $type)) {
                if ($keyType == 'character' && !isset($this->session->character)) {
                    throw new Alert(0x3c9, 'info');
                }

                foreach ($type as $keyValue => $value) {
                    $data['nav'][$keyValue] = [
                        'active' => $subpageName == $keyValue ? 'active' : '',
                        'link' => $value['link'],
                        'name' => (isset($value['name']) && $value['name'] != '') ? $value['name'] : __LANG['body']['block']['accountMenu']['nav'][$keyType][$keyValue]
                    ];
                }
                break;
            }
        }

        return $data;
    }
}
?>
