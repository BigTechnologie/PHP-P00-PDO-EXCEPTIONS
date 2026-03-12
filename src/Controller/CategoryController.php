<?php
namespace App\Controller;

use App\Controller\Controller;
use App\Model\Category;

use \PDO;

final class CategoryController extends Controller {

    // Définition du nom de la table associée
    protected $table = "category";
    // Définition de la classe modèle associée
    protected $class = Category::class;

    // Méthode permettant d’hydrater les articles passés en paramètre avec leurs catégories associées.
    public function hydrateArticles (array $articles): void // [4,8,9] -> "4,8,9"
    {
        
        $articlesByID = [];
        
        foreach($articles as $article) {
             
            $article->setCategories([]); 
            
            $articlesByID[$article->getID()] = $article; 
        }

        $categories = $this->pdo
            ->query('SELECT c.*, ac.article_id 
                    FROM article_category ac
                    JOIN category c ON c.id = ac.category_id
                    WHERE ac.article_id IN (' . implode(',', array_keys($articlesByID)) . ')'
            )->fetchAll(PDO::FETCH_CLASS, $this->class); 

        foreach($categories as $category) {
            $articlesByID[$category->getArticleID()]->addCategory($category);
        }
    }


}