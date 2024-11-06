<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Controller;

/**
 * The main controller for storing all basic pages.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class PagesController {
    
    /**
     * An array of system data about engine pages.
     * @var array
     */
    private array $map = [
        'main' => ['namespace' => 'App\\Pages\\Main', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Main.html']],
        'ranking' => ['namespace' => 'App\\Pages\\Ranking', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Ranking.html']],
        'about' => ['namespace' => 'App\\Pages\\About', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'About.html']],
        'downloads' => ['namespace' => 'App\\Pages\\Downloads', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Downloads.html']],
        'character' => ['namespace' => 'App\\Pages\\Character', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Character.html']],
        'guild' => ['namespace' => 'App\\Pages\\Guild', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Guild.html']],
        'signup' => ['namespace' => 'App\\Pages\\SignUp', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'SignUp.html']],
        'sigin' => ['namespace' => 'App\\Pages\\SigIn', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'SigIn.html']],
        'account' => ['namespace' => 'App\\Pages\\Account', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Account.html']],
        'logout' => ['namespace' => 'App\\Pages\\LogOut', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'LogOut.html']]
    ];

    /**
     * Add or change page data using the page key.
     * @param string $pageName
     * The page key for recording data.
     * @param array $information
     * Information that will be assigned to our key.
     * @return void
     */
    public function __set(string $pageName, array $information): void
    {
        $this->map[$pageName] = $information;
    }

    /**
     * Reading the page data.
     * @param string $pageName
     * Page key for receiving data.
     * @return array
     */
    public function __get(string $pageName): array
    {
        return $this->map[$pageName];
    }

    /**
     * Check the existence of page data by key.
     * @param string $pageName
     * Key of the page to check.
     * @return bool
     */
    public function __isset(string $pageName): bool
    {
        return isset($this->map[$pageName]);
    }
}

?>