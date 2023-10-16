<?php

namespace App\Pages;

use App\Util;
use App\Core\PostgreSQL\Query;
use App\Core\Auth\Validation;
use \Exception;

class Account {

    
    /**
     * Summary of data
     * @var array
     */
    private $data = ['page' => []];

    /**
     * Summary of config
     * @var array
     */
    private $config = [];

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
     * @return Account
     */
    public static function getInfo() {
        $info = new Account;
        return $info;
    }

    /**
     * Summary of setInfo
     * @return void
     */
    private function setInfo(): void {

        if(!isset($_SESSION['user']) || $_SESSION['user'] == '') Util::redirect();

        $subpage = (isset($_GET['subpage']) && $_GET['subpage'] != '') ? $_GET['subpage'] : 'information';
        $subpage = strtolower($subpage);
        $subpage = Util::trimSChars($subpage);

        if(!is_callable([$this, $subpage])) Util::redirect('/account');

        $this->config = Util::config('body');
        $this->data['page']['account'] = [
            'text' => __LANG['body']['page']['account']['menu'],
            'get' => [
                'subpage' => $subpage
            ]
        ];
        foreach($this->config['block']['accountMenu']['menu'] as $key => $menu) {
            foreach($menu as $subkey => $submenu) {
                $this->data['page']['account']['menu'][$key][] = [
                    'name' => $this->data['page']['account']['text'][$key][$subkey]['name'],
                    'link' => $submenu['link'],
                    'active' => ($subkey == $subpage) ? 'active' : ''
                ];
            }
        }

        try {
            $this->{$subpage}();
        } catch (Exception $alert) {
            $this->data['page']['account'][$subpage]['alert'] = str_replace(
                ['{{ str_message }}', '{{ code_message }}'],
                [$alert->getMessage(), $alert->getCode()],
                file_get_contents(__ROOT . 'templates/' . __CONFIG['other']['template'] . '/alert.html')
            );
        }
    }

    /**
     * Summary of information
     * @return void
     */
    private function information(): void {

        $this->data['page']['account']['information'] = [
            'text' => __LANG['body']['page']['account']['information'],
            'info' => Query::getRow('SELECT "LoginName", "EMail", "RegistrationDate" FROM data."Account" WHERE "Id" = :id', ['id' => $_SESSION['user']['loginId']])
        ];

        $this->data['page']['account']['information']['info']['chars'] = Query::getRowAll('SELECT "Name" FROM data."Character" WHERE "AccountId" = :id', ['id' => $_SESSION['user']['loginId']]);

        $this->data['page']['account']['information']['info'][1] = $this->data['page']['account']['information']['info'][1] ? $this->data['page']['account']['information']['info'][1] : $this->data['page']['account']['information']['text']['emailempty'];

    }

    private function changepass(): void {
        $this->data['page']['account']['changepass'] = [
            'text' => __LANG['body']['page']['account']['changepass'],
            'config' => $this->config['page']['account']['changepass']
        ];
        
        $this->data['page']['account']['changepass']['text']['form']['renewpassword']['help'] = sprintf(
            $this->data['page']['account']['changepass']['text']['form']['renewpassword']['help'], 
            $this->config['page']['account']['changepass']['validator']['password']['min_length'],
            $this->config['page']['account']['changepass']['validator']['password']['max_length']
        );

        if(isset($_POST['submit'])) {
            $data = $_POST['changepass'];

            foreach($data as $key => $password) {
                $password = (string) Util::trimSChars($password);
                if(!Validation::password($password, $this->config['page']['account']['changepass']['validator']['password'])) throw new Exception($this->data['page']['account']['changepass']['text']['form']['alert'][$key . 'incorrect'], 2);
            }

            if($data['newpassword'] !== $data['renewpassword']) throw new Exception($this->data['page']['account']['changepass']['text']['form']['alert']['newpasswordsdonotmatch'], 2);
            if($data['password'] === $data['newpassword']) throw new Exception($this->data['page']['account']['changepass']['text']['form']['alert']['oldpasswordsmatch'], 2);

            if($account = Query::getRow('SELECT "PasswordHash" FROM data."Account" WHERE "Id" = :id', ['id' => $_SESSION['user']['loginId']])) {
                if(password_verify($data['password'], $account[0])) {
                    if(Query::updateRow('data."Account"', ['PasswordHash' => password_hash($data['newpassword'], PASSWORD_BCRYPT)], ['Id' => $_SESSION['user']['loginId']])) {
                        throw new Exception($this->data['page']['account']['changepass']['text']['form']['alert']['passwordchanged'], 1);
                    }
                }
                throw new Exception($this->data['page']['account']['changepass']['text']['form']['alert']['passwordfailed'], 2);
            }

            throw new Exception($this->data['page']['account']['changepass']['text']['form']['alert']['getpasswordfailed'], 2);
        }
    }

