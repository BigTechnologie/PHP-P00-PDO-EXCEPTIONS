<?php 
namespace App\Controller;

use App\Model\Article;
use App\PaginatedQuery;
use App\Controller\Controller;

final class ArticleController extends Controller {

    // Déclaration du nom de la table associée
    protected $table = "article";

    // Déclaration de la classe modèle associée
    protected $class = Article::class;

    public function updateArticle (Article $article): void 
    {
        $this->update([
            'name' => $article->getName(),
            'slug' => $article->getSlug(),
            'content' => $article->getContent(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'image' => $article->getImage()
        ], $article->getID()); // Ici On précise l'identifiant de l'article à modifier
    }

    public function createArticle (Article $article): void 
    {
        $id = $this->create([
            'name' => $article->getName(),
            'slug' => $article->getSlug(),
            'content' => $article->getContent(),
            'image' => $article->getImage(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        // On met à jour l'identifiant de l'objet Article avec l'ID retourné après insertion
        $article->setID($id);
    }

    /**
     * Méthode permettant d'associer des catégories à un article.
    */
    public function attachCategories (int $id, array $categories) 
    {
        // On supprime les anciennes associations catégories de cet article
        $this->pdo->exec('DELETE FROM article_category WHERE article_id = ' . $id); // article(id), category(id), article_category(article_id, category_id)

        // Préparation de la requête pour insérer de nouvelles associations
        $query = $this->pdo->prepare('INSERT INTO article_category SET article_id = ?, category_id = ?');
        
        foreach($categories as $category) {
            $query->execute([$id, $category]);
        }
    }

    /**
     * Méthode permettant de récupérer des articles paginés.
    */
    public function findPaginated () : array
    {
        // Création d'une instance de PaginatedQuery avec la requête de sélection et de comptage
        $paginatedQuery = new PaginatedQuery(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
            $this->pdo
        );

        // Récupération des articles sous forme d'objets Article
        $articles = $paginatedQuery->getItems(Article::class);

        // Hydratation des catégories associées à chaque article via la méthode hydrateArticles() définie dans CategoryController
        (new CategoryController($this->pdo))->hydrateArticles($articles);

        // Retour du tableau contenant les articles et l'objet de pagination
        return [$articles, $paginatedQuery];
    }

    // Permettant de récuperer des articles paginés pour une catégorie spécifique
    public function findPaginatedForCategory(int $categoryID)
    {
        $paginatedQuery = new PaginatedQuery(
            "SELECT a.*
            FROM {$this->table} a
            JOIN article_category ac ON ac.article_id = a.id
            WHERE ac.category_id = {$categoryID}
            ORDER BY created_at DESC",
            "SELECT COUNT(category_id) FROM article_category WHERE category_id = {$categoryID}"
        );

        $articles = $paginatedQuery->getItems(Article::class);
        
        (new CategoryController($this->pdo))->hydrateArticles($articles);
    }

    

}