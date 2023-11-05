<?php

namespace App\Pages;

use App\Util;
use App\Core\PostgreSQL\Query;
use Symfony\Contracts\Cache\ItemInterface;

class Guild {

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

        if(!isset($_GET['subpage']) || $_GET['subpage'] == '') Util::redirect('/ranking/guild');

        $this->config = [
            'body' => Util::config('body'),
            'openmu' => Util::config('openmu')
        ];

        $this->data['page'] = [
            'guild' => [
                'text' => __LANG['body']['page']['guild'],
                'info' => []
            ]
        ];

        $this->data['page']['guild']['info'] = Util::cache($this->config['body']['page']['guild']['cache']['subdir'])->get(
            sprintf($this->config['body']['page']['guild']['cache']['name'], Util::trimSChars($_GET['subpage'])),
            function(ItemInterface $item) {
                $item->expiresAfter($this->config['body']['page']['guild']['cache']['lifetime']);

                if($guild = Query::getRow('SELECT "Id", "AllianceGuildId", "Name", "Logo"::TEXT, "Score" FROM guild."Guild" WHERE "Name" = :name', [ 'name' => Util::trimSChars($_GET['subpage'])])) {
                    $data = [
                        'name' => $guild[2],
                        'logo' => Util::binaryToImageGuildLogo($guild[3], 128),
                        'score' => $guild[4]
                    ];

                    $i = 1;
                    foreach(Query::getRowAll(
                        'SELECT 
                            character."Name", 
                            character."CharacterClassId", 
                            COALESCE(l."Value", 0) AS level, 
                            COALESCE(r."Value", 0) AS reset, 
                            guildmember."Status"
                        FROM guild."GuildMember" guildmember, data."Character" character
                            left outer join "data"."StatAttribute" l ON character."Id" = l."CharacterId" AND l."DefinitionId" = :level
                            left outer join "data"."StatAttribute" r ON character."Id" = r."CharacterId" AND r."DefinitionId" = :resets
                        WHERE guildmember."GuildId" = :guild
                        AND character."Id" = guildmember."Id"
                        ORDER BY guildmember."Status" DESC
                        ',
                        [
                            'level' => $this->config['openmu']['attribute_definition']['level'],
                            'resets' => $this->config['openmu']['attribute_definition']['resets'],
                            'guild' => $guild[0]
                        ]
                        ) as $char) {
                            $data['members'][] = [
                                'rank' => $i++,
                                'name' => $char[0],
                                'class' => [
                                    'id' => $this->config['openmu']['character']['class'][$char[1]]['number'],
                                    'name' => $this->config['openmu']['character']['class'][$char[1]]['name']
                                ],
                                'stats' => [
                                    'level' => $char[2],
                                    'reset' => $char[3]
                                ],
                                'status' => __LANG['body']['page']['guild']['status'][$char[4]]
                            ];

                    }

                    $data['master'] = $data['members'][0]['name'];

                    if($guild[1] != null) {

                    }

                    return $data;
                }
            }
        );

        if($this->data['page']['guild']['info'] == null) {
            Util::cache($this->config['body']['page']['guild']['cache']['subdir'])->deleteItem(sprintf($this->config['body']['page']['guild']['cache']['name'], Util::trimSChars($_GET['subpage'])));
            Util::redirect('/ranking/guild');
        }
    }
}

?>