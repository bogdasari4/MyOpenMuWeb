<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */

 try {
    if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']))
        throw new Exception();

    header('Access-Control-Allow-Origin: ' . $_SERVER['REMOTE_ADDR']);
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Max-Age: 86400');

    if($_SERVER['REQUEST_METHOD'] !== 'GET')
        throw new Exception();

    header('Cache-Control: public, max-age=3600');

    /**
     * We catch system exceptions and load the engine.
     */
    if(!@include_once('App/Application.php')) throw new Exception('Failed to load application file.');

    $App = new App;
    $App->loader($App::ACCESS_API);

} catch(Exception) {

    exit('Access denied');
    
}
?>