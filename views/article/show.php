<?php 

use App\Connection;
use App\Controller\ArticleController;
use App\Controller\CategoryController;

$id = (int)$params['id'];
$slug = $params['slug'];

$pdo = Connection::getPDO();

$article = (new ArticleController($pdo))->find($id);

(new CategoryController($pdo))->hydrateArticles([$article]);

if($article->getSlug() !== $slug) {
    $url = $router->url('article', ['slug' => $article->getSlug(), 'id' => $id]);
    http_response_code(301);
    header('Location: ' . $url);
}

?>

<h1> <?= htmlentities($article->getName()) ?> </h1>

<p class="text-muted"> <?= $article->getCreatedAt()->format('d F Y') ?> </p>

<?php foreach($article->getCategories() as $k => $category):
    if ($k > 0):
        echo ', '; 
    endif;

    $category_url = $router->url('category', ['id' => $category->getID(), 'slug' => $category->getSlug()]);
    
    ?> <!-- [cat1, cat2, cat3, cat4] -->

    <!-- Affichons le nom de la catégorie en tant que lien -->
    <a href="<?= $category_url ?>"><?= htmlentities($category->getName()) ?></a>

<?php endforeach ?>

<?php if($article->getImage()): ?>
    <p>
        <img src="<?= $article->getImageURL('large') ?>" style="width: 100%" alt="">
    </p>
<?php endif ?>

<p> <?= $article->getFormattedContent() ?> </p>

