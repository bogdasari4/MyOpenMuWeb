<?php

namespace App\Core\Template;

use App\Util;
use App\Core\PostgreSQL\Query;

class Block {

    /**
     * Summary of serverInfo
     * @param array $config
     * @return array
     */
    protected function serverInfo(array $config): array {
        $cache = Util::readCache($config['cache']);

        if($cache[0][0] < time() - $config['cache']['lifetime']) {

            $servers = [
                'list' => Query::getRowAll(
                        'SELECT "ServerID", "Description", "ExperienceRate", "GameConfigurationId"
                        FROM config."GameServerDefinition"',
                        []
                ),
                'status' => Util::parseStatusServer()
            ];

            foreach($servers['list'] as $key => $value) {
                $data[$key] = array_merge($value, $servers['status'][$key]);
            }

            Util::writeCache($config['cache'], $data);
        }

        unset($cache[0]);
        $data = [
            'text' => __LANG['body']['block']['serverInfo'],
            'row' => $cache ? $cache : $data
        ];

        return $data;
    }

    /**
     * Summary of siginForm
     * @return array
     */
    protected function siginForm(): array {
        $data['text'] = __LANG['body']['block']['siginForm'];
        return $data;
    }

    /**
     * Summary of rankingInfo
     * @param array $config
     * @return array
     */
    protected function rankingInfo(array $config): array {

        $cache = Util::readCache($config['cache']);

        $data['text'] = __LANG['body']['block']['rankingInfo'];

        $data['text']['lifetime'] = sprintf($data['text']['lifetime'], date('H:i', $cache[0][0]));

        unset($cache[0]);
        if($cache) {
            for($i = 1; $i < ($config['row'] + 1) ; $i++) {
                $data['row'][$i] = $cache[$i];
            }
        }
        return $data;
    }

}

?>