    private function reset(): void {

        $this->data['page']['account']['reset'] = [
            'text' => __LANG['body']['page']['account']['reset'],
            'conditionsnotmet' => false,
            'nocharacters' => true
        ];

        if(!isset($_SESSION['user']['character']) || ($_SESSION['user']['character']['id'] == '')) {
            $this->data['page']['account']['reset']['nocharacters'] = false;
            throw new Exception($this->data['page']['account']['reset']['text']['alert']['characternotselected'], 2);
        }

        $config['openmu'] = Util::config('openmu');
        if($char['info'] = Query::getRow(
            'SELECT character."CharacterClassId", itemstorage."Money", itemstorage."Id", character."LevelUpPoints"
            FROM data."Character" character, data."ItemStorage" itemstorage 
            WHERE character."Id" = :id 
            AND itemstorage."Id" = character."InventoryId"',
            [
                'id' => isset($_SESSION['user']['character']) ? $_SESSION['user']['character']['id'] : null
            ])) {
            if($char['stats'] = Query::getRowAll('SELECT "DefinitionId", "Value" FROM data."StatAttribute" WHERE "CharacterId" = :characterid AND ("DefinitionId" = :level OR "DefinitionId" = :resets)',
                [
                    'characterid' => $_SESSION['user']['character']['id'],
                    'level' => $config['openmu']['attribute_definition']['level'],
                    'resets' => $config['openmu']['attribute_definition']['resets']
                ])) {

                    foreach($char['stats'] as $stats) {
                        $char['stats'][$stats[0]] = $stats[1];
                    }

                    foreach($this->config['page']['account']['reset']['requirements'] as $key => $requirements) {
                        if(($char['stats'][$config['openmu']['attribute_definition']['resets']] >= $requirements['reset']) && ($char['stats'][$config['openmu']['attribute_definition']['resets']] <= $requirements['resetmax'])) {
                            $result = [
                                'zen' => ($char['stats'][$config['openmu']['attribute_definition']['resets']] + 1) * $requirements['zen']
                            ];
                            $this->data['page']['account']['reset']['addpoints'] = $this->config['page']['account']['reset']['requirements'][$key]['addpoints'];

                            $this->data['page']['account']['reset']['text']['resetforreset'] = sprintf(
                                $this->data['page']['account']['reset']['text']['resetforreset'], 
                                $requirements['reset'], 
                                $char['stats'][$config['openmu']['attribute_definition']['resets']]
                            );
                            $this->data['page']['account']['reset']['text']['levelforreset'] = sprintf(
                                $this->data['page']['account']['reset']['text']['levelforreset'], 
                                $requirements['level'], 
                                $char['stats'][$config['openmu']['attribute_definition']['level']]
                            );
                            $this->data['page']['account']['reset']['text']['zenforreset'] = sprintf(
                                $this->data['page']['account']['reset']['text']['zenforreset'], 
                                number_format($result['zen']), 
                                number_format($char['info'][1])
                            );
            
                            $this->data['page']['account']['reset']['text']['rewardpoint'] = sprintf(
                                $this->data['page']['account']['reset']['text']['rewardpoint'], 
                                ($char['stats'][$config['openmu']['attribute_definition']['resets']] + 1) * $this->config['page']['account']['reset']['requirements'][$key]['pointsforclass'][$char['info'][0]]['point']
                            );

                            if(($char['stats'][$config['openmu']['attribute_definition']['level']] == $requirements['level']) && ($char['info'][1] >= (($char['stats'][$config['openmu']['attribute_definition']['resets']] + 1) * $requirements['zen']))) {
                                $this->data['page']['account']['reset']['conditionsnotmet'] = true;
                                if(isset($_POST['reset']['submit'])) {
                                    foreach($config['openmu']['attribute_definition'] as $keyAttr => $attribute_definition) {
                                        $query = [
                                            'table' => 'data."StatAttribute"',
                                            'where' => [
                                                'CharacterId' => $_SESSION['user']['character']['id'],
                                                'DefinitionId' => $attribute_definition
                                            ]
                                        ];

                                        switch($keyAttr) {
                                            case 'level':
                                                if(!Query::updateRow($query['table'], ['Value' => 1], $query['where'])) {
                                                    throw new Exception($this->data['page']['account']['reset']['text']['form']['alert']['errorresets'], 2);
                                                }
                                                break;

                                            case 'resets':
                                                if(!Query::updateRow($query['table'], ['Value' => $char['stats'][$config['openmu']['attribute_definition']['resets']] + 1], $query['where'])) {
                                                    throw new Exception($this->data['page']['account']['reset']['text']['form']['alert']['errorresets'], 2);
                                                    
                                                }
                                                break;

                                            default:
                                                if($this->config['page']['account']['reset']['requirements'][$key]['resetstats']) {
                                                    if(isset($config['openmu']['character']['class'][$char['info'][0]]['attr'][$keyAttr])) {
                                                        if(!Query::updateRow($query['table'], ['Value' => $config['openmu']['character']['class'][$char['info'][0]]['attr'][$keyAttr]], $query['where'])) {
                                                            throw new Exception($this->data['page']['account']['reset']['text']['form']['alert']['errorresets'], 2);
                                                        }
                                                    }
                                                }
                                                break;
                                        }
                                    }

                                    if(!Query::updateRow('data."Character"', 
                                        [
                                            'Experience' => 0, 
                                            'LevelUpPoints' => $this->config['page']['account']['reset']['requirements'][$key]['addpoints'] ? ($char['stats'][$config['openmu']['attribute_definition']['resets']] + 1) * $this->config['page']['account']['reset']['requirements'][$key]['pointsforclass'][$char['info'][0]]['point'] : $char['info'][3]
                                        ],
                                        [
                                            'Id' => $_SESSION['user']['character']['id']
                                        ]
                                    )) {
                                        throw new Exception($this->data['page']['account']['reset']['text']['form']['alert']['errorresets'], 2);
                                    }

                                    if(!Query::updateRow('data."ItemStorage"',
                                    [
                                        'Money' => $char['info'][1] - $result['zen']
                                    ],
                                    [
                                        'Id' => $char['info'][2]
                                    ])) {
                                        throw new Exception($this->data['page']['account']['reset']['text']['form']['alert']['errorresets'], 2);
                                    }

                                    $this->data['page']['account']['reset']['conditionsnotmet'] = false;

                                    $this->data['page']['account']['reset']['text']['resetforreset'] = sprintf(
                                        __LANG['body']['page']['account']['reset']['resetforreset'], 
                                        $requirements['reset'], 
                                        $char['stats'][$config['openmu']['attribute_definition']['resets']] + 1
                                    );
                                    $this->data['page']['account']['reset']['text']['levelforreset'] = sprintf(
                                        __LANG['body']['page']['account']['reset']['levelforreset'], 
                                        $requirements['level'], 
                                        1
                                    );
                                    $this->data['page']['account']['reset']['text']['zenforreset'] = sprintf(
                                        __LANG['body']['page']['account']['reset']['zenforreset'], 
                                        number_format($result['zen'] + $requirements['zen']), 
                                        number_format($char['info'][1] - $result['zen'])
                                    );
                    
                                    $this->data['page']['account']['reset']['text']['rewardpoint'] = sprintf(
                                        __LANG['body']['page']['account']['reset']['rewardpoint'], 
                                        ($char['stats'][$config['openmu']['attribute_definition']['resets']] + 2) * $this->config['page']['account']['reset']['requirements'][$key]['pointsforclass'][$char['info'][0]]['point']
                                    );
                                    throw new Exception($this->data['page']['account']['reset']['text']['form']['alert']['successfulreset'], 1);
                                }
                            }
                            break;
                        }
                    }

            }

        } else {
            $this->data['page']['account']['reset']['nocharacters'] = false;
            throw new Exception($this->data['page']['account']['reset']['text']['alert']['nocharacter'], 2);
        }
    }

