<?php 
/** @var \App\Model\Article $article */
/** @var \App\Router $router */

$categories = array_map(function ($category) use ($router) {

    $url = $router->url('category', [
        'id' => $category->getID(),
        'slug' => $category->getSlug()
    ]);

    return <<<HTML
    <a href="{$url}"> {$category->getName()} </a>
HTML;
}, $article->getCategories());

?>

<div class="card mb-3">

    <!-- Image de l'article -->
    <?php if($article->getImage()): ?>
        <img src="<?= $article->getImageURL('small') ?>" class="card-img-top" alt="">
    <?php endif ?>

    <div class="card-body">
        <!-- Titre de l'article -->
        <h5> <?= htmlentities($article->getName()) ?> </h5>

        <p class="text-muted">
            <!-- Date de création formatée -->
            <?= $article->getCreatedAt()->format('d F Y') ?>

            <!-- Affichage des catégories si elles existent -->
            <?php if(!empty($article->getCategories())): ?>

                ::
                <!-- Affichage des catégories sous forme de lien -->
                <?= implode(', ', $categories) ?>

            <?php endif ?>

        </p>

        <p> <?= $article->getExcerpt() ?> </p>

        <p>
            <a href="<?= $router->url('article', [
                'id' => $article->getID(),
                'slug' => $article->getSlug()
            ]) ?>" class="btn btn-primary">
                Voir plus
            </a>
        </p>

    </div>

</div>