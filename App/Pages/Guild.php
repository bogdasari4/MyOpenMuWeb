<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Core\Component\{FormattedGet, RedirectTo};
use App\Core\Adapter\PageAdapter;

/**
 * Page to display information about the guild.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Guild extends PageAdapter
{
    use FormattedGet, RedirectTo;

    /**
     * The public function `getInfo()` provides data for rendering pages.
     * @return array
     * We return an array of data.
     */
    public function getInfo(): array
    {
        return $this->setInfo();
    }

    /**
     * The private function `setInfo()` collects information into a data array.
     * @return array
     * We return an array of data.
     */
    private function setInfo(): ?array
    {
        $guildName = $this->formattedGet('subpage', '', false);
        if ($guildName == '')
            $this->redirectTo('/ranking/guild');

        $this->config['cache']['setName'] = sprintf($this->config['cache']['setName'], $this->formattedGet('subpage', '', false));

        $data['text'] = __LANG['body']['page']['guild'];
        $data['info'] = $this->cache()->get(function (array $config) {
            return $this->readyQueries()->guildInfo()->fullGuildInforamtion(substr($config['cache']['setName'], 6));
        });

        if (!$data['info'])
            $this->redirectTo('/ranking/guild');

        return $data;
    }
}
?>
