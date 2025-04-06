<?php

namespace App\Extensions\Votes;

use App\Core\Adapter\ExtensionsAdapter;

final class Init extends ExtensionsAdapter
{
    protected function load(): void
    {

        // $this->app->menucontroller->ranking = [
        //     'vote' => [
        //         'link' => '/ranking/vote',
        //         'position' => 30,
        //         'islogin' => false,
        //         'title' => match ($this->getLanguageCode()) {
        //             45 => 'Vote',
        //             570 => 'Голоса'
        //         }
        //     ]
        // ];

        // $this->app->subpagescontroller->ranking = [
        //     'vote' => [
        //         'namespace' => '\\App\\Extensions\\Votes\\Render\\Votes',
        //         'template' => [
        //             'path' => __ROOT_APP_EXT . 'Votes/Templates/',
        //             'name' => 'Votes.html'
        //         ]
        //     ]
        // ];
    }
}
