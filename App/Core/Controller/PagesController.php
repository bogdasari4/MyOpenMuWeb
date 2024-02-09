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
        'main' => ['namespace' => 'App\\Pages\\Main', 'file' => __ROOT_APP_PAGE . 'Main.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Main.html']],
        'ranking' => ['namespace' => 'App\\Pages\\Ranking', 'file' => __ROOT_APP_PAGE . 'Ranking.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Ranking.html']],
        'about' => ['namespace' => 'App\\Pages\\About', 'file' => __ROOT_APP_PAGE . 'About.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'About.html']],
        'downloads' => ['namespace' => 'App\\Pages\\Downloads', 'file' => __ROOT_APP_PAGE . 'Downloads.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Downloads.html']],
        'character' => ['namespace' => 'App\\Pages\\Character', 'file' => __ROOT_APP_PAGE . 'Character.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Character.html']],
        'guild' => ['namespace' => 'App\\Pages\\Guild', 'file' => __ROOT_APP_PAGE . 'Guild.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Guild.html']],
        'signup' => ['namespace' => 'App\\Pages\\SignUp', 'file' => __ROOT_APP_PAGE . 'SignUp.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'SignUp.html']],
        'sigin' => ['namespace' => 'App\\Pages\\SigIn', 'file' => __ROOT_APP_PAGE . 'sigin.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'SigIn.html']],
        'account' => ['namespace' => 'App\\Pages\\Account', 'file' => __ROOT_APP_PAGE . 'Account.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'Account.html']],
        'logout' => ['namespace' => 'App\\Pages\\LogOut', 'file' => __ROOT_APP_PAGE . 'LogOut.php', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES, 'name' => 'LogOut.html']]
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