<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
//die(phpinfo());
set_include_path(get_include_path()
    . PATH_SEPARATOR . 'Controllers'
    . PATH_SEPARATOR . 'Models'
    . PATH_SEPARATOR . 'Templates'
    . PATH_SEPARATOR . 'View');

spl_autoload_register(function ($class) {
    $path = str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once($path);
    }
});

session_start();
//session_destroy();

$front = new Controllers\FrontController;
$front->route();
