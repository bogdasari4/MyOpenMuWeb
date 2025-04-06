<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Template;

use App\Core\Component\FormattedGet;

/**
 * A special class manages blocks in the template body.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
class Block extends AbstractTemplate
{

    use FormattedGet;

    /**
     * We collect information and create a menu account.
     * Not displayed if the user is not authorized.
     * @return null|array
     */
    protected function accountMenu(): ?array
    {
        if (!isset($this->session->user))
            return null;

        $data['text'] = __LANG['body']['block']['accountMenu'];

        $data['text']['characterSelect'] = isset($this->session->character) ? $this->session->character['name'] : __LANG['body']['block']['accountMenu']['charNotSelect'];

        foreach ($this->app->menucontroller->account as $keyType => $type) {
            foreach ($type as $keyValue => $value) {
                $data['nav'][$keyType][$keyValue] = [
                    'active' => $this->formattedGet('subpage', 'information') == $keyValue ? 'active' : '',
                    'link' => $value['link'],
                    'name' => (isset($value['name']) && $value['name'] != '') ? $value['name'] : __LANG['body']['block']['accountMenu']['nav'][$keyType][$keyValue]
                ];
            }

        }
        
        return $data;
    }

    /**
     * Parsing information about servers. This information is also used on the `/about` page.
     * @param array $config
     * Get block configs.
     * @return array
     * We get information about the server from the GameServerDefinition and GameServerEndpoint table 
     * and check the status using the parseStatusServer or parseAPIServer function.
     */
    protected function serverInfo(array $config): array
    {
        $data['text'] = __LANG['body']['block']['serverInfo'];
        
        $data['row'] = $this->cache($config)->get(function (array $config): array {
            return $this->readyQueries()->serverInfo()->sideServerInfo($config);
        });

        return $data;
    }

    /**
     * Data for rendering the authorization block.
     * Not displayed if the user is logged in.
     * @return null|array
     */
    protected function siginForm(): ?array
    {
        if (isset($this->session->user) || $this->formattedGet('page', '') == 'sigin')
            return null;

        $data['text'] = __LANG['body']['block']['siginForm'];
        $data['config'] = $this->config['body']['page']['sigin']['validator'];

        return $data;
    }

    /**
     * Reading the user rating cache.
     * If the cache is missing or outdated, create a new one.
     * Print the first `$config['row']` characters.
     * @param array $config
     * Get block configs.
     * @return array
     */
    protected function rankingInfo(array $config): array
    {
        $data['text'] = __LANG['body']['block']['rankingInfo'];
        $data['row'] = array_slice($this->cache($config, true)->get(function (array $config) {
            return $this->readyQueries()->rankingInfo()->character();
            
        }), 0, $config['row']);

        return $data;
    }
}

?>