<?php

namespace App\Pages;

use App\Util;
use App\Core\PostgreSQL\Query;

class About {

    
    /**
     * Summary of data
     * @var array
     */
    private $data = ['page' => []];

    /**
     * Summary of __get
     * @param string $info
     * @return array
     */
    public function __get(string $info): array {
        $this->setInfo();
        return $this->data[$info];
    }

    /**
     * Summary of getInfo
     * @return About
     */
    public static function getInfo() {
        $info = new About;
        return $info;
    }

    /**
     * Summary of setInfo
     * @return void
     */
    private function setInfo(): void {


        $config = Util::config('body');

        $cache['serverInfo'] = Util::readCache($config['block']['serverInfo']['cache']);
        unset($cache['serverInfo'][0]);

        $this->data['page'] = [
            'about' => [
                'text' => __LANG['body']['page']['about'],
                'serverInfo' => $cache['serverInfo']
            ]
        ];

        $cache['about'] = Util::readCache($config['page']['about']['cache']);
        if($cache['about'][0][0] < time() - $config['page']['about']['cache']['lifetime']) {
            $gcID = $data = [];
            $i = 1;
            foreach($cache['serverInfo'] as $server) {
                if(!in_array($server[3], $gcID)) {
                    $gcID[] = $server[3];
                    $gc[$server[3]] = Query::getRow(
                    'SELECT "MaximumLevel", 
                            "MaximumMasterLevel",
                            "ExperienceRate",
                            "MinimumMonsterLevelForMasterExperience",
                            "MaximumInventoryMoney",
                            "MaximumVaultMoney",
                            "MaximumCharactersPerAccount",
                            "MaximumPartySize",
                            "ItemDropDuration"
                    FROM config."GameConfiguration"
                    WHERE "Id" = :id
                    ', [
                        'id' => $server[3]
                    ]);
                }

                $data[$i++] = $gc[$server[3]];
            }
            Util::writeCache(
                $config['page']['about']['cache'],
                $data
            );
        }
        
        $this->data['page']['about']['text']['lifetime'] = sprintf($this->data['page']['about']['text']['lifetime'], date('H:i', $cache['about'][0][0]));
        unset($cache['about'][0]);
        $this->data['page']['about']['info'] = $cache['about'] ? $cache['about'] : $data;
    }

}

?>