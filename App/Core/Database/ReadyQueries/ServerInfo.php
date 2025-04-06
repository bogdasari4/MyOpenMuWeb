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
final class ServerInfo extends DBAdapter
{

    use ConfigLoader;

    /**
     * Get detailed information and settings about the server.
     * @param string $configurationId
     * Game server ID, which allows us to get additional information about the server.
     * @return array
     */
    public function serverConfigurationInfo(string $configurationId): array
    {
        $sql = 'SELECT "MaximumLevel", "MaximumMasterLevel", "ExperienceRate", "MinimumMonsterLevelForMasterExperience", "MaximumInventoryMoney", "MaximumVaultMoney", "MaximumCharactersPerAccount", "MaximumPartySize", "ItemDropDuration"
                FROM config."GameConfiguration"
                WHERE "Id" = :id';
        
        $keys = ['maxlevel', 'maxmasterlevel', 'experience', 'minmoblevelformasterexp', 'maxinvzen', 'maxvaultzen', 'maxcharacc', 'maxpartysize', 'itemdur'];
        
        return array_combine($keys, $this->queryBuilder()->exec($sql, ['id' => $configurationId], false, false));
    }

    /**
     * Primitive information and server status.
     * @param array $config
     * Custom server configuration.
     * @return array
     */
    public function sideServerInfo(?array $config = null): array
    {
        $result = [];
        $apiStats = false;

        if($config === null) {
            $body = $this->configLoader('body');
            $config = $body['body']['serverInfo'];
        }
        
        if($config['apiParse'])
            $apiStats = $this->parseAPIServer($config['api']['url']);

        foreach($this->getDBServerList() as $server) {
            if ($apiStats !== false) {
                if (isset($apiStats[$server[0]])) {
                    $result[$server[0]] = $apiStats[$server[0]];
                }
            } else {
                $result[$server[0]]['status'] = $this->parseStatusServer($config['parse'][$server[1]]);
            }

            $result[$server[0]]['serverid'] = $server[0];
            $result[$server[0]]['name'] = $server[2];
            $result[$server[0]]['experience'] = $server[3];
            $result[$server[0]]['configuration'] = $server[4];
        }
        
        return $result;
    }

    /**
     * Using this function we get a list of servers and basic information.
     * @return array|bool
     */
    private function getDBServerList(): array|bool
    {   
        $sql = 'SELECT gameserverdefinition."ServerID", gameserverendpoint."NetworkPort", gameserverdefinition."Description", gameserverdefinition."ExperienceRate", gameserverdefinition."GameConfigurationId"
                FROM config."GameServerDefinition" gameserverdefinition
                INNER JOIN config."GameServerEndpoint" gameserverendpoint ON gameserverendpoint."GameServerDefinitionId" = gameserverdefinition."Id"
                GROUP BY gameserverdefinition."ServerID", gameserverendpoint."NetworkPort", gameserverdefinition."Description", gameserverdefinition."ExperienceRate", gameserverdefinition."GameConfigurationId"';
    
        return $this->queryBuilder()->exec($sql);
    }

    /**
     * A function for opening a connection to an Internet socket and checking the status of the connection to the server.
     * Not recommended as it slows down the web server.
     * @param array $parse
     * Receives data to open a connection.
     * @return bool
     * If the connection is successful, we get `true`, otherwise `false`.
     */
    private function parseStatusServer(array $parse): bool
    {
        $fp = @fsockopen($parse['host'], $parse['port'], $errno, $errstr, $parse['timeout']);
        if ($fp) {
            fclose($fp);
            return true;
        }

        return false;
    }

        /**
     * Uses a modified api `http://localhost/api/status` to parse detailed information about servers.
     * @param string|null $urlLine
     * Takes a URL string to read the server state. If the value is `null`, we get the serverInfo block configuration file.
     * @return array|bool
     * Get a data array. If it is impossible to read, get `false`
     * @see https://github.com/bogdasari4/MyOpenMuWeb/tree/main#api-modification-example
     * More information and example.
     */
    private function parseAPIServer(?string $urlLine = null): array|bool
    {
        if(($apiString = @file_get_contents(filter_var($urlLine, FILTER_SANITIZE_URL))) !== false) {
            if (($data = json_decode($apiString, true)) !== null && is_array($data)) {
                return $data;
            }
        }

        return false;
    }
}