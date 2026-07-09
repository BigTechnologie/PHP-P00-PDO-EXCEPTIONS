<?php 
namespace App\Controller;

use App\Model\Article;
use App\PaginatedQuery;
use App\Controller\Controller;

/**
 * Cette class permet de faire des requêtes au niveau de nos articles
 * final car cette class n'a pas vocation à être héritée
 * Elle hérite de `Controller` qui contient les méthodes génériques (create, update, etc.)
*/
final class ArticleController extends Controller {

    // Nom de la table liée aux articles
    protected ?string $table = "article";
    
    // Classe du modèle associé
    protected ?string $class = Article::class;

    /**
     * Met à jour un article existant en base de données
     * @param Article $article L'article à mettre à jour
     * @return void
     */
    public function updateArticle (Article $article): void 
    {
        // Appelle la méthode update du parent avec les données de l'article
        $this->update([
            'name' => $article->getName(),
            'slug' => $article->getSlug(),
            'content' => $article->getContent(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'image' => $article->getImage()
        ], $article->getID()); // L'ID est utilisé pour cibler l'article à modifier
    }

    /**
     * Crée un nouvel article en base de données.
     * @param Article $article L'article à enregistrer
     * @return void
     */
    public function createArticle (Article $article): void 
    {
        // Appelle la méthode create du parent et récupère l'ID généré
        $id = $this->create([
            'name' => $article->getName(),
            'slug' => $article->getSlug(),
            'content' => $article->getContent(),
            'image' => $article->getImage(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        $article->setID($id); // Met à jour l'objet Article avec son ID
    }

    /**
     * Associe des catégories à un article.
     * @param int $id L'ID de l'article
     * @param array $categories Tableau d'ID de catégories à associer
     * @return void
     */
    public function attachCategories (int $id, array $categories) 
    {
        // Supprime toutes les anciennes associations catégorie/article
        $this->pdo->exec('DELETE FROM article_category WHERE article_id = ' . $id);

        // Prépare une requête d'insertion
        $query = $this->pdo->prepare('INSERT INTO article_category SET article_id = ?, category_id = ?');

        // Exécute l'insertion pour chaque catégorie sélectionnée
        foreach($categories as $category) {
            $query->execute([$id, $category]);
        }
    }

    /**
     * Récupère tous les articles paginés
     * @return array<array|PaginatedQuery> [articles, objet de pagination]
     */
    public function findPaginated () 
    {
        // Création d'une requête paginée avec tri par date décroissante
        $paginatedQuery = new PaginatedQuery(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
            $this->pdo
        );

        // Récupère les articles de la page courante
        $articles = $paginatedQuery->getItems(Article::class);

        // Hydrate les articles avec leurs catégories (liaison avec CategoryController)
        (new CategoryController($this->pdo))->hydrateArticles($articles);

        return [$articles, $paginatedQuery]; // Retourne les articles + info pagination
    }

    /**
     * Récupère les articles paginés pour une catégorie spécifique. 
     * 
     * @param int $categoryID L'ID de la catégorie
     * @return array<array|PaginatedQuery> [articles, objet de pagination]
     */
    public function findPaginatedForCategory (int $categoryID) 
    {
        // Requête SQL pour filtrer les articles appartenant à une catégorie spécifique
        $paginatedQuery = new PaginatedQuery(
            "SELECT a.*
                FROM {$this->table} a 
                JOIN article_category ac ON ac.article_id = a.id
                WHERE ac.category_id = {$categoryID}
                ORDER BY created_at DESC",
            "SELECT COUNT(category_id) FROM article_category WHERE category_id = {$categoryID}"
        );

        // Récupère les articles paginés
        $articles = $paginatedQuery->getItems(Article::class);

        // Hydrate les articles avec leurs catégories
        (new CategoryController($this->pdo))->hydrateArticles($articles);
        // Retourne le tableau d’articles et la pagination
        return [$articles, $paginatedQuery]; 
    }





}