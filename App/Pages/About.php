<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Core\Adapter\PageAdapter;

/**
 * The `About` page is designed to display information for each server.
 * We read the cache of the `serverInfo` block template,
 * using the `configurationID` keys we display information about each server.
 * If the `serverInfo` cache does not exist, create it.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class About extends PageAdapter
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
        $data['text'] = __LANG['body']['page']['about'];
        $data['info'] = $this->cache()->get(function (array $config): array {
            $serverInfoCache = $this->cache()->getInfo();
            if(!$serverInfoCache->isHit()) {
                $serverInfoCache->set($this->readyQueries()->serverInfo()->sideServerInfo());
                $this->cache()->save($serverInfoCache);
            }

            foreach($serverInfoCache->get() as $value) {

                $gameServerID = [];

                $data[$value['serverid']] = $value;

                if (!in_array($value['configuration'], $gameServerID)) {
                    $gameServerID[] = $value['configuration'];
                    $gameConfiguration[$value['configuration']] = $this->readyQueries()->serverInfo()->serverConfigurationInfo($value['configuration']);
                }

                $data[$value['serverid']]['configuration'] = $gameConfiguration[$value['configuration']];
            }

            return $data;
        });

        return $data;
    }
}

?>
