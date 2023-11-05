<?php

namespace App\Pages;

use App\Util;
use APP\Core\PostgreSQL\Query;
use Symfony\Contracts\Cache\ItemInterface;

class Character {

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

        if(!isset($_GET['subpage']) || $_GET['subpage'] == '') Util::redirect('/ranking');

        $this->config = [
            'body' => Util::config('body'),
            'openmu' => Util::config('openmu')
        ];
        
        $this->data['page'] = [
            'character' => [
                'text' => __LANG['body']['page']['character'],
                'info' => []
            ]
        ];

        $this->data['page']['character']['info'] = Util::cache($this->config['body']['page']['character']['cache']['subdir'])->get(
            sprintf($this->config['body']['page']['character']['cache']['name'], Util::trimSChars($_GET['subpage'])),
            function(ItemInterface $item) {
                $item->expiresAfter($this->config['body']['page']['character']['cache']['lifetime']);

                if($charData = Query::getRow(
                    'SELECT "Id", "Name", "CharacterClassId", "CurrentMapId", "PositionX", "PositionY"
                    FROM data."Character"
                    WHERE "Name" = :name
                    ',
                    [
                        'name' => Util::trimSChars($_GET['subpage'])
                    ]
                )) {
                    if($charStat = Query::getRowAll(
                        'SELECT "DefinitionId", "Value"
                        FROM data."StatAttribute"
                        WHERE "CharacterId" = :id
                        ',
                        [
                            'id' => $charData[0]
                        ]
                    )) {
                        $data = [
                            'name' => $charData[1],
                            'class' => [
                                'id' => $this->config['openmu']['character']['class'][$charData[2]]['number'],
                                'name' => $this->config['openmu']['character']['class'][$charData[2]]['name']
                            ],
                            'location' => [
                                'map' => $this->config['openmu']['game_map'][$charData[3]]['name'],
                                'x' => $charData[4],
                                'y' => $charData[5]
                            ],
                            'guild' => false
                        ];

                        foreach($this->config['openmu']['attribute_definition'] as $key => $attr) {
                            foreach($charStat as $stat) {
                                if($attr == $stat[0]) {
                                    $data['stats'][$key] = $stat[1];
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
                            $data['guild'] = [
                                'name' => $charGuild[0],
                                'logo' => Util::binaryToImageGuildLogo($charGuild[1], 16)
                            ];
                        }

                    }

                    return $data;
                }
            }
        );

        if($this->data['page']['character']['info'] == null) {
            Util::cache($this->config['body']['page']['character']['cache']['subdir'])->deleteItem(sprintf($this->config['body']['page']['character']['cache']['name'], Util::trimSChars($_GET['subpage'])));
            Util::redirect('/ranking');
        }
    }

}

?>