<?php
namespace App\Controller;

use App\Controller\Controller;
use App\Model\Category;

use \PDO;


final class CategoryController extends Controller {

    protected ?string $table = "category"; // Nom de la table en BDD
    protected ?string $class = Category::class; // Classe du modèle associé

    
    /**
     * Hydrate les objets Article passés en paramètre avec leurs catégories associées.
     * @param \App\Model\Article[] $articles Liste des articles à enrichir avec leurs catégories
     */
    public function hydrateArticles (array $articles): void
    {
        $articlesByID = []; // Tableau associatif pour retrouver les articles par ID

        // On initialise les catégories pour chaque article
        foreach($articles as $article) {
            $article->setCategories([]); // Vide les catégories existantes
            $articlesByID[$article->getID()] = $article; // Indexation par ID
        }

        // On récupère toutes les catégories liées aux articles via la table de liaison
        $categories = $this->pdo
            ->query('SELECT c.*, ac.article_id
                    FROM article_category ac
                    JOIN category c ON c.id = ac.category_id
                    WHERE ac.article_id IN (' . implode(',', array_keys($articlesByID)) . ')'
            )->fetchAll(PDO::FETCH_CLASS, $this->class); // On récupère les catégories en tant qu'objets Category

        // On ajoute chaque catégorie à l'article concerné
        foreach($categories as $category) {
            // `getArticleID()` est une méthode personnalisée dans le modèle Category qui retourne l'ID de l'article lié
            $articlesByID[$category->getArticleID()]->addCategory($category);
        }
    }

    /**
     * Récupère toutes les catégories, triées par ID décroissant.
     * @return array Liste des catégories
     */
    public function all (): array
    {
        // Appelle une méthode héritée qui exécute la requête et retourne un tableau d'objets Category
        return $this->queryAndFetchAll("SELECT * FROM {$this->table} ORDER BY id DESC");
    }

    /**
     * Récupère les catégories sous forme de tableau associatif [id => nom], triées par nom.
     * @return array Tableau associatif ID => Nom
     */
    public function list (): array
    {
        // Récupère toutes les catégories, triées par nom
        $categories = $this->queryAndFetchAll("SELECT * FROM {$this->table} ORDER BY name ASC");
        $results = [];

        // On transforme le tableau d’objets en tableau associatif
        foreach($categories as $category) {
            $results[$category->getID()] = $category->getName();
        }
        return $results;
        
    }


}