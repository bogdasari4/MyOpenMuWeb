<?php

namespace App\Pages;

use App\Util;
use App\Util\Cache;
use App\Core\PostgreSQL\Query;
use Symfony\Contracts\Cache\ItemInterface;

class Ranking {

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
     * Stores 'subpage' data.
     * @var string
     */
    private string $subpage = '';

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
            'body' => Util::config('body'),
            'openmu' => Util::config('openmu')
        ];

        $this->subpage = (isset($_GET['subpage']) && $_GET['subpage'] != '' && (isset($this->config['body']['page']['ranking'][$_GET['subpage']]))) ? $_GET['subpage'] : 'character';
        $this->subpage = strtolower($this->subpage);
        $this->subpage = Util::trimSChars($this->subpage);

        $this->data['page'] = [
            'ranking' => [
                'config' => [
                    'subpage' => $this->subpage
                ],
                $this->subpage => [
                    'text' => __LANG['body']['page']['ranking'][$this->subpage]
                ]
            ]
        ];

        foreach($this->config['body']['page']['ranking'] as $key => $nav) {
            $this->data['page']['ranking']['menu'][] = [
                'active' => ($this->subpage == $key) ? 'active' : '',
                'link' => '/ranking/' . $key,
                'name' => __LANG['body']['page']['ranking'][$key]['subtitle']
            ];
        }
        
        $this->data['page']['ranking'][$this->subpage]['row'] = Util::cache($this->config['body']['page']['ranking'][$this->subpage]['cache']['subdir'])->get(
            $this->config['body']['page']['ranking'][$this->subpage]['cache']['name'],
            function(ItemInterface $item) {
                $item->expiresAfter($this->config['body']['page']['ranking'][$this->subpage]['cache']['lifetime']);
                return $this->{$this->subpage}();
            }
        );
    }

    /**
     * Private function: character
     * Used only within its class.
     * @return array
     * We get character data from the required number of lines in the request.
     */
    private function character(): array {
            $chars = Query::getRowAll(
                'SELECT 
                    character."Name", 
                    character."CharacterClassId",
                    COALESCE(r."Value", 0) AS resets,
                    COALESCE(l."Value", 0) AS levels
                 FROM data."Character" character
                    left outer join "data"."StatAttribute" r ON character."Id" = r."CharacterId" AND r."DefinitionId" = :resets
                    left outer join "data"."StatAttribute" l ON character."Id" = l."CharacterId" AND l."DefinitionId" = :level
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

    /**
     * Private function: guild
     * Used only within its class.
     * @return array
     * We get data about the guild from the required number of lines in the request.
     */
    private function guild(): array {
        $guilds = Query::getRowAll(
            'SELECT guild."Id", guild."Name", guild."Logo"::TEXT, guild."Score", COUNT(guildmember."Id")
             FROM guild."Guild" guild, guild."GuildMember" guildmember
             WHERE guildmember."GuildId" = guild."Id"
             GROUP BY (guild."Id")
             ORDER BY guild."Score" DESC
             LIMIT :limit
            ',
            [
                'limit' => $this->config['body']['page']['ranking']['guild']['row']
            ]
        );

        $i = 1;
        foreach($guilds as $guild) {
            $data[] = [
                'rank' => $i++,
                'name' => $guild[1],
                'logo' => Util::binaryToImageGuildLogo($guild[2], 16),
                'score' => $guild[3],
                'guildmember' => $guild[4]

            ];
        }

        return $data;
    }
}

?>