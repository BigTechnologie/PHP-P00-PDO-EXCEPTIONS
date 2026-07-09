<?php

use App\Auth;
use App\Connection;

use App\Controller\ArticleController;

Auth::check();

$title = "Administration";
$pdo = Connection::getPDO();
$link = $router->url('admin_articles');
[$articles, $pagination] = (new ArticleController($pdo))->findPaginated();
?>

<?php if (isset($_GET['delete'])): ?>
<div class="alert alert-success">
    L'enregistrement a bien été supprimé
</div>
<?php endif ?>

<table class="table">
    <thead>
        <th>#</th>
        <th>Titre</th>
        <th>
            <a href="<?= $router->url('admin_article_new') ?>" class="btn btn-primary">Nouveau</a>
        </th>
    </thead>
    <tbody>
        <?php foreach($articles as $article): ?>
        <tr>
            <td>#<?= $article->getID() ?></td>
            <td>
                <a href="<?= $router->url('admin_article', ['id' => $article->getID()]) ?>">
                <?= htmlentities($article->getName()) ?>
                </a>
            </td>
            <td>
                <a href="<?= $router->url('admin_article', ['id' => $article->getID()]) ?>" class="btn btn-primary">
                    Editer
                </a>
                <form action="<?= $router->url('admin_article_delete', ['id' => $article->getID()]) ?>" method="POST"
                    onsubmit="return confirm('Voulez vous vraiment effectuer cette action ?')" style="display:inline">
                    <button type="submit"  class="btn btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<div class="d-flex justify-content-between my-4">
    <?= $pagination->previousLink($link); ?>
    <?= $pagination->nextLink($link); ?>
</div>