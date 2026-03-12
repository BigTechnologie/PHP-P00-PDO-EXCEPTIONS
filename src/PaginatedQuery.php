<?php
namespace App;

use \PDO;
use App\Connection;

class PaginatedQuery {
    private $query; 
    private $queryCount;   
    private $pdo; 
    private $perPage; 
    private $count;  
    private $items;  

    /**
     * Constructeur de la classe
     * @param string $query : Requête SQL principale (ex. SELECT * FROM posts)
     * @param string $queryCount : Requête SQL pour compter les éléments (ex. SELECT COUNT(id) FROM posts)
     * @param PDO|null $pdo : Instance PDO ou null (dans ce cas, la connexion sera récupérée automatiquement)
     * @param int $perPage : Nombre d’éléments par page (par défaut : 12)
     */
    public function __construct(
        string $query, // exple : SELECT * FROM products
        string $queryCount, // SELECT COUNT(id) FROM products
        ?PDO $pdo = null, 
        int $perPage = 12
    )
    {
        $this->query = $query; 
        $this->queryCount = $queryCount; 
        $this->pdo = $pdo ?: Connection::getPDO(); 
        $this->perPage = $perPage;      
    }

    /**
     * Calcule le nombre total de pages disponibles // Total = 25; page1 : 12; page2: 12; page3: 1; => 3 pages
     * @return int : Nombre total de pages
    */
    private function getPages (): int 
    {
        if ($this->count === null) {
            $this->count = (int)$this->pdo
                ->query($this->queryCount)
                ->fetch(PDO::FETCH_NUM)[0]; 
        }

        return ceil($this->count / $this->perPage); 
    }

    /**
     * Récupère la page actuelle depuis l’URL (ex: ?page=2)
     * @return int : Numéro de la page actuelle
     * #On definit cette méthode pendant qu'on crée la méthode previousLink()
     * Cette méthode permet d'obtenir le numéro de page actuel à partir de l'URL.
     * 'page': est le nom du paramètre dans l'URL que la méthode tente de récupérer.
     * 1 : Une valeur par défaut à retourner si le paramètre 'page' n'est pas présent dans l'URL, ou s'il n'est pas un entier positif.
    */
    private function getCurrentPage(): int
    {
        return URL::getPositiveInt('page', 1);
    }
    
    /**
     * Récupère les éléments paginés depuis la base de données
     * @param string $classMapping : Nom de la classe à utiliser pour hydrater les objets
     * @return array : Tableau d’objets de type $classMapping
     * Cette méthode permet récupérer  une liste d'éléments dans la base de données en fonction de la page demandée, en utilisant une requête 
     * SQL avec une pagination.
    */
    public function getItems(string $classMapping): array 
    {
        if ($this->items === null) {
            $currentPage = $this->getCurrentPage(); 
            $pages = $this->getPages(); 
            if ($currentPage > $pages) {
                throw new \Exception('Cette page n\'existe pas');
            }

            $offset = $this->perPage * ($currentPage - 1); // sur la page 1 =>  12 * (1 - 1) = 0; sur la page 2 => 12 * (2 - 1) = 12

            $this->items = $this->pdo->query(
                $this->query . 
                " LIMIT {$this->perPage} OFFSET $offset") 
                ->fetchAll(PDO::FETCH_CLASS, $classMapping); 
        }
        return $this->items; 
    }

    /**
     * Génère le lien HTML vers la page précédente
     * @param string $link : Lien de base (ex: /blog)
     * @return string|null : Lien HTML vers la page précédente ou null si on est sur la première page
     */

    public function previousLink(string $link): ?string
    {
        $currentPage = $this->getCurrentPage(); 
         
        if ($currentPage <= 1) return null; 
        
        if ($currentPage > 2) $link .= "?page=" . ($currentPage - 1);  // 3-1 = 2

        return <<<HTML
            <a href="{$link}" class="btn btn-primary">&laquo; Page précédente</a>
HTML;
    }

     /**
     * Génère le lien HTML vers la page suivante
     * @param string $link : Lien de base (ex: /blog)
     * @return string|null : Lien HTML vers la page suivante ou null si on est sur la dernière page
     */
    public function nextLink(string $link): ?string
    {
        $currentPage = $this->getCurrentPage(); 
        $pages = $this->getPages(); 

        if ($currentPage >= $pages) return null; 
       
        $link .= "?page=" . ($currentPage + 1); 

        return <<<HTML
            <a href="{$link}" class="btn btn-primary ml-auto">Page suivante &raquo;</a> 
HTML;
    }
}