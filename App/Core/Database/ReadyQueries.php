<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Database;

use App\Core\Database\ReadyQueries\{AccountInfo, Auth, CharacterInfo,
                                    GuildInfo, RankingInfo, ServerInfo};

/**
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class ReadyQueries
{
    
    public function serverInfo(): ServerInfo
    {
        return new ServerInfo;
    }

    public function rankingInfo(): RankingInfo
    {
        return new RankingInfo;
    }

    public function characterInfo(): CharacterInfo
    {
        return new CharacterInfo;
    }

    public function guildInfo(): GuildInfo
    {
        return new GuildInfo;
    }

    public function auth(): Auth
    {
        return new Auth;
    }

    public function accountInfo(): AccountInfo
    {
        return new AccountInfo;
    }
}