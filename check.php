<?php

$modules = ['intl', 'pdo_pgsql', 'pgsql', 'zip', 'session', 'gd'];


foreach($modules as $module) {
    echo extension_loaded($module) ? 'Extension <b>' . $module . '</b> <font style="color:green;">is loaded</font><br>' : 'Extension <b>' . $module . '</b> <font style="color:red;">is not loaded</font><br>';
}
?>