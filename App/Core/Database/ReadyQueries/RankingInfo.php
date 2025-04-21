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
final class RankingInfo extends DBAdapter
{

    use ConfigLoader;

    /**
     * We create a character ranking by sorting by reset and level.
     * @param mixed $config
     * Character Rating Page Configuration
     * @return array{class: array, level: mixed, name: mixed, rank: int, reset: mixed[]}
     */
    public function character(?array $config = null): array
    {
        $result = [];

        if($config === null) {
            $body = $this->configLoader('body');
            $config = $body['page']['ranking']['character'];;
        }

        $attributeDefinition = $this->configLoader('openmu.attribute_definition');

        $sql = 'SELECT c."Name", c."CharacterClassId", COALESCE(resets_attr."Value", 0) AS resets, COALESCE(levels_attr."Value", 0) AS levels
                FROM data."Character" c
                LEFT OUTER JOIN data."StatAttribute" resets_attr ON c."Id" = resets_attr."CharacterId" AND resets_attr."DefinitionId" = :resets
                LEFT OUTER JOIN data."StatAttribute" levels_attr ON c."Id" = levels_attr."CharacterId" AND levels_attr."DefinitionId" = :level
                ORDER BY resets DESC, levels DESC
                LIMIT :limit';

        $chars = $this->queryBuilder()->exec($sql, ['resets' => $attributeDefinition['resets'], 'level' => $attributeDefinition['level'], 'limit' => $config['row']]);
        
        if(!empty($chars)) {
            $rank = 1;
            $characteClass = $this->configLoader('openmu.character_class');

            foreach($chars as $char) {
                $result[] = [
                    'rank' => $rank++,
                    'name' => $char[0],
                    'class' => [
                        'name' => $characteClass[$char[1]]['name'],
                        'id' => $characteClass[$char[1]]['number']
                    ],
                    'level' => $char[3],
                    'reset' => $char[2],
                ];
            }
        }

        return $result;
    }

    /**
     * We create a guild ranking by sorting by score.
     * @param mixed $config
     * Guild Ranking Page Configuration
     * @return array{guildmember: mixed, logo: string, name: mixed, rank: int, score: mixed[]}
     */
    public function guild(?array $config = null)
    {
        $result = [];

        if($config === null) {
            $body = $this->configLoader('body');
            $config = $body['page']['ranking']['guild'];;
        }

        $sql = 'SELECT guilds."Id", guilds."Name", guilds."Logo"::TEXT AS logo_text, guilds."Score", COUNT(members."Id") AS member_count
                FROM guild."Guild" guilds
                INNER JOIN guild."GuildMember" members ON members."GuildId" = guilds."Id"
                GROUP BY guilds."Id"
                ORDER BY guilds."Score" DESC
                LIMIT :limit';

        $guilds = $this->queryBuilder()->exec($sql, ['limit' => $config['row']]);

        if (!empty($guilds)) {
            $rank = 1;
            foreach ($guilds as $guild) {
                $result[] = [
                    'rank' => $rank++,
                    'name' => $guild[1],
                    'logo' => $this->binaryToImageGuildLogo($guild[2], 16),
                    'score' => $guild[3],
                    'guildmember' => $guild[4]

                ];
            }
        }
            
        return $result;
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
        return '<img src="/api/guildmark?data=' . $binary . '&size=' . urlencode($size) . '" width="' . $size . '" height="' . $size . '"/>';
    }

}