<?php

try {
    if(!@include_once('App/Application.php')) throw new Exception('1');
    $App = new App;
    $App->getLoader();
} catch(Exception $e) {
    exit($e->getMessage());
}

?>