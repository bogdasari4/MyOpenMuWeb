<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database\ReadyQueries;

use App\Core\Adapter\DBAdapter;
use App\Core\Component\ConfigLoader;

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class CharacterInfo extends DBAdapter
{

    use ConfigLoader;

    /**
     * Get full information about a character by his name.
     * @param string $charname
     * Character name.
     * @return array[]|array{class: array{id: mixed, name: mixed, guild: array, location: array{map: mixed, x: mixed, y: mixed}, name: mixed, stats: array}}
     */
    public function fullCharacterInfo(string $charname)
    {
        $result = [];

        $character = $this->getDBCharacterInfo($charname);

        if(!empty($character)) {
            $characterClass = $this->configLoader('openmu.character_class');
            $characterClass = $characterClass[$character[0][2]];

            $gameMap = $this->configLoader('openmu.game_map');

            $result = [
                'name' => $character[0][1],
                'class' => [
                    'id' => $characterClass['number'],
                    'name' => $characterClass['name']
                ],
                'stats' => [],
                'location' => [
                    'map' => $gameMap[$character[0][3]]['name'],
                    'x' => $character[0][4],
                    'y' => $character[0][5]
                ],
                'guild' => []
            ];

            $attributeDefinition = $this->configLoader('openmu.attribute_definition');

            foreach($character as $attrValue) {
                if(($key = array_find_key($attributeDefinition, fn($value): bool => $value == $attrValue[6])) !== null)
                    $result['stats'][$key] = $attrValue[7]; 
            }

            $guild = $this->getDBGuildInfo($character[0][0]);

            if(!empty($guild)) 
                $result['guild'] = [
                    'name' => $guild[0],
                    'logo' => $this->binaryToImageGuildLogo($guild[1], 16)
                ];
            
        }

        return $result;
    }

    /**
     * We get all the information about the character.
     * @param string $charname
     * Search by character name
     * @return array
     */
    private function getDBCharacterInfo(string $charname): array
    {
        $sql = 'SELECT c."Id", c."Name", c."CharacterClassId", c."CurrentMapId", c."PositionX", c."PositionY", sa."DefinitionId", sa."Value"
                FROM data."Character" c
                INNER JOIN data."StatAttribute" sa ON sa."CharacterId" = c."Id"
                WHERE c."Name" = :name';

        return $this->queryBuilder()->exec($sql, ['name' => $charname]);
    }

    /**
     * We get information about the guild of which the character is a member.
     * @param string $charID
     * Search by character id(uuid)
     * @return array
     */
    private function getDBGuildInfo(string $charID)
    {
        $sql = 'SELECT guild."Name", guild."Logo"::TEXT
                FROM guild."GuildMember" guildmember, guild."Guild" guild
                WHERE guildmember."Id" = :id
                AND guild."Id" = guildmember."GuildId"';

        return $this->queryBuilder()->exec($sql, ['id' => $charID], false, false);
    }

    /**
     * Function to get `html` link to render guild logo.
     * @param string $binary
     * A binary string containing the guild logo.
     * @param int $size
     * Proportional image size.
     * @return string
     * Returns an `html` <img> tag with an image of the guild.
     */
    private function binaryToImageGuildLogo(string $binary, int $size = 40): string
    {
        return '<img src="/api/guildmark.php?data=' . $binary . '&size=' . urlencode($size) . '" width="' . $size . '" height="' . $size . '"/>';
    }
}