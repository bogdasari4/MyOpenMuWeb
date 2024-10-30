<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Template;

/**
 * Template body.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Body extends Block
{
    /**
     * The public function `getData()` provides data for rendering pages.
     * @return array
     * We return an array of data.
     */
    public function getData(): array
    {
        return $this->setInfo();
    }

    /**
     * The private function `setInfo()` collects information into a data array.
     * @return array
     * We return an array of data.
     */
    private function setInfo(): array
    {
        $data['template']['body']['header']['isLogin'] = isset($this->session->user) ? true : false;
        $data['template']['body']['header']['nav'] = $this->app->menucontroller->header;
        $data['template']['text'] = __LANG['body']['template'];

        foreach($this->config['body']['block'] as $key => $block ) {
            $data['template']['body']['main']['block'][$key] = $block['status'] ? $this->{$key}($block) : null;
        }

        return $data;
    }

    /**
     * Get settings for the specified page. 
     * Used in the `Handler` class to prevent the `body` configuration file from being opened multiple times.
     * @param string $key
     * Get settings for the `$key` page.
     * @return array
     */
    public function getPageConfig(string $key): array
    {
        if(isset($this->config['body']['page'][$key]))
            return $this->config['body']['page'][$key];
        return [];
    }
}

?>