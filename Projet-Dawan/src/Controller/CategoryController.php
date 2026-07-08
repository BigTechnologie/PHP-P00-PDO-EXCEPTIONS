<?php 

namespace App\Controller;

use App\Controller\Controller;
use App\Model\Category;
use PDO;

final class CategoryController extends Controller {

    protected ?string $table = "category";
    protected ?string $class = Category::class;

    // Permet d'hydrater les objets articles passés en parametre avec leurs catégories associées
    public function hydrateArticles(array $articles): void 
    {
        $articlesByID = [];

        foreach($articles as $article) {
            $article->setCategories([]);
            $articlesByID[$article->getID()] = $article; // Indexation par ID
        }

        // On recupère toutes les catégories liées aux articles via la table de liaison
        $categories = $this->pdo 
            ->query('SELECT c.*, ac.article_id
                    FROM article_category ac
                    JOIN category c ON c.id = ac.category_id
                    WHERE ac.article_id IN (' . implode(',', array_keys($articlesByID)) . ')'
            )->fetchAll(PDO::FETCH_CLASS, $this->class);
        // On ajoute chaque catégorie à l'article concerné
        foreach($categories as $category) {
            $articlesByID[$category->getArticle()]->addCategory($category);
        }
    }

    // Récupère toutes les catégories, tiées par ID décroissant
    public function all(): array 
    {
        return $this->queryAndFetchAll("SELECT * FROM {$this->table} ORDER BY id DESC");
    }

    // Récupère les catégories sous forme d etableau associatif [id => nom], trié par nom
    public function list(): array 
    {
        $categories = $this->queryAndFetchAll("SELECT * FROM {$this->table} ORDER BY name ASC");

        $results = [];

        // On transforme le tableau d'objet en tableau associatif
        foreach($categories as $category) {
            $results[$category->getID()] = $category->getName();
        }
        return $results;
    }

}