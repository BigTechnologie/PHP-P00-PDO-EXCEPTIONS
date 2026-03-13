<?php 

use App\Connection;
use App\Controller\ArticleController;
use App\Controller\CategoryController;

$id = (int)$params['id'];
$slug = $params['slug'];

$pdo = Connection::getPDO();

$category = (new CategoryController($pdo))->find($id);

if($category->getSlug() !== $slug) {
    $url = $router->url('category', ['slug' => $category->getSlug(), 'id' => $id]);
    http_response_code(301);
    header('Location: ' . $url);
}

$title = "Catégorie {$category->getName()}";

[$articles, $paginatedQuery] = (new ArticleController($pdo))->findPaginatedForCategory($category->getID());

$link = $router->url('category', ['id' => $category->getID(), 'slug' => $category->getSlug()]);
?>

<h1><?= htmlentities($title) ?></h1>

<div class="row">

    <?php foreach($articles as $article): ?>

        <div class="col-md-3">
            <?php require dirname(__DIR__) . '/article/card.php' ?>
        </div>

    <?php endforeach ?>

</div>

<div class="d-flex justify-content-between my-4">
    <?= $paginatedQuery->previousLink($link); ?>
    <?= $paginatedQuery->nextLink($link); ?>
</div>

