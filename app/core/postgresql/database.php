<?php

if(!@include_once(__ROOT . 'app/core/postgresql/connect.php')) throw new \Exception(__LANG['exception']['core']['postgresql']['database']['connect']);
if(!@include_once(__ROOT . 'app/core/postgresql/query.php')) throw new \Exception(__LANG['exception']['core']['postgresql']['database']['query']);

?>