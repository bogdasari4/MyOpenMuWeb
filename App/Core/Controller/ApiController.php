<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Controller;

/**
 * The main controller for storing all basic api.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class ApiController {
    
    /**
     * An array of system data about engine api.
     * @var array
     */
    private array $map = [
        'guildmark' => ['namespace' => '\\App\\Core\\Api\\GuildMark']
    ];

    /**
     * Add or change api data using the api key.
     * @param string $apiName
     * The api key for recording data.
     * @param array $information
     * Information that will be assigned to our key.
     * @return void
     */
    public function __set(string $apiName, array $information): void
    {
        $this->map[$apiName] = $information;
    }

    /**
     * Reading the api data.
     * @param string $apiName
     * api key for receiving data.
     * @return array
     */
    public function __get(string $apiName): array
    {
        return $this->map[$apiName];
    }

    /**
     * Check the existence of api data by key.
     * @param string $apiName
     * Key of the api to check.
     * @return bool
     */
    public function __isset(string $apiName): bool
    {
        return isset($this->map[$apiName]);
    }
}