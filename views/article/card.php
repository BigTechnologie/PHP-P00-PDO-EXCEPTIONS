<?php

$categories = array_map(function ($category) use ($router) {
    
    $url = $router->url('category', ['id' => $category->getID(), 'slug' => $category->getSlug()]);

    return <<<HTML
     <a href="{$url}">{$category->getName()}</a>
HTML;
}, $article->getCategories());
?>

<div class="card mb-3">  
    <?php if ($article->getImage()): ?>
        
        <img src="<?= $article->getImageURL('small') ?>" class="card-img-top">
    <?php endif ?>

    <div class="card-body">
        
        <h5 class="card-title"><?= htmlentities($article->getName()) ?></h5>
        
        <p class="text-muted">

            <?= $article->getCreatedAt()->format('d F Y') ?> 

            <?php if (!empty($article->getCategories())): ?>
            ::
            <?= implode(', ', $categories) ?>
            <?php endif ?>
        </p>
        <p><?= $article->getExcerpt() ?></p>
        <p>
            <a href="<?= $router->url('article', ['id' => $article->getID(), 'slug' => $article->getSlug()]) ?>" class="btn btn-primary">Voir plus</a>
        </p>
    </div>
</div>