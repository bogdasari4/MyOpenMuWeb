<?php

namespace App\Core\Template;

use App\Core\PostgreSQL\Query;
use App\Util;
use Symfony\Contracts\Cache\ItemInterface;

class Block {

    /**
     * Summary of config
     * @var string
     */
    private array $config = [];

    /**
     * Protected function: accountMenu
     * Used in Body class.
     * @return array
     * We display the data of the authorization block or user menu.
     */
    protected function accountMenu(): array {
        $data = [];

        if(isset($_SESSION['user']['isLogin'])) {
            $data = [
                'text' => __LANG['body']['block']['accountMenu']
            ];

            $config = Util::config('body');
            $data['menu'] = $config['block']['accountMenu']['menu'];

            $data['text']['menu'][0]['subheader'] = $_SESSION['user']['loginName'];
            $data['text']['menu'][1]['subheader'] = isset($_SESSION['user']['character']) && ($_SESSION['user']['character']['name'] != '') ? $_SESSION['user']['character']['name'] : $data['text']['menu'][1]['noСharacters'];
        }
        return $data;
    }
    
    /**
     * Protected function: serverInfo.
     * Used in Body class.
     * @param array $config
     * Get block configs.
     * 
     * @return array
     * We get information about the server from the GameServerDefinition and GameServerEndpoint table 
     * and check the status using the parseStatusServer or parseAPIServer function.
     */
    protected function serverInfo(array $config): array {
        $this->config = [
            'block' => $config
        ];

        $data = [
            'text' => __LANG['body']['block']['serverInfo'],
            'row' => []
        ];
        
        $data['row'] = Util::cache()->get(
            $this->config['block']['cache']['name'],
            function(ItemInterface $item) {
                $item->expiresAfter($this->config['block']['cache']['lifetime']);

                $serverList = Query::getRowAll(
                    'SELECT gameserverdefinition."ServerID", gameserverendpoint."NetworkPort", gameserverdefinition."Description", gameserverdefinition."ExperienceRate", gameserverdefinition."GameConfigurationId"
                    FROM config."GameServerDefinition" gameserverdefinition, config."GameServerEndpoint" gameserverendpoint
                    WHERE gameserverendpoint."GameServerDefinitionId" = gameserverdefinition."Id"
                    GROUP BY gameserverdefinition."ServerID", gameserverendpoint."NetworkPort", gameserverdefinition."Description", gameserverdefinition."ExperienceRate", gameserverdefinition."GameConfigurationId"'
                );
                
                if($this->config['block']['apiParse']) {
                    $apiStats = Util::parseAPIServer();
                }

                foreach($serverList as $server) {
                    if($this->config['block']['apiParse'] && $apiStats) {
                        $data[$server[0]] = $apiStats[$server[0]];
                    } else {
                        $data[$server[0]]['status'] = Util::parseStatusServer($this->config['block']['parse'][$server[1]]) ? 1 : 0;
                    }

                    $data[$server[0]]['serverid'] = $server[0];
                    $data[$server[0]]['name'] = $server[2];
                    $data[$server[0]]['experience'] = $server[3];
                    $data[$server[0]]['configuration'] = $server[4];
                }

                return $data;
            }
        );
        return $data;
    }

    /**
     * Protected function siginForm.
     * User Authorization Form.
     * Used in Body class.
     * @return array
     */
    protected function siginForm(): array {

        $config = Util::config('body');

        $data = [
            'text' => __LANG['body']['block']['siginForm'],
            'config' => $config['page']['sigin']['validator']
        ];
        
        return $data;
    }

    /**
     * Protected function rankingInfo
     * Used in Body class.
     * @param array $config
     * Get block configs.
     * Reading the character ranking cache.
     * 
     * @return array
     * We get the first five lines.
     */
    protected function rankingInfo(array $config): array {

        $this->config = [
            'block' => $config,
            'openmu' => Util::config('openmu')
        ];

        $data = [
            'text' => __LANG['body']['block']['rankingInfo'],
            'row' => []
        ];

        $cache = Util::cache($this->config['block']['cache']['subdir'])->get(
            $this->config['block']['cache']['name'],
            function(ItemInterface $item) {
                $item->expiresAfter($this->config['block']['cache']['lifetime']);
                
                $this->config['body'] = Util::config('body');

                $chars = Query::getRowAll(
                    'SELECT c."Name" , c."CharacterClassId",
                        COALESCE(r."Value", 0) AS resets,
                        COALESCE(l."Value", 0) AS levels
                     FROM data."Character" c
                        left outer join "data"."StatAttribute" r ON c."Id" = r."CharacterId" AND r."DefinitionId" = :resets
                        left outer join "data"."StatAttribute" l ON c."Id" = l."CharacterId" AND l."DefinitionId" = :level
                    ORDER BY resets DESC, levels DESC
                    LIMIT :limit
                     ',
                     [
                        'resets' => $this->config['openmu']['attribute_definition']['resets'],
                        'level' => $this->config['openmu']['attribute_definition']['level'],
                        'limit' => $this->config['body']['page']['ranking']['character']['row']
                     ]
                );
                $i = 1;
                foreach($chars as $char) {
                    $data[] = [
                        'rank' => $i++,
                        'name' => $char[0],
                        'class' => [
                            'name' => $this->config['openmu']['character']['class'][$char[1]]['name'],
                            'id' => $this->config['openmu']['character']['class'][$char[1]]['number']
                        ],
                        'level' => $char[3],
                        'reset' => $char[2],
                    ];
                }

                return $data;
            }
        );

        for($i = 0; $i < $this->config['block']['row']; $i++) {
            $data['row'][] = $cache[$i];
        }
        return $data;
    }
}

?>