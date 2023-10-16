<?php

namespace App\Pages;

use App\Util;
use App\Core\PostgreSQL\Query;

class Guild {

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
     * @return Guild
     */
    public static function getInfo() {
        $info = new Guild;
        return $info;
    }

    /**
     * Summary of setInfo
     * @return void
     */
    private function setInfo(): void {

        if(!isset($_GET['subpage']) || $_GET['subpage'] == '') Util::redirect('/ranking/guild');
        $guildName = Util::trimSChars($_GET['subpage']);

        $this->data['page'] = [
            'guild' => [
                'text' => __LANG['body']['page']['guild']
            ]
        ];

        $config  = [
            'body' => Util::config('body'),
            'openmu' => Util::config('openmu')
        ];
        
        $config['body']['page']['guild']['cache']['name'] = sprintf($config['body']['page']['guild']['cache']['name'], $guildName);

        $cache = Util::readCache($config['body']['page']['guild']['cache']);
        $this->data['page']['guild']['text']['lifetime'] = sprintf($this->data['page']['guild']['text']['lifetime'], date('H:i', $cache[0][0]));

        if($cache[0][0] < time() - $config['body']['page']['guild']['cache']['lifetime']) {
            if($guild = Query::getRow(
                'SELECT "Id", "AllianceGuildId", "Name", "Logo"::TEXT, "Score"
                 FROM guild."Guild"
                 WHERE "Name" = :name
                ',
                [
                    'name' => $guildName
                ]
                )) {
                    $data[1] = [
                        $guild[2],
                        Util::binaryToImageGuildLogo($guild[3], 128),
                        $guild[4],
                        0
                    ];

                    foreach(Query::getRowAll(
                        'SELECT character."Name", character."CharacterClassId", statattribute."Value", guildmember."Status"
                        FROM guild."GuildMember" guildmember, data."Character" character, data."StatAttribute" statattribute
                        WHERE guildmember."GuildId" = :guild
                        AND character."Id" = guildmember."Id"
                        AND statattribute."CharacterId" = character."Id"
                        AND statattribute."DefinitionId" = :level
                        ORDER BY guildmember."Status" DESC
                        ',
                        [
                            'guild' => $guild[0],
                            'level' => $config['openmu']['attribute_definition']['level']
                        ]
                        ) as $char) {
                            $data[] = [
                                $char[0],
                                $config['openmu']['character']['class'][$char[1]]['name'],
                                $config['openmu']['character']['class'][$char[1]]['number'],
                                $char[2],
                                __LANG['body']['page']['guild']['status'][$char[3]]
                            ];

                            $data[1][3]++;

                    }
                    if($guild[1] != null) {

                    }

                    Util::writeCache(
                        $config['body']['page']['guild']['cache'],
                        $data
                    );
                }
        }

        unset($cache[0]);
        $this->data['page']['guild']['info'] = $cache ? $cache : $data;
    }
}

?>