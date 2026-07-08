<?php 

namespace App\Controller;

use App\Model\Article;
use App\PaginatedQuery;

final class ArticleController extends Controller {
    protected ?string $table = "article";
    protected ?string $class = Article::class;

    // Méthode qui permet de mettre à jour un enregistrement existant en BDD
    public function updateAticle(Article $article): void 
    {
        $this->update([
            'name' => $article->getName(),
            'slug' => $article->getSlug(),
            'content' => $article->getContent(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'image' => $article->getImage(),
        ], $article->getID());
    }

    public function createArticle(Article $article): void
    {
        // Appelle de la méthode create du parent et on récupère l'ID généné
        $id = $this->create([
            'name' => $article->getName(),
            'slug' => $article->getSlug(),
            'content' => $article->getContent(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'image' => $article->getImage(),
        ]);
        // Mise à jour de l'objet Article avec son ID
        $article->setID($id);
    }

    // Méthode qui permet d'associer des catégories à un article
    public function attachCategories (int $id, array $categories)
    {
        // On supprime toutes les anciennes associations catégorie/article
        $this->pdo->exec('DELETE FROM article_category WHERE article_id = ' . $id);
        
        // On prépare une requête d'insertion
        $query = $this->pdo->prepare('INSERT INTO article_category SET article_id = ?, category_id = ?');

        // On exécute l'insertion pour chaque catégorie
        foreach($categories as $category) {
            $query->execute([$id, $category]);
        }
    }

    // Méthode permettant de récuperer tous les articles paginés
    public function findPaginated()
    {
        $paginatedQuery = new PaginatedQuery(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
            $this->pdo
        );

        // Les articles de la page courante
        $articles = $paginatedQuery->getItems(Article::class);

        // On hydrate les articles avec leurs catégories (liaison)
        (new CategoryController($this->pdo))->hydrateArticles($articles);

        // On retourne les articles + info pagination
        return [$articles, $paginatedQuery];
    }

    // Méthode permettant de récuperer les articles paginés pour une catégorie spécifique
    public function findPaginatedForCategory(int $categoryID)
    {
        // Requête SQL pour filtrer les articles appartenent à une catégorie spécifique
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

        // On retourne le tableau d'articles et la pagination
        return [$articles, $paginatedQuery];
    }

}