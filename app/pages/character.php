<?php

namespace App\Pages;

use App\Util;
use APP\Core\PostgreSQL\Query;

class Character {

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
     * @return Character
     */
    public static function getInfo() {
        $info = new Character;
        return $info;
    }

    /**
     * Summary of setInfo
     * @return void
     */
    private function setInfo(): void {

        if(!isset($_GET['subpage']) || $_GET['subpage'] == '') Util::redirect('/ranking');
        $char[1][] = Util::trimSChars($_GET['subpage']);

        $config  = [
            'body' => Util::config('body'),
            'openmu' => Util::config('openmu')
        ];
        
        $config['body']['page']['character']['cache']['name'] = sprintf($config['body']['page']['character']['cache']['name'], $char[1][0]);
        $cache = Util::readCache($config['body']['page']['character']['cache']);

        $this->data['page']['character']['text'] = __LANG['body']['page']['character'];
        $this->data['page']['character']['text']['lifetime'] = sprintf($this->data['page']['character']['text']['lifetime'], date('H:i', $cache[0][0]));

        if($cache[0][0] < time() - $config['body']['page']['character']['cache']['lifetime']) {
            if($charData = Query::getRow(
                'SELECT "Id", "CharacterClassId", "CurrentMapId", "PositionX", "PositionY"
                FROM data."Character"
                WHERE "Name" = :name
                ',
                [
                    'name' => $char[1][0]
                ]
            )) {
                $charStat = Query::getRowAll(
                    'SELECT "DefinitionId", "Value"
                    FROM data."StatAttribute"
                    WHERE "CharacterId" = :id
                    ',
                    [
                        'id' => $charData[0]
                    ]
                );
                
                $char[1][] = $config['openmu']['character']['class'][$charData[1]]['name'];
                $char[1][] = $config['openmu']['character']['class'][$charData[1]]['number'];

                $char[1][] = $config['openmu']['game_map'][$charData[2]]['name'];
                $char[1][] = $charData[3];
                $char[1][] = $charData[4];

                foreach($config['openmu']['attribute_definition'] as $attr) {
                    foreach($charStat as $stat) {
                        if($attr == $stat[0]) {
                            $char[2][] = $stat[1];
                        }
                    }
                }

                if($charGuild = Query::getRow(
                    'SELECT guild."Name", guild."Logo"::TEXT
                    FROM guild."GuildMember" guildmember, guild."Guild" guild
                    WHERE guildmember."Id" = :id
                    ',
                    [
                        'id' => $charData[0]
                    ]
                )) {
                    $char[3][] = $charGuild[0];
                    $char[3][] = $charGuild[1];
                }

                $char[3][1] = isset($char[3][0]) ? Util::binaryToImageGuildLogo($char[3][1], 16) : 'Не в гильдии';
                
                Util::writeCache(
                    $config['body']['page']['character']['cache'],
                    $char
                );
            }
        }

        unset($cache[0]);
        $this->data['page']['character']['info'] = $cache ? $cache : $char;
    }

}

?>