    private function addstats(): void {

        $this->data['page']['account']['addstats'] = [
            'text' => __LANG['body']['page']['account']['addstats'],
            'nocharacters' => true
        ];

        if(!isset($_SESSION['user']['character']) || ($_SESSION['user']['character']['id'] == '')) {
            $this->data['page']['account']['addstats']['nocharacters'] = false;
            throw new Exception($this->data['page']['account']['addstats']['text']['alert']['characternotselected'], 2);
        }

        $config['openmu'] = Util::config('openmu');
        if($char['info'] = Query::getRow(
            'SELECT "CharacterClassId", "LevelUpPoints"
             FROM data."Character"
             WHERE "Id" = :id
            ',
            [
                'id' => $_SESSION['user']['character']['id']
            ]
        )) {
            $this->data['page']['account']['addstats']['text']['form']['table']['leveluppoints'] = sprintf(__LANG['body']['page']['account']['addstats']['form']['table']['leveluppoints'], number_format($char['info'][1]));
            if($char['attr'] = Query::getRowAll(
                'SELECT "DefinitionId", "Value"
                 FROM data."StatAttribute"
                 WHERE "CharacterId" = :characterid 
                 AND ("DefinitionId" = :strength 
                 OR "DefinitionId" = :agility
                 OR "DefinitionId" = :vitality
                 OR "DefinitionId" = :energy
                 OR "DefinitionId" = :leadership)
                ',
                [
                    'characterid' => $_SESSION['user']['character']['id'], 
                    'strength' => $config['openmu']['attribute_definition']['strength'],
                    'agility' => $config['openmu']['attribute_definition']['agility'],
                    'vitality' => $config['openmu']['attribute_definition']['vitality'],
                    'energy' => $config['openmu']['attribute_definition']['energy'],
                    'leadership' => $config['openmu']['attribute_definition']['leadership']
                ]
            )) {
                
                foreach($config['openmu']['character']['class'][$char['info'][0]]['attr'] as $key => $jattr) {
                    $this->data['page']['account']['addstats']['info']['attr'][] = $key;
                    foreach($char['attr'] as $attr) {
                        if($attr[0] == $config['openmu']['attribute_definition'][$key]) {
                            $data['attr'][$key] = $attr[1];
                            $this->data['page']['account']['addstats']['text']['form']['table'][$key] = sprintf(__LANG['body']['page']['account']['addstats']['form']['table'][$key], number_format($attr[1]));
                        }
                    }
                }

                if(isset($_POST['submit'])) {
                    if(array_sum($_POST['addstats']) <= $char['info'][1]) {
                        $leveluppoints = 0;
                        $i = 0;
                        $data['sql'] = '';
                        foreach($_POST['addstats'] as $key => $attr) {
                            $attr = (int) $attr;
                            $resultAttr = $data['attr'][$key] + $attr;
                            if(($attr != '') && is_int($attr) && ($attr > 0) && ($resultAttr <= $this->config['page']['account']['addstats']['max'][$key])) {
                                $leveluppoints += $attr;
                                $data['sql'] .= ' WHEN "DefinitionId" = :definitionid' . $i . ' THEN :result' . $i;
                                $sql['definitionid' . $i] = $config['openmu']['attribute_definition'][$key];
                                $sql['result' . $i] = $resultAttr;
                                ++$i;
                            }
                        }

                        if(($leveluppoints != '') && ($leveluppoints > 0)) {
                            $sql['characterid'] = $_SESSION['user']['character']['id'];
                            if($data = Query::exec('UPDATE data."StatAttribute" SET "Value" = CASE ' . $data['sql'] . ' ELSE "Value" END WHERE "CharacterId" = :characterid', $sql)) {
                                if(Query::store($data)) {
                                    $i = 0;
                                    foreach($_POST['addstats'] as $key => $attr) {
                                        if(isset($sql['result' . $i])) {
                                            $this->data['page']['account']['addstats']['text']['form']['table'][$key] = sprintf(__LANG['body']['page']['account']['addstats']['form']['table'][$key], number_format($sql['result' . $i]));
                                        }
                                        ++$i;
                                    }

                                    $resultLevelUpPoints = $char['info'][1] - $leveluppoints;
                                    if(Query::updateRow('data."Character"', ['LevelUpPoints' => $resultLevelUpPoints], ['Id' => $sql['characterid']])) {
                                        $this->data['page']['account']['addstats']['text']['form']['table']['leveluppoints'] = sprintf(__LANG['body']['page']['account']['addstats']['form']['table']['leveluppoints'], number_format($resultLevelUpPoints));
                                    }
                                }
                            }
                        } else {
                            throw new Exception($this->data['page']['account']['addstats']['text']['form']['alert']['statisticsarenotindicated'], 2);
                        }
                    } else {
                        throw new Exception($this->data['page']['account']['addstats']['text']['form']['alert']['noleveluppoints'], 2);
                    }

                    throw new Exception($this->data['page']['account']['addstats']['text']['form']['alert']['successfuladdpoints'], 1);
                }
            }
        } else {

            $this->data['page']['account']['addstats']['nocharacters'] = false;
            throw new Exception($this->data['page']['account']['addstats']['text']['alert']['nocharacter'], 2);
        }

    }

