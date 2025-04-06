<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database\ReadyQueries;

use App\Core\Adapter\DBAdapter;
use App\Core\Component\{RedirectTo, ConfigLoader};
use App\Core\Session;
use App\Alert;

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class AccountInfo extends DBAdapter
{

    use RedirectTo, ConfigLoader;

    private readonly Session $session;

    public function __construct()
    {
        $this->session = new Session;
    }

    /**
     * Basic account information with a list of all created characters.
     * @return array[]|array{email: mixed, id: mixed, loginName: mixed, registrationDate: string}
     */
    public function information(): array
    {
        $result = $this->session->user;

        if(!empty($chars = $this->queryBuilder()->getRowsAll('data."Character"', ['AccountId' => $this->session->user['id']], '"Id", "Name"'))) {
            foreach($chars as $char) {
                $result['chars'][] = $char[1];
            }

            $this->session->character ??= ['id' => $chars[0][0], 'name' => $chars[0][1]];
        }

        return $result;
    }

    /**
     * Change password with current password check.
     * Additional checks are performed in the page class before using this function.
     * @param array $data
     * Data from the password change form.
     * @return bool
     */
    public function changePass(array $data): bool
    {
        if(($account = $this->queryBuilder()->getRow('data."Account"', ['Id' => $this->session->user['id']], '"PasswordHash"')) !== false) {
            if(password_verify($data['password'], $account[0])) {
                return $this->queryBuilder()->updateRow(
                    'data."Account"',
                    ['PasswordHash' => password_hash($data['newpassword'], PASSWORD_BCRYPT)],
                    ['Id' => $this->session->user['id']]);
            } 
        }

        return false;
    }

    /**
     * Function of selecting an active character.
     * @param string $charName
     * Character name
     * @return bool
     */
    public function changeCharacter(string $charName): bool
    {
        if(isset($this->session->user)) {
            if(isset($this->session->character) && $this->session->character['name'] == $charName) 
                return false;
            
            if(($char = $this->queryBuilder()->getRow('data."Character"', ['AccountId' => $this->session->user['id'], 'Name' => $charName], '"Id", "Name"'))  !== false) {
                $this->session->character = [
                    'id' => $char[0],
                    'name' => $char[1]
                ];

                return true;
            }
        }

        $this->redirectTo();
    }

    /**
     * Moving characters to other locations.
     * The movement does not occur if the character is already in the desired location.
     * @param mixed $data
     * Data to move.
     * @return bool
     */
    public function teleport(?array $data): bool
    {
        if(isset($this->session->character)) {
            $gameMap = $this->configLoader('openmu.game_map');

            if(isset($gameMap[$data['map']])) {
                if(($char = $this->queryBuilder()->getRow('data."Character"', ['Id' => $this->session->character['id']], '"CurrentMapId"')) !== false) {
                    if($char[0] != $data['map']) {
                        return $this->queryBuilder()->updateRow(
                            'data."Character"',
                            ['CurrentMapId' => $data['map'], 'PositionX' => $gameMap[$data['map']]['teleport']['positionx'], 'PositionY' => $gameMap[$data['map']]['teleport']['positiony']],
                            ['Id' => $this->session->character['id']]);
                    }
                }
            }
        }

        return false;
    }

    /**
     * Reset character with character data provided.
     * @param array $config
     * Configuration with dynamic reset requirements.
     * @param bool $execute
     * If `$execute` is `true` the reset request is executed, otherwise the function returns character information and reset requirements.
     * @throws \App\Alert
     * @return array
     */
    public function reset(array $config, bool $execute = false): array
    {
        if(isset($this->session->character)) {
            $result = [];

            $attributeDefinition = $this->configLoader('openmu.attribute_definition');

            $sql = 'SELECT c."CharacterClassId", i."Money", i."Id" AS inventory_id, c."LevelUpPoints", sa."DefinitionId", sa."Value"
                    FROM data."Character" c
                    INNER JOIN data."ItemStorage" i ON i."Id" = c."InventoryId"
                    INNER JOIN data."StatAttribute" sa ON sa."CharacterId" = c."Id" 
                    WHERE (sa."DefinitionId" = :' . implode(' OR sa."DefinitionId" = :', array_keys($attributeDefinition)) . ') AND c."Id" = :id';

            $params = $attributeDefinition;
            $params['id'] = $this->session->character['id'];
            
            if(!empty($fullCharInfo = $this->queryBuilder()->exec($sql, $params))) {
                foreach($fullCharInfo as $row) {
                    $result['character']['stats'][array_search($row[4], $attributeDefinition)] = $row[5];
                }

                $result['character']['zen'] = $fullCharInfo[0][1];

                if(($result['requirements'] = array_find($config['requirements'], fn($requirements): bool => ($result['character']['stats']['resets'] >= $requirements['resetmin']) && ($result['character']['stats']['resets'] <= $requirements['resetmax']))) !== null) {
                    $result['requirements']['zen'] *= $result['character']['stats']['resets'] + 1;

                    if($execute) {
                        if(($result['character']['stats']['level'] >= $result['requirements']['level']) && ($result['character']['zen'] >= $result['requirements']['zen'])) {
                            $result['character']['zen'] -= $result['requirements']['zen'];

                            if($this->queryBuilder()->updateRow('data."ItemStorage"', ['Money' => $result['character']['zen']], ['Id' => $fullCharInfo[0][2]])) {
                                $result['character']['stats']['level'] = 1;
                                $result['character']['stats']['resets'] += 1;
                                $result['requirements']['zen'] *= $result['character']['stats']['resets'] + 1;

                                $characterClass = $this->configLoader('openmu.character_class');
                                $attributeSQL = 'UPDATE data."StatAttribute" SET "Value" = CASE';

                                foreach ($result['character']['stats'] as $key => $stat) {
                                    $attributeSQL .= match ($key) {
                                        'level', 'resets' => ' WHEN "DefinitionId" = :' . $key . ' THEN ' . $result['character']['stats'][$key],
                                        default => $result['requirements']['resetstats'] ? ' WHEN "DefinitionId" = :' . $key . ' THEN ' . $characterClass[$fullCharInfo[0][0]]['attr'][$key] : ''
                                    };

                                    $attributeParams[$key] = $attributeDefinition[$key];
                                }

                                $attributeParams['characterid'] = $this->session->character['id'];
                                $attributeSQL .= ' ELSE "Value" END WHERE "CharacterId" = :characterid';

                                if($this->queryBuilder()->exec($attributeSQL, $attributeParams, true)) {
                                    if($result['requirements']['addpoints'])
                                        $characterSQL['LevelUpPoints'] = $result['requirements']['pointsforclass'][$fullCharInfo[0][0]]['point'] * $result['character']['stats']['resets'];
                                    $characterSQL['Experience'] = 0;

                                    if($this->queryBuilder()->updateRow('data."Character"', $characterSQL)) {
                                        $this->teleport(['map' => $characterClass[$fullCharInfo[0][0]]['map']]);
                                        
                                        throw new Alert(0x240, 'success', '/account/reset');
                                    }
                                } else {
                                    throw new Alert(0x254, 'danger', '/account/reset');
                                }
                            }
                        } else {
                            throw new Alert(0x118, 'warning', '/account/reset');
                        }
                    }
                } else {
                    throw new Alert(0x389, 'danger', '/account');
                }
            }
        }
        
        return $result;
    }

    /**
     * Character stat distribution function.
     * @param array $config
     * Configuration for each characteristic depending on the class.
     * @param mixed $data
     * Data from the distribution form.
     * @throws \App\Alert
     * @return array[]
     */
    public function addStats(array $config, ?array $data = null): array
    {
        if(isset($this->session->character)) {
            $result = [];

            $attributeDefinition = $this->configLoader('openmu.attribute_definition');
            $characterClass = $this->configLoader('openmu.character_class');

            if(($character = $this->queryBuilder()->getRow('data."Character"', ['Id' => $this->session->character['id']], '"CharacterClassId", "LevelUpPoints"')) !== false) {
                $result['character']['point'] = $character[1];

                $params = array_filter($attributeDefinition, fn($k) => isset($characterClass[$character[0]]['attr'][$k]), ARRAY_FILTER_USE_KEY );
                $params['characterid'] = $this->session->character['id'];

                $sql = 'SELECT "DefinitionId", "Value" FROM data."StatAttribute" WHERE "CharacterId" = :characterid AND ("DefinitionId" = :' . implode(' OR "DefinitionId" = :', array_keys($params)) . ')';
                
                if(!empty($statAttribute = $this->queryBuilder()->exec($sql, $params))) {
                    foreach($statAttribute as $attr) {
                        $result['character']['stats'][array_search($attr[0], $params)] = $attr[1]; 
                    }

                    if($data !== null) {
                        $useStat = [];
                        $useLevelUpPoints = 0;
                        $sqlAttr = 'UPDATE data."StatAttribute" SET "Value" = CASE';

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
                            $sqlAttr .= ' WHEN "DefinitionId" = :' . $key . ' THEN ' . $useStat[$key];
                            $paramsAttr[] = $attributeDefinition[$key];
                        }

                        $sqlAttr .= ' ELSE "Value" END WHERE "CharacterId" = :characterid';

                        if ($useLevelUpPoints != 0) {
                            if ($useLevelUpPoints <= $result['character']['point']) {
                                $paramsAttr['characterid'] = $this->session->character['id'];
                                if($this->queryBuilder()->exec($sqlAttr, $paramsAttr)) {
                                    $result['character']['point'] -= $useLevelUpPoints;
                                    $result['character']['stats'] = array_replace($result['character']['stats'], $useStat);
                                    
                                    if($this->queryBuilder()->updateRow('data."Character"', ['LevelUpPoints' => $result['character']['point']], ['Id' => $this->session->character['id']]))
                                        throw new Alert(0x1ef, 'success', '/account/addstats');
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
}