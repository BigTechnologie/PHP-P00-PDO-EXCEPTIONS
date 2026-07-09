<?php

use App\Connection;
use App\Controller\ArticleController;

$title = 'Dawan Info';

$pdo = Connection::getPDO();

$table = new ArticleController($pdo);

// Récupération des articles paginés
[$articles, $pagination] = $table->findPaginated();

// Génération de l'URL de la page d'accueil via le routeur
$link = $router->url('home');

?>

<h1>Dawan Info</h1>

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
