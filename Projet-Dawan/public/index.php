<?php 

require dirname(__DIR__) . '/vendor/autoload.php';

//define('DEBUG_TIME', microtime(true));

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

define('UPLOAD_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'uploads');

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
    ->get('/blog/category/[*:slug]-[i:id]', 'category/show', 'category')
    ->get('/blog/[*:slug]-[i:id]', 'article/show', 'article')
    ->match('/login', 'auth/login', 'login')
    ->post('/logout', 'auth/logout', 'logout')
    
    // ADMIN
    // Gestion des articles
    ->get('/admin', 'admin/article/index', 'admin_articles')
    ->match('/admin/article/[i:id]', 'admin/article/edit', 'admin_article')
    ->post('/admin/article/[i:id]/delete', 'admin/article/delete', 'admin_article_delete')
    ->match('/admin/article/new', 'admin/article/new', 'admin_article_new')
    // Gestion des catégories
    ->get('/admin/categories', 'admin/category/index', 'admin_categories')
    ->match('/admin/category/[i:id]', 'admin/category/edit', 'admin_category')
    ->post('/admin/category/[i:id]/delete', 'admin/category/delete', 'admin_category_delete')
    ->match('/admin/category/new', 'admin/category/new', 'admin_category_new')
    ->run();

