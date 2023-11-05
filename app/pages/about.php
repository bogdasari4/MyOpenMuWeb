<?php

namespace App\Pages;

use App\Util;
use App\Core\PostgreSQL\Query;
use Symfony\Contracts\Cache\ItemInterface;

class About {

    
    /**
     * An array of data prepared in this class.
     * @var array
     */
    private array $data = ['page' => []];

    /**
     * Array of configuration in this class.
     * @var array
     */
    private array $config = [];

    /**
     * When the __get() magic method is called, data will be read from this class.
     * @param string $info
     * The parameter takes the value 'page' automatically in the handler class.
     * 
     * @return array
     * We return an array of data.
     */
    public function __get(string $info): array {
        $this->setInfo();
        return $this->data[$info];
    }

    /**
     * Preparing a data array.
     * @return void
     */
    private function setInfo(): void {


        $this->config = [
            'body' => Util::config('body')
        ];
        
        $this->data['page'] = [
            'about' => [
                'text' => __LANG['body']['page']['about'],
                'info' => []
            ]
        ];

        $this->data['page']['about']['info'] = Util::cache()->get(
            $this->config['body']['page']['about']['cache']['name'],
            function(ItemInterface $item) {
                $item->expiresAfter($this->config['body']['page']['about']['cache']['lifetime']);

                $gameServerID = $gameServer = $data = [];
                $serverInfo = Util::cache()->getItem($this->config['body']['block']['serverInfo']['cache']['name']);

                foreach($serverInfo->get() as $value) {

                    $data[$value['serverid']] = $value;

                    if(!in_array($value['configuration'], $gameServerID)) {
                        $gameServerID[] = $value['configuration'];
                        $gameConfiguration = Query::getRow(
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
                                'id' => $value['configuration']
                            ]
                        );

                        $gameServer[$value['configuration']] = [
                            'maxlevel' => $gameConfiguration[0],
                            'maxmasterlevel' => $gameConfiguration[1],
                            'experience' => $gameConfiguration[2],
                            'minmoblevelformasterexp' => $gameConfiguration[3],
                            'maxinvzen' => $gameConfiguration[4],
                            'maxvaultzen' => $gameConfiguration[5],
                            'maxcharacc' => $gameConfiguration[6],
                            'maxpartysize' => $gameConfiguration[7],
                            'itemdur' => $gameConfiguration[8]
                        ];
                    }

                    $data[$value['serverid']]['configuration'] = $gameServer[$value['configuration']];
                }

                return $data;
            }
        );
    }

}

?>