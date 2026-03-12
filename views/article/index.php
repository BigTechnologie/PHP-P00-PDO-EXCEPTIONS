<?php
use App\Connection;
use App\Controller\ArticleController;


$title = 'Dawan Info plus';

$pdo = Connection::getPDO();

$table = new ArticleController($pdo);

[$articles, $pagination] = $table->findPaginated();

$link = $router->url('home');
?>

<h1>Dawan News</h1>

<div class="row">
    <?php foreach($articles as $article): ?>
    <div class="col-md-3">
        <?php require 'card.php' ?>
    </div>
    <?php endforeach ?>
</div>

<div class="d-flex justify-content-between my-4">
    <?= $pagination->previousLink($link); ?>
    <?= $pagination->nextLink($link); ?>
</div>



