<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database\PostgreSQL;

/**
 * A class with ready-made queries to the `PostgreSQL` database.
 */
interface InterfaceReadyStrings
{
    /**
     * We get information about servers for the `serverInfo` block.
     */
    public function serverInfo(?array $config = null): array;

    /**
     * We get `ROW' of character or guild rows.
     */
    public function rankingInfo(?array $config = null, string $queryType = 'character'): array;

    /**
     * Get detailed information about each server by querying the server configuration.
     */
    public function getServerInfo(string $configurationId): array;

    /**
     * Get complete information about the character.
     */
    public function getCharacterInfo(string $charName): array;
    
    /**
     * Get complete information about the guild.
     */
    public function getGuildInfo(string $guildName): array;

    /**
     * Ready request for creating an account.
     */
    public function createAccount(array $data): bool;

    /**
     * Function of authorization and creation of a user session.
     */
    public function authorization(array $data): bool;

    /**
     * Function with an anonymous class. Processes all account pages.
     */
    public function getAccountInfo();
}