<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */

try {

    /**
     * We catch system exceptions and load the engine.
     */
    if(!@include_once('App/Application.php')) throw new Exception('Failed to load application file.');

    $App = new App;
    $App->loader();

} catch(Exception $e) {

    exit($e->getMessage());
    
}

?>