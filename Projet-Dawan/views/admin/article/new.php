<?php 
/** @var array $params */

use App\Attachment\ArticleAttachment;
use App\Auth;
use App\Connection;
use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\HTML\Form;
use App\Model\Article;
use App\ObjectHelper;
use App\Validators\ArticleValidator;


Auth::check();

$pdo = Connection::getPDO();
$errors = [];
$article = new Article();
$categoryTable = new CategoryController($pdo);
$categories = $categoryTable->list();
$article->setCreatedAt(date('Y-m-d H:i:s'));

// Traitement du Formulaire
if(!empty($_POST)) {

    $articleController = new ArticleController($pdo);

    //$data = array_merge($_POST, $_FILES);
    $data = [...$_POST, ...$_FILES];
    $v = new ArticleValidator($data, $articleController, $categories, $article->getID());
    ObjectHelper::hydrate($article, $data, ['name', 'content', 'slug', 'created_at', 'image']);
    if($v->validate()) {
        $pdo->beginTransaction();
        ArticleAttachment::upload($article);
        $articleController->createArticle($article);
        $articleController->attachCategories($article->getID(), $_POST['categories_ids']);
        $pdo->commit();
        header('Location: ' . $router->url('admin_article', ['id' => $article->getID()]) . '?created=1');
        exit();
    } else {
        $errors = $v->errors();
    }
}

$form = new Form($article, $errors);

?>

<?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
        L'article n'a pas pu être enregistré, merci de corriger vos erreurs 
    </div>
<?php endif ?>

<h1>Créer un Article</h1>

<?php require('_form.php') ?>
