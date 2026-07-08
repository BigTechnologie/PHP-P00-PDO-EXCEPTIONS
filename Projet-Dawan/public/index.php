<?php 

require dirname(__DIR__) . '/vendor/autoload.php';

define('DEBUG_TIME', microtime(true));

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

// http://localhost/articles?page=1&sort=asc&category=php
if(isset($_GET['page']) && $_GET['page'] === '1') { // $_GET = ['page' => '1', 'sort' => 'asc', 'category' => 'php']
    $uri = explode('?', $_SERVER['REQUEST_URI'])[0]; // [0, 1]
    $get = $_GET; // Copier les paramètres GET
    unset($get['page']);
    $query = http_build_query($get); // Reconstruction 
    if(!empty($query)) {
        $uri = $uri . '?' . $query;
    }
    http_response_code(301);
    header('Location: ' . $uri);
    exit();
}

// Configuration et utilisateur du routeur
$router = new App\Router(dirname(__DIR__) . '/views');
$router
    ->get('/', 'article/index', 'home')
    
    ->run(); // On lance notre routeur

