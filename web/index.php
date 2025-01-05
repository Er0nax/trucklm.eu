<?php
session_start();

// define basic constants
use src\App;
use src\services\Router;

define('DS', dirname(DIRECTORY_SEPARATOR));
define('BASE_PATH', dirname(__DIR__));
define('ASSET_PATH', dirname(__DIR__) . DS . 'web' . DS . 'assets');
define('VENDOR_PATH', dirname(__DIR__) . DS . 'vendor');

// autoloader by composer
require VENDOR_PATH . '/autoload.php';

// include all classes
spl_autoload_register(function ($class_name) {
    // Convert namespace to file path and add BASE_PATH
    $file = BASE_PATH . DS . str_replace('\\', DS, $class_name) . '.php';

    if (file_exists($file)) {
        include_once $file;
    }
});

// load dotenv
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// load app
$app = new App();

// load router
$router = new Router();
$router->run();