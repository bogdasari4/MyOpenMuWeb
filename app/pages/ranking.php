<?php

namespace App\Pages;

use App\Util;
use App\Core\PostgreSQL\Query;

class Ranking {

    /**
     * Summary of data
     * @var array
     */
    private $data = [];

    /**
     * Summary of config
     * @var array
     */
    private $config = [];

    public function __get($info) {
        $this->setInfo();
        return $this->data[$info];
    }

    public static function getInfo() {
        $info = new Ranking;
        return $info;
    }

    private function setInfo(): void {

        $this->config = Util::config();

        $subpage = (isset($_GET['subpage']) && $_GET['subpage'] != '' && (isset($this->config['body']['page']['ranking'][$_GET['subpage']]))) ? $_GET['subpage'] : 'character';
        $subpage = strtolower($subpage);
        $subpage = Util::trimSChars($subpage);

        $cache = Util::readCache($this->config['body']['page']['ranking'][$subpage]['cache']);

        $this->data['page'] = [
            'ranking' => [
                'config' => [
                    'subpage' => $subpage
                ],
                $subpage => [
                    'text' => __LANG['body']['page']['ranking'][$subpage]
                ]
            ]
        ];
        $this->data['page']['ranking'][$subpage]['text']['lifetime'] = sprintf($this->data['page']['ranking'][$subpage]['text']['lifetime'], date('H:i', $cache[0][0]));
        foreach($this->config['body']['page']['ranking'] as $key => $nav) {
            $this->data['page']['ranking']['menu'][] = [
                'active' => ($subpage == $key) ? 'active' : '',
                'link' => '/ranking/' . $key,
                'name' => __LANG['body']['page']['ranking'][$key]['subtitle']
            ];
        }

        if($cache[0][0] < time() - $this->config['body']['page']['ranking'][$subpage]['cache']['lifetime']) {
            $data = $this->{$subpage}();

            Util::writeCache(
                $this->config['body']['page']['ranking'][$subpage]['cache'],
                $data
            );
        }

        unset($cache[0]);
        $this->data['page']['ranking'][$subpage]['row'] = $cache ? $cache : $data;
    }

    private function character(): array {

            $chars = Query::getRowAll(
                'SELECT character."Name", character."CharacterClassId", statattribute."Value"
                FROM data."Character" character, data."StatAttribute" statattribute 
                WHERE character."Id" = statattribute."CharacterId" 
                AND statattribute."DefinitionId" = :level 
                ORDER BY statattribute."Value" DESC
                LIMIT :limit', 
            [
                'level' => $this->config['openmu']['attribute_definition']['level'],
                'limit' => $this->config['body']['page']['ranking']['character']['row']
            ]);

            $i = 1;
            foreach($chars as $char) {
                $data[$i++] = [
                    $char[0],
                    $this->config['openmu']['character']['class'][$char[1]]['name'],
                    $char[2],
                    $this->config['openmu']['character']['class'][$char[1]]['number']
                ];
            }

            return $data;
    }

    private function guild(): array {
        $guilds = Query::getRowAll(
            'SELECT guild."Id", guild."Name", guild."Logo"::TEXT, guild."Score", COUNT(guildmember."Id")
             FROM guild."Guild" guild, guild."GuildMember" guildmember
             WHERE guildmember."GuildId" = guild."Id"
             GROUP BY(guild."Id")
             ORDER BY guild."Score" DESC
             LIMIT :limit
            ',
            [
                'limit' => $this->config['body']['page']['ranking']['guild']['row']
            ]
        );

        $i = 1;
        foreach($guilds as $guild) {
            $data[$i++] = [
                $guild[1],
                Util::binaryToImageGuildLogo($guild[2], 16),
                $guild[3],
                $guild[4]

            ];
        }

        return $data;
    }
}

?>