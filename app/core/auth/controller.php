<?php
if(!@include_once(__ROOT . 'app/core/auth/validation.php')) throw new \Exception(__LANG['exception']['core']['auth']['controller']['validation']);
if(!@include_once(__ROOT . 'app/core/auth/sigin.php')) throw new \Exception(__LANG['exception']['core']['auth']['controller']['sigin']);
if(!@include_once(__ROOT . 'app/core/auth/signup.php')) throw new \Exception(__LANG['exception']['core']['auth']['controller']['signup']);

?>