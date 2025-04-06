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
final class GuildInfo extends DBAdapter
{
    use ConfigLoader;

    /**
     * Get complete information about the guild and its composition.
     * @param string $guildname
     * Guild name to search for.
     * @return array[]|array{logo: string, members: array, name: mixed, score: mixed}
     */
    public function fullGuildInforamtion(string $guildname): array
    {
        $result = [];

        $guild = $this->getDBGuildInfo($guildname);

        if(!empty($guild)) {
            $result = [
                'name' => $guild[0][2],
                'logo' => $this->binaryToImageGuildLogo($guild[0][3], 128),
                'score' => $guild[0][4],
                'members' => []
            ];

            $characteClass = $this->configLoader('openmu.character_class');
            $rank = 1;

            foreach($guild as $members) {

                if($rank == 1)
                    $result['master'] = $members[5];

                $result['members'][] = [
                    'rank' => $rank++,
                    'name' => $members[5],
                    'class' => [
                        'id' => $characteClass[$members[6]]['number'],
                        'name' => $characteClass[$members[6]]['name']
                    ],
                    'stats' => [
                        'level' => $members[7],
                        'resets' => $members[8]
                    ],
                    'status' => __LANG['body']['page']['guild']['status'][$members[9]]
                ];
            }

            if($guild[0][1] !== null)
            {

            }
        }

        return $result;
    }

    /**
     * We get all the data from the database about the guild by its name.
     * @param string $guildname
     * Guild name to search for.
     * @return array
     */
    private function getDBGuildInfo(string $guildname): array
    {
        $sql = 'SELECT g."Id" AS guild_id, g."AllianceGuildId", g."Name" AS guild_name, g."Logo"::TEXT AS logo_text, g."Score", c."Name" AS character_name, c."CharacterClassId", 
                    COALESCE(levels."Value", 0) AS level, COALESCE(resets."Value", 0) AS reset, gm."Status"
                FROM guild."Guild" g 
                INNER JOIN guild."GuildMember" gm ON g."Id" = gm."GuildId"
                INNER JOIN data."Character" c ON c."Id" = gm."Id"
                LEFT OUTER JOIN data."StatAttribute" levels ON c."Id" = levels."CharacterId" AND levels."DefinitionId" = :level
                LEFT OUTER JOIN data."StatAttribute" resets ON c."Id" = resets."CharacterId" AND resets."DefinitionId" = :resets
                WHERE g."Name" = :name
                ORDER BY gm."Status" DESC';

        $attributeDefinition = $this->configLoader('openmu.attribute_definition');

        return $this->queryBuilder()->exec($sql, ['level' => $attributeDefinition['level'], 'resets' => $attributeDefinition['resets'], 'name' => $guildname]);
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