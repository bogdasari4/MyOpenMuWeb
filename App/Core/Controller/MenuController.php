<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Controller;

use App\Core\Session;

/**
 * The main controller for storing all basic menus.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class MenuController
{

    private array $map = [
        'header' => [
            'main' => ['link' => '/', 'position' => 10, 'islogin' => false],
            'account' => ['link' => '/account', 'position' => 20, 'islogin' => true],
            'ranking' => ['link' => '/ranking', 'position' => 30, 'islogin' => false],
            'downloads' => ['link' => '/downloads', 'position' => 40, 'islogin' => false],
            'forum' => ['link' => '/forum', 'position' => 50, 'islogin' => false],
            'about' => ['link' => '/about', 'position' => 60, 'islogin' => false]
        ],
        'ranking' => [
            'character' => ['link' => '/ranking/character', 'position' => 10, 'islogin' => false],
            'guild' => ['link' => '/ranking/guild', 'position' => 20, 'islogin' => false]
        ],
        'account' => [
            'user' => [
                'information' => ['link' => '/account/information', 'position' => 10, 'isLogin' => true],
                'changepass' => ['link' => '/account/changepass', 'position' => 20, 'isLogin' => true]
            ],
            'character' => [
                'reset' => ['link' => '/account/reset', 'position' => 10, 'isLogin' => true],
                'addstats' => ['link' => '/account/addstats', 'position' => 20, 'isLogin' => true],
                'teleport' => ['link' => '/account/teleport', 'position' => 30, 'isLogin' => true]
            ]
        ]
    ];

    public function __set(string $typeMenu, array $information): void
    {
        foreach ($information as $key => $value) {
            $this->map[$typeMenu][$key] = $value;
        }
    }

    public function __get(string $typeMenu): array
    {
        $session = new Session;
        $menuRow = 0;

        $uasort = function (array &$menu) {
            uasort($menu, function ($a, $b) {
                if ($a['position'] == $b['position']) {
                    return 0;
                }

                return ($a['position'] < $b['position']) ? -1 : 1;
            });
        };

        foreach ($this->map[$typeMenu] as $key => $value) {
            if (!isset($session->user) && $value['islogin']) {
                unset($this->map[$typeMenu][$key]);
                continue;
            }

            if(!isset($value['position'])) {
                $uasort($this->map[$typeMenu][$key]);
                continue;
            } 
            
            ++$menuRow;
        }

        if($menuRow === count($this->map[$typeMenu])) {
            $uasort($this->map[$typeMenu]);
        }

        return $this->map[$typeMenu];
    }

    public function __isset(string $typeMenu): bool
    {
        return isset($this->map[$typeMenu]);
    }
}

?>