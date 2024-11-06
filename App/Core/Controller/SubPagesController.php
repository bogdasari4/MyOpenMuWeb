<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Controller;

/**
 * The main controller for storing all basic subpages.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class SubpagesController {
    
    /**
     * An array of system data about engine subpages.
     * @var array
     */
    private array $map = [
        'ranking' => [
            'character' => ['namespace' => 'App\\Pages\\Ranking\\Character', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES . 'Ranking/', 'name' => 'Character.html']],
            'guild' => ['namespace' => 'App\\Pages\\Ranking\\Guild', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES . 'Ranking/', 'name' => 'Guild.html']]
        ],
        'account' => [
            'information' => ['namespace' => 'App\\Pages\\Account\\Information', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES . 'Account/', 'name' => 'Information.html']],
            'changepass' => ['namespace' => 'App\\Pages\\Account\\ChangePass', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES . 'Account/', 'name' => 'ChangePass.html']],
            'reset' => ['namespace' => 'App\\Pages\\Account\\Reset', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES . 'Account/', 'name' => 'Reset.html']],
            'addstats' => ['namespace' => 'App\\Pages\\Account\\AddStats', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES . 'Account/', 'name' => 'AddStats.html']],
            'teleport' => ['namespace' => 'App\\Pages\\Account\\Teleport', 'template' => ['path' => __ROOT_TEMP_ACTIVE_PAGES . 'Account/', 'name' => 'Teleport.html']]
        ]
    ];

    /**
     * Add or change subpage data using the subpage key.
     * @param string $subPageName
     * The subpage key for recording data.
     * @param array $information
     * Information that will be assigned to our key.
     * @return void
     */
    public function __set(string $subPageName, array $information): void
    {
        $this->map[$subPageName] = array_merge($this->map[$subPageName], $information);
    }

    /**
     * Reading the subpage data.
     * @param string $subPageName
     * Subpage key for receiving data.
     * @return array
     */
    public function __get(string $subPageName): array
    {
        return $this->map[$subPageName];
    }

    /**
     * Check the existence of subpage data by key.
     * @param string $subPageName
     * Key of the subpage to check.
     * @return bool
     */
    public function __isset(string $subPageName): bool
    {
        return isset($this->map[$subPageName]);
    }
}

?>