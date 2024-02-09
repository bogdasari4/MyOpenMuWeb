<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database;

use App\Alert;
use App\Core\Database\Interface\InterfaceReady;
use App\Core\Database\Query;
use App\Util;
use App\Core\Session;

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Ready implements InterfaceReady
{

    private readonly Query $query;
    private array $config;
    private readonly Uuid $uuid;
    private readonly Session $session;

    public function __construct()
    {
        $this->query = new Query;
    }

    /**
     * @param null|array $config
     * Configuration for obtaining information about servers.
     * If the value of `$config` is empty or `null` we use the internal configuration $this->config().
     * @return array
     */
    public function serverInfo(?array $config = null): array
    {
        $result = [];

        if (is_null($config)) {
            $config = $this->config('body');
            $config = $config['block']['serverInfo'];
        }

        $serverList = $this->query->getRowAll(
            'SELECT gameserverdefinition."ServerID", gameserverendpoint."NetworkPort", gameserverdefinition."Description", gameserverdefinition."ExperienceRate", gameserverdefinition."GameConfigurationId"
            FROM config."GameServerDefinition" gameserverdefinition, config."GameServerEndpoint" gameserverendpoint
            WHERE gameserverendpoint."GameServerDefinitionId" = gameserverdefinition."Id"
            GROUP BY gameserverdefinition."ServerID", gameserverendpoint."NetworkPort", gameserverdefinition."Description", gameserverdefinition."ExperienceRate", gameserverdefinition."GameConfigurationId"'
        );

        $apiStats = false;

        if ($config['apiParse'] == true) {
            $apiStats = Util::parseAPIServer($config['api']['url']);
        }


        foreach ($serverList as $server) {
            if ($apiStats !== false) {
                if (isset($apiStats[$server[0]])) {
                    $result[$server[0]] = $apiStats[$server[0]];
                }
            } else {
                $result[$server[0]]['status'] = Util::parseStatusServer($config['parse'][$server[1]]) ? true : false;
            }

            $result[$server[0]]['serverid'] = $server[0];
            $result[$server[0]]['name'] = $server[2];
            $result[$server[0]]['experience'] = $server[3];
            $result[$server[0]]['configuration'] = $server[4];
        }

        return $result;
    }

    /**
     * @param null|array $config
     * If the value of `$config` is empty or `null` we use the internal configuration $this->config().
     * @param string $queryType
     * Ranking type `guild` or `character`.
     * @return array
     */
    public function rankingInfo(?array $config = null, string $queryType = 'character'): array
    {
        $result = [];

        if (is_null($config)) {
            $config = $this->config('body');
            $config = $config['page']['ranking'][$queryType];
        }

        switch ($queryType) {
            case 'guild':
                $guilds = $this->query->getRowAll(
                    'SELECT guild."Id", guild."Name", guild."Logo"::TEXT, guild."Score", COUNT(guildmember."Id")
                     FROM guild."Guild" guild, guild."GuildMember" guildmember
                     WHERE guildmember."GuildId" = guild."Id"
                     GROUP BY (guild."Id")
                     ORDER BY guild."Score" DESC
                     LIMIT :limit
                    ',
                    [
                        'limit' => $config['row']
                    ]
                );

                if ($guilds) {
                    $i = 1;
                    foreach ($guilds as $guild) {
                        $result[] = [
                            'rank' => $i++,
                            'name' => $guild[1],
                            'logo' => Util::binaryToImageGuildLogo($guild[2], 16),
                            'score' => $guild[3],
                            'guildmember' => $guild[4]

                        ];
                    }
                }
                break;

            case 'character':
                $this->config('openmu');

                $chars = $this->query->getRowAll(
                    'SELECT c."Name" , c."CharacterClassId",
                        COALESCE(r."Value", 0) AS resets,
                        COALESCE(l."Value", 0) AS levels
                        FROM data."Character" c
                        left outer join "data"."StatAttribute" r ON c."Id" = r."CharacterId" AND r."DefinitionId" = :resets
                        left outer join "data"."StatAttribute" l ON c."Id" = l."CharacterId" AND l."DefinitionId" = :level
                    ORDER BY resets DESC, levels DESC
                    LIMIT :limit',
                    [
                        'resets' => $this->config['openmu']['attribute_definition']['resets'],
                        'level' => $this->config['openmu']['attribute_definition']['level'],
                        ':limit' => $config['row']
                    ]
                );

                if ($chars) {
                    $i = 1;
                    foreach ($chars as $char) {
                        $result[] = [
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
                }
                break;
        }

        return $result;
    }

    /**
     * @param string $configurationId
     * `uuid` server configuration.
     * @return array
     */
    public function getServerInfo(string $configurationId): array
    {
        $result = [];

        if (
            $gameConfiguration = $this->query->getRow(
                'SELECT "MaximumLevel", 
                    "MaximumMasterLevel",
                    "ExperienceRate",
                    "MinimumMonsterLevelForMasterExperience",
                    "MaximumInventoryMoney",
                    "MaximumVaultMoney",
                    "MaximumCharactersPerAccount",
                    "MaximumPartySize",
                    "ItemDropDuration"
            FROM config."GameConfiguration"
            WHERE "Id" = :id
            ',
                [
                    'id' => $configurationId
                ]
            )
        ) {
            $result = [
                'maxlevel' => $gameConfiguration[0],
                'maxmasterlevel' => $gameConfiguration[1],
                'experience' => $gameConfiguration[2],
                'minmoblevelformasterexp' => $gameConfiguration[3],
                'maxinvzen' => $gameConfiguration[4],
                'maxvaultzen' => $gameConfiguration[5],
                'maxcharacc' => $gameConfiguration[6],
                'maxpartysize' => $gameConfiguration[7],
                'itemdur' => $gameConfiguration[8]
            ];
        }

        return $result;
    }

    /**
     * @param string $charName
     * We accept the name of the character.
     * @return array
     */
    public function getCharacterInfo(string $charName): array
    {
        $result = [1];

        if (
            $charData = $this->query->getRow(
                'SELECT "Id", "Name", "CharacterClassId", "CurrentMapId", "PositionX", "PositionY"
            FROM data."Character"
            WHERE "Name" = :name',
                [
                    'name' => $charName
                ]
            )
        ) {

            if (
                $charStat = $this->query->getRowAll(
                    'SELECT "DefinitionId", "Value"
                FROM data."StatAttribute"
                WHERE "CharacterId" = :id',
                    [
                        'id' => $charData[0]
                    ]
                )
            ) {
                $this->config('openmu');

                $result = [
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

                foreach ($this->config['openmu']['attribute_definition'] as $key => $attr) {
                    foreach ($charStat as $stat) {
                        if ($attr == $stat[0]) {
                            $result['stats'][$key] = $stat[1];
                        }
                    }
                }

                if (
                    $charGuild = $this->query->getRow(
                        'SELECT guild."Name", guild."Logo"::TEXT
                    FROM guild."GuildMember" guildmember, guild."Guild" guild
                    WHERE guildmember."Id" = :id
                    AND guild."Id" = guildmember."GuildId"',
                        [
                            'id' => $charData[0]
                        ]
                    )
                ) {
                    $result['guild'] = [
                        'name' => $charGuild[0],
                        'logo' => Util::binaryToImageGuildLogo($charGuild[1], 16)
                    ];
                }

            }
        }

        return $result;
    }

    /**
     * @param string $guildName
     * We accept the name of the guild.
     * @return array
     */
    public function getGuildInfo(string $guildName): array
    {
        $result = [];

        if (
            $guild = $this->query->getRow(
                'SELECT "Id", "AllianceGuildId", "Name", "Logo"::TEXT, "Score" FROM guild."Guild" 
             WHERE "Name" = :name',
                [
                    'name' => $guildName
                ]
            )
        ) {
            $result = [
                'name' => $guild[2],
                'logo' => Util::binaryToImageGuildLogo($guild[3], 128),
                'score' => $guild[4]
            ];

            $this->config('openmu');

            $i = 1;
            foreach ($this->query->getRowAll('SELECT 
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
                ', ['level' => $this->config['openmu']['attribute_definition']['level'], 'resets' => $this->config['openmu']['attribute_definition']['resets'], 'guild' => $guild[0]]) as $char) {
                $result['members'][] = [
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

            $result['master'] = $result['members'][0]['name'];

            if ($guild[1] != null) {

            }
        }

        return $result;
    }

    /**
     * @param array $data
     * We accept data in the `loginName`, `password`, `email` array to create a request.
     * @return bool
     */
    public function createAccount(array $data): bool
    {
        if (!$this->query->getRow('SELECT * FROM data."Account" WHERE "LoginName" = :loginName', ['loginName' => $data['loginName']])) {
            $vaultUuid = $this->uuid()->generate();
            if ($this->query->insertRow('data."ItemStorage"', ['Id' => $vaultUuid, 'Money' => 0])) {
                $data = [
                    'Id' => $this->uuid()->generate(),
                    'VaultId' => $vaultUuid,
                    'LoginName' => $data['loginName'],
                    'PasswordHash' => password_hash($data['password'], PASSWORD_BCRYPT),
                    'SecurityCode' => '',
                    'EMail' => $data['email'],
                    'RegistrationDate' => date('Y-m-d H:i:s.u O'),
                    'State' => 0,
                    'TimeZone' => 0,
                    'VaultPassword' => '',
                    'IsVaultExtended' => 'false',
                    'ChatBanUntil' => NULL
                ];

                if ($this->query->insertRow('data."Account"', $data))
                    return true;
            }
        }

        return false;
    }

    /**
     * @param array $data
     * Data for checking and creating a session.
     * @return bool
     */
    public function authorization(array $data): bool
    {
        if ($user = $this->query->getRow('SELECT "Id", "LoginName", "PasswordHash", "EMail", "RegistrationDate" FROM data."Account" WHERE "LoginName" = :loginName', ['loginName' => $data['loginName']])) {
            if (password_verify($data['password'], $user[2])) {
                $this->session()->user = [
                    'id' => $user[0],
                    'loginName' => $user[1],
                    'email' => $user[3] ? $user[3] : null,
                    'registrationDate' => $user[4]
                ];
                return true;
            }
        }

        return false;
    }

    public function getAccountInfo()
    {
        return new class ($this->query, $this->session()) {
            protected Session $session;
            protected Query $query;

            public function __construct(object $query, object $session)
            {
                $this->query = $query;
                $this->session = $session;
            }

            public function getCharacter(string $charName): void
            {
                if ($this->session->character['name'] != $charName) {
                    if ($char = $this->query->getRow('SELECT "Id", "Name" FROM data."Character" WHERE "AccountId" = :id AND "Name" = :name', ['id' => $this->session->user['id'], 'name' => $charName])) {
                        $this->session->character = [
                            'id' => $char[0],
                            'name' => $char[1]
                        ];
                        return;
                    }

                    throw new Alert(0x28f, 'danger', '/account');
                }
            }

            public function information(): ?array
            {
                $data = $this->session->user;
                if ($chars = $this->query->getRowAll('SELECT "Id", "Name" FROM data."Character" WHERE "AccountId" = :id', ['id' => $data['id']])) {
                    foreach ($chars as $value) {
                        $data['chars'][] = $value[1];
                    }

                    if (!isset($this->session->character))
                        $this->session->character = [
                            'id' => $chars[0][0],
                            'name' => $chars[0][1]
                        ];
                }
                return $data;
            }

            public function changePass(array $data): bool
            {
                if ($account = $this->query->getRow('SELECT "PasswordHash" FROM data."Account" WHERE "Id" = :id', ['id' => $this->session->user['id']])) {
                    if (password_verify($data['password'], $account[0])) {
                        return $this->query->updateRow('data."Account"', ['PasswordHash' => password_hash($data['newpassword'], PASSWORD_BCRYPT)], ['Id' => $this->session->user['id']]);
                    }
                }
                return false;
            }

            public function teleport(?array $data = null): array|bool
            {
                $result = [];
                if (isset($this->session->character)) {
                    $openmu = Util::config('openmu');

                    if (!$data) {
                        foreach ($openmu['game_map'] as $key => $value) {
                            if ($value['teleport']['status']) {
                                $result['gamemap'][] = [
                                    'id' => $key,
                                    'name' => $value['name']
                                ];
                            }
                        }
                    } else {
                        if (isset($this->session->character)) {
                            if (isset($openmu['game_map'][$data['map']])) {
                                $map = $openmu['game_map'][$data['map']];

                                if ($char = $this->query->getRow('SELECT "CurrentMapId" FROM data."Character" WHERE "Id" = :id', ['id' => $this->session->character['id']])) {
                                    if ($char[0] != $data['map']) {
                                        if($this->query->updateRow('data."Character"', ['CurrentMapId' => $data['map'], 'PositionX' => $map['teleport']['positionx'], 'PositionY' => $map['teleport']['positiony']], ['Id' => $this->session->character['id']])) {
                                            throw new Alert(0x21c, 'success', '/account/teleport');
                                        }
                                    }

                                    throw new Alert(0x39d, 'info', '/account/teleport');
                                }
                            }
                        }

                        throw new Alert(0x22f, 'warning', '/account');
                    }
                }

                return $result;
            }

            public function reset(array $config, bool $execute = false): array|int
            {
                $result = [];
                if (isset($this->session->character)) {
                    $openmu = Util::config('openmu');

                    if (
                        $character['info'] = $this->query->getRow(
                            'SELECT character."CharacterClassId", itemstorage."Money", itemstorage."Id", character."LevelUpPoints" FROM data."Character" character, data."ItemStorage" itemstorage WHERE character."Id" = :id AND itemstorage."Id" = character."InventoryId"',
                            ['id' => $this->session->character['id']]
                        )
                    ) {
                        foreach ($openmu['attribute_definition'] as $key => $attribute_definition) {
                            $statsKey[] = '"DefinitionId" = :' . $key;
                        }

                        if (
                            $character['stats'] = $this->query->getRowAll(
                                'SELECT "DefinitionId", "Value" FROM data."StatAttribute" WHERE "CharacterId" = :characterid AND (' . implode(' OR ', $statsKey) . ')',
                                array_merge(['characterid' => $this->session->character['id']], $openmu['attribute_definition'])
                            )
                        ) {
                            foreach ($character['stats'] as $key => $value) {
                                $result['character']['stats'][array_search($value[0], $openmu['attribute_definition'])] = $value[1];

                            }

                            unset($character['stats']);

                            $result['character']['zen'] = $character['info'][1];

                            foreach ($config['requirements'] as $requirements) {
                                if (($result['character']['stats']['resets'] >= $requirements['resetmin']) && ($result['character']['stats']['resets'] <= $requirements['resetmax'])) {
                                    $result['requirements'] = $requirements;
                                    $result['requirements']['zen'] *= $result['character']['stats']['resets'] + 1;

                                    if ($execute) {
                                        if (($result['character']['stats']['level'] >= $result['requirements']['level']) && ($result['character']['zen'] >= $result['requirements']['zen'])) {
                                            $result['character']['zen'] -= $result['requirements']['zen'];
                                            if ($this->query->updateRow('data."ItemStorage"', ['Money' => $result['character']['zen']], ['Id' => $character['info'][2]])) {

                                                $result['character']['stats']['level'] = 1;
                                                $result['character']['stats']['resets'] += 1;
                                                $result['requirements']['zen'] *= $result['character']['stats']['resets'] + 1;

                                                $attributeSQL = 'UPDATE data."StatAttribute" SET "Value" = CASE';
                                                foreach ($result['character']['stats'] as $key => $stat) {
                                                    $attributeSQL .= match ($key) {
                                                        'level' => ' WHEN "DefinitionId" = :' . $key . ' THEN ' . $result['character']['stats'][$key],
                                                        'resets' => ' WHEN "DefinitionId" = :' . $key . ' THEN ' . $result['character']['stats'][$key],
                                                        default => $result['requirements']['resetstats'] ? ' WHEN "DefinitionId" = :' . $key . ' THEN ' . $openmu['character']['class'][$character['info'][0]]['attr'][$key] : ''
                                                    };

                                                    $attributePrepare[$key] = $openmu['attribute_definition'][$key];
                                                }

                                                $attributePrepare['characterid'] = $this->session->character['id'];
                                                $attributeSQL .= ' ELSE "Value" END WHERE "CharacterId" = :characterid';

                                                if ($updateAttribute = $this->query->exec($attributeSQL, $attributePrepare)) {
                                                    if ($this->query->store($updateAttribute)) {
                                                        if ($result['requirements']['addpoints']) {
                                                            $characterSQL['LevelUpPoints'] = $result['requirements']['pointsforclass'][$character['info'][0]]['point'] * $result['character']['stats']['resets'];
                                                        }

                                                        $characterSQL['Experience'] = 0;

                                                        if ($this->query->updateRow('data."Character"', $characterSQL, ['Id' => $this->session->character['id']])) {
                                                            $this->teleport(['map' => $openmu['character']['class'][$character['info'][0]]['map']]);
                                                            throw new Alert(0x240, 'success', '/account/reset');
                                                        }
                                                    }
                                                }
                                            } else {
                                                throw new Alert(0x254, 'danger', '/account/reset');
                                            }

                                        } else {
                                            throw new Alert(0x118, 'warning', '/account/reset');
                                        }

                                    }
                                    break;
                                }
                            }

                            if (!$result['requirements'])
                                throw new Alert(0x389, 'danger', '/account');

                            return $result;
                        }
                    }
                }

                throw new Alert(0x22f, 'warning', '/account');
            }

            public function addStats(array $config, ?array $data = null)
            {
                $result = $stats = [];
                if (isset($this->session->character)) {
                    $openmu = Util::config('openmu');

                    if (
                        $character['info'] = $this->query->getRow(
                            'SELECT "CharacterClassId", "LevelUpPoints" FROM data."Character" WHERE "Id" = :id',
                            ['id' => $this->session->character['id']]
                        )
                    ) {
                        $result['character']['point'] = $character['info'][1];

                        $stats['value']['characterid'] = $this->session->character['id'];
                        foreach ($openmu['character']['class'][$character['info'][0]]['attr'] as $key => $value) {
                            $stats['key'][] = '"DefinitionId" = :' . $key;
                            $stats['value'][$key] = $openmu['attribute_definition'][$key];
                        }

                        if ($character['stats'] = $this->query->getRowAll('SELECT "DefinitionId", "Value" FROM data."StatAttribute" WHERE "CharacterId" = :characterid AND (' . implode(' OR ', $stats['key']) . ')', $stats['value'])) {
                            foreach ($character['stats'] as $key => $value) {
                                $result['character']['stats'][array_search($value[0], $openmu['attribute_definition'])] = $value[1];
                            }

                            unset($character['stats']);

                            if ($data) {
                                $useStat = [];
                                $useLevelUpPoints = 0;
                                $attr['sql'] = 'UPDATE data."StatAttribute" SET "Value" = CASE';

                                foreach ($data as $key => $value) {
                                    if ($value == 0)
                                        continue;

                                    if ($value < 0)
                                        $value *= -1;

                                    if ($value > $config['max'][$key])
                                        throw new Alert(0x3d7, 'info', '/account/addstats');

                                    $useStat[$key] = $result['character']['stats'][$key] + $value;

                                    if ($config['max'][$key] < $useStat[$key])
                                        throw new Alert(0x3d7, 'info', '/account/addstats');


                                    $useLevelUpPoints += $value;
                                    $attr['sql'] .= ' WHEN "DefinitionId" = :' . $key . ' THEN ' . $useStat[$key];
                                    $attr['key'][] = $openmu['attribute_definition'][$key];
                                }

                                $attr['sql'] .= ' ELSE "Value" END WHERE "CharacterId" = :characterid';
                                
                                if ($useLevelUpPoints != 0) {
                                    if ($useLevelUpPoints <= $result['character']['point']) {
                                        $attr['key']['characterid'] = $this->session->character['id'];
                                        if ($updateAttribute = $this->query->exec($attr['sql'], $attr['key'])) {
                                            if ($this->query->store($updateAttribute)) {
                                                $result['character']['point'] -= $useLevelUpPoints;
                                                $result['character']['stats'] = array_replace($result['character']['stats'], $useStat);

                                                if ($this->query->updateRow('data."Character"', ['LevelUpPoints' => $result['character']['point']], ['Id' => $this->session->character['id']])) {
                                                    throw new Alert(0x1ef, 'success', '/account/addstats');
                                                }
                                            }
                                        }
                                    } else {
                                        throw new Alert(0x3a8, 'info', '/account/addstats');
                                    }
                                } else {
                                    throw new Alert(0x217, 'info', '/account/addstats');
                                }
                            }

                            return $result;
                        }
                    }
                }

                throw new Alert(0x22f, 'warning', '/account/addstats');
            }
        };
    }

    /**
     * config function.
     * It is only used when you declare it.
     * @return array
     */
    private function config(string $configName): array
    {
        if (!isset($this->config[$configName])) {
            $this->config[$configName] = Util::config($configName);
        }

        return $this->config[$configName];
    }

    /**
     * Uuid function.
     * It is only used when you declare it.
     * @return Uuid
     */
    private function uuid(): Uuid
    {
        if (!isset($this->uuid)) {
            $this->uuid = new Uuid;
        }

        return $this->uuid;
    }

    /**
     * Session function.
     * It is only used when you declare it.
     * @return Session
     */
    private function session(): Session
    {
        if (!isset($this->session)) {
            $this->session = new Session;
        }

        return $this->session;
    }
}

?>