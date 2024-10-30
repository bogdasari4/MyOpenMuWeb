<?php
if(isset($_GET['step'])) $_GET['step'] = intval($_GET['step']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="Bogdan Reva">
        <title>MyOpenMuWeb Install</title>

        <link rel="shortcut icon" href="/Install/favicon.ico" type="image/x-icon">
        <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    </head>
    <body>
        <div class="col-lg-8 mx-auto p-4 py-md-5">
            <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
                <a href="https://getbootstrap.com/" class="d-flex align-items-center text-body-emphasis text-decoration-none">
                    <svg class="bi me-2" width="40" height="32">
                    <use xlink:href="#bootstrap"></use>
                    </svg>
                    <span class="fs-4">Installing CMS for OpenMu</span>
                </a>
            </header>
            <main>
                <?php
                    if($_GET['step'] == 0 || $_GET['step'] > 3)
                    {
                ?>
                <h1 class="text-body-emphasis">Get started with MyOpenMyWeb</h1>
                <p class="fs-5 col-md-8">Click install to get started.</p>
                <div class="mb-5">
                    <a href="?step=1" class="btn btn-primary btn-lg px-4">Install</a>
                </div>
                <?php
                    } else {
                        define('__ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))));

                        $StepDir = str_replace('\\', '/', __DIR__) . '/Step/';
                        $StepPage = match($_GET['step']) 
                        {
                            1 => $StepDir . 'One.php',
                            2 => $StepDir . 'Two.php'
                        };

                        include_once($StepPage);
                    }
                ?>
            </main>
        </div>
    </body>
</html>
