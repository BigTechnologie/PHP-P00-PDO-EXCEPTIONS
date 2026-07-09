<?php 
/** @var \App\Model\Article $article */
/** @var array $categories */
// nom
// slug
// champ pour télécharger l'image
// Affichage de l'image actuelle
// Select des catégories
// contenu de l'article
// champ pour la date de creation

?>

<form action="" method="POST" enctype="multipart/form-data">
    <?= $form->input('name', 'Titre'); ?>
    <?= $form->input('slug', 'URL'); ?>
    <div class="row">
        <div class="col-md-9">
            <?= $form->file('image', 'Image à la une'); ?>
        </div>
        <div class="col-md-3">
            <?php if($article->getImage()): ?>
                <img src="<?= $article->getImageURL('small') ?>" alt="" style="width: 100%;">
            <?php endif ?>
        </div>
    </div>
    <?= $form->select('categories_ids', 'Catégories', $categories); ?>
    <?= $form->textarea('content', 'Contenu'); ?>
    <?= $form->input('created_at', 'Date de création'); ?>

    <button class="btn btn-primary">
        <?php if ($article->getID() !== null): ?>
            Modifier 
        <?php else: ?>  
            Créer  
        <?php endif ?>
    </button>

</form>