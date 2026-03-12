<?php 
require '../vendor/autoload.php';

define('DEBUG_TIME', microtime(true));

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

define('UPLOAD_PATH', __DIR__ .DIRECTORY_SEPARATOR . 'uploads'); // CRUD

// /path?page=1&sort=asc // [2, 8]
if(isset($_GET['page']) && $_GET['page'] === '1') {
    $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
    $get = $_GET;
    unset($get['page']);
    $query = http_build_query($get);
    if(!empty($query)) {
        $uri = $uri . '?' . $query;
    }

    http_response_code(301);
    header('Location: ' . $uri);
    exit();
}

// Configuration et utilisation d'un routeur
$router = new App\Router(dirname(__DIR__) . '/views');
$router
    ->get('/', 'article/index', 'home')
    ->get('/blog/category/[*:slug]-[i:id]', 'category/show', 'category')
    ->get('/blog/[*:slug]-[i:id]', 'article/show', 'article')
    ->match('/login', 'auth/login', 'login')
    ->post('logout', 'auth/logout', 'logout')
    ->run(); // Permet de lancer notre routeur