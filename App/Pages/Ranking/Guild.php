<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages\Ranking;

use App\Core\Adapter\PageAdapter;

/**
 * Guild ranking subpage.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
class Guild extends PageAdapter
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
        $data = $this->cache($this->config['guild'])->get(function (array $config) {
            return $this->readyQueries()->rankingInfo()->guild($config);
        });

        return $data;
    }
}

?>