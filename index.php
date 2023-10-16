<?php

try {
    define('access', 'index');

    if(!@include_once('app/application.php')) throw new Exception('1');
} catch(Exception $e) {
    exit($e->getMessage());
}

?>