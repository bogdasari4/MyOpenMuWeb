<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Pages;

use App\Core\Adapter\PageAdapter;

/**
 * The main page `Main`, converts simple `xml` documents,
 * in this case `rss` sheets to display the latest news.
 * Returns an object of class SimpleXMLElement with properties containing the data held within the XML document.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Main extends PageAdapter
{
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
     * @see https://www.php.net/manual/en/function.simplexml-load-file.php
     */
    private function setInfo(): array
    {
        $data['text'] = __LANG['body']['page']['main'];
        $data['items'] = $this->cache()->get(function (array $config): array {
            $result = [];
            $xmlParse = @simplexml_load_file(filter_var($config['url'], FILTER_SANITIZE_URL));
            if ($xmlParse !== false) {
                for ($i = 0; $i < count($xmlParse->channel->item) && $i < 5; $i++) {
                    $item = $xmlParse->channel->item[$i];
                    $result[] = [
                        'title' => (string) $item->title,
                        'description' => strip_tags((string) $item->description),
                        'media' => (string) $item->children('media', true)->content->attributes(),
                        'auth' => (string) $item->children('dc', true)->creator,
                        'date' => date('d.h.Y', strtotime((string) $item->pubDate)),
                        'link' => (string) $item->link
                    ];
                }
            }

            return $result;
        });

        return $data;
    }
}

?>