    private function teleport(): void {

        $this->data['page']['account']['teleport'] = [
            'text' => __LANG['body']['page']['account']['teleport'],
            'nocharacters' => true
        ];

        if(!isset($_SESSION['user']['character']) || ($_SESSION['user']['character']['id'] == '')) {
            $this->data['page']['account']['teleport']['nocharacters'] = false;
            throw new Exception($this->data['page']['account']['teleport']['text']['alert']['characternotselected'], 2);
        }

        if($char['info'] = Query::getRow(
            'SELECT "CurrentMapId"
             FROM data."Character"
             WHERE "Id" = :id
            ',
            [
                'id' => $_SESSION['user']['character']['id']
            ]
        )) {
            $config = Util::config('openmu');
            foreach($config['game_map'] as $key => $game_map) {
                if($game_map['teleport']['status']) {
                    $this->data['page']['account']['teleport']['game_map'][$key] = [$key, $game_map['name']];
                }
            }

            if(isset($_POST['submit'])) {
                $data['map'] = strtolower(Util::trimSChars($_POST['teleport']['map']));
                if(($data['map'] != '') && ($char['info'][0] !== $data['map'])) {
                    if(Query::updateRow(
                        'data."Character"',
                        [
                            'CurrentMapId' => $data['map'],
                            'PositionX' => $config['game_map'][$data['map']]['teleport']['positionx'],
                            'PositionY' => $config['game_map'][$data['map']]['teleport']['positiony']
                        ],
                        [
                            'Id' => $_SESSION['user']['character']['id']
                        ]
                    )) {
                        throw new Exception($this->data['page']['account']['teleport']['text']['form']['alert']['successfulteleport'], 1);
                    }
                } else {
                    throw new Exception($this->data['page']['account']['teleport']['text']['form']['alert']['charisgamemap'], 2);
                }
            }
        } else {

            $this->data['page']['account']['teleport']['nocharacters'] = false;
            throw new Exception($this->data['page']['account']['teleport']['text']['alert']['nocharacter'], 2);
        }

    }

    private function getchar(): void {
        if(isset($_GET['request']) && $_GET['request'] != '') {
            $request = Util::trimSChars($_GET['request']);
            if($char = Query::getRow('SELECT "Id", "Name" FROM data."Character" WHERE "AccountId" = :id AND "Name" = :name', ['id' => $_SESSION['user']['loginId'], 'name' => $request])) {
                $_SESSION['user']['character'] = [
                    'id' => $char[0],
                    'name' => $char[1]
                ];

                Util::redirect('/account/reset');
                return;
            }
        }

        Util::redirect('/account');
    }
}