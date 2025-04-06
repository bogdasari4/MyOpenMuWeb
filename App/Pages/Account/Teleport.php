<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages\Account;

use App\Alert;
use App\Core\Adapter\PageAdapter;
use App\Core\Component\ConfigLoader;

/**
 * The class controls the movement of the selected character.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Teleport extends PageAdapter
{

    use ConfigLoader;

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
        $data = [];

        if(isset($_POST['teleport'])) {
            $teleport = $_POST['teleport'];

            if($this->readyQueries()->accountInfo()->teleport($teleport))
                throw new Alert(0x21c, 'success', '/account/teleport');

            throw new Alert(0x39d, 'info', '/account/teleport');
        }

        $gameMap = $this->configLoader('openmu.game_map');
        foreach($gameMap as $id => $map) {
            if($map['teleport']['status'])
                $data['gamemap'][] = ['id' => $id, 'name' => $map['name']];
        }

        return $data;
    }
}