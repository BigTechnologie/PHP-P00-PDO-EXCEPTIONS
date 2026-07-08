<?php
namespace App;

use \PDO;

class PaginatedQuery {

    // Sont les variables dont on n'a besoin car ils changent d'une page à une autre
    private string $query; // Requête SQL pour récupérer les éléments à afficher
    private string $queryCount;   // Requête SQL pour compter le nombre total d’éléments
    private PDO $pdo; // Instance de la connexion à la base de données
    private int $perPage;  // Nombre d’éléments à afficher par page (pagination)
    private ?int $count = null;   // Nombre total d’éléments (calculé une seule fois)
    private ?array $items = null;  // Tableau des éléments récupérés (mis en cache pour éviter de relancer la requête)

    /**
     * Constructeur de la classe
     * @param string $query : Requête SQL principale (ex. SELECT * FROM posts)
     * @param string $queryCount : Requête SQL pour compter les éléments (ex. SELECT COUNT(id) FROM posts)
     * @param PDO|null $pdo : Instance PDO ou null (dans ce cas, la connexion sera récupérée automatiquement)
     * @param int $perPage : Nombre d’éléments par page (par défaut : 12)
     */
    public function __construct(
        string $query,
        string $queryCount,
        ?PDO $pdo = null, 
        int $perPage = 12
    )
    {
        $this->query = $query; 
        $this->queryCount = $queryCount; 
        $this->pdo = $pdo ?? Connection::getPDO(); // Utilise l’objet PDO fourni ou la connexion par défaut
        $this->perPage = $perPage;      
    }

    /**
     * Calcule le nombre total de pages disponibles
     * @return int : Nombre total de pages
    */
    private function getPages (): int //retourne un entier représentant le nombre total de pages.
    {
        //si la propriété $count de l'objet actuel est null. // Si le nombre d'éléments n'a pas encore été calculé
        //Si c'est le cas, cela signifie que le nombre total d'éléments n'a pas encore été récupéré.
        if ($this->count === null) {
            // Effectue une requête SQL pour obtenir le nombre total d'éléments en utilisant la propriété $queryCount de l'objet actuel.
            // Exécute la requête de comptage
            $this->count = (int)$this->pdo
                // Effectue une requête SQL pour obtenir le nombre total d'éléments en utilisant la propriété $queryCount de l'objet actuel.
                ->query($this->queryCount)
                 //je demande de recuperer les informations sous forme de tableau numérique. et le premier élément de ce tableau ([0]) est extrait. 
                 //Ce nombre est ensuite converti en entier et stocké dans la propriété $count pour un éventuel usage ultérieur. 
                ->fetch(PDO::FETCH_NUM)[0]; // Récupère la première colonne (le COUNT) comme entier
        }

        //Calcule le nombre total de pages en divisant le nombre total d'éléments par le nombre d'éléments par page ($perPage)
        return ceil($this->count / $this->perPage); // Retourne le nombre total de pages en fonction des éléments par page, en utilisant la fonction ceil() pour arrondir au-dessus.
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
        //Demande de recuperer un entier positif dans l'url.'page': et c'est l'entier qui correspond  à la page
        // Récupère l'entier positif depuis l'URL avec une valeur par défaut de 1
        return \App\URL::getPositiveInt('page', 1);
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
        //si la propriété $items de l'objet actuel est null. Si c'est le cas, cela signifie que les éléments n'ont pas encore été récupérés.
        if ($this->items === null) {
            $currentPage = $this->getCurrentPage(); //Appelle la méthode getCurrentPage() pour obtenir le numéro de la page actuelle.
            $pages = $this->getPages(); //Appelle une méthode getPages() pour obtenir le nombre total de pages disponibles.
            // Si la page demandée est supérieure au nombre de pages existantes
            if ($currentPage > $pages) {
                throw new \Exception('Cette page n\'existe pas');
            }

            // Calcule l’offset pour la requête SQL (ex: page 2 → offset 12)
            $offset = $this->perPage * ($currentPage - 1); // Si on est sur la page 1 alors $offset = 0, $offset de 12 je suis sur la page 2, ainsi de suite

            // Ici on va Effectuer une requête SQL en utilisant l'objet PDO ($this->pdo). La requête SQL est construite à partir de la propriété $query de 
            // l'objet actuel en ajoutant une clause LIMIT et OFFSET pour la pagination 
            // Exécute la requête avec LIMIT et OFFSET pour paginer les résultats
            $this->items = $this->pdo->query(
                $this->query . 
                " LIMIT {$this->perPage} OFFSET $offset") // Permet d'avoir les articles recents
                // Hydrate les résultats en objets de type $classMapping
                ->fetchAll(PDO::FETCH_CLASS, $classMapping); // Le mode et le type de class à utiliser. Pour reccuperer les resultats je vais demander d'utiliser $classMapping  
        }
        return $this->items; //Retourne le tableau d'éléments (soit récupérés à partir de la propriété $items, soit nouvellement récupérés et stockés dans $items).
    }

    /**
     * Génère le lien HTML vers la page précédente
     * @param string $link : Lien de base (ex: /blog)
     * @return string|null : Lien HTML vers la page précédente ou null si on est sur la première page
     */

    public function previousLink(string $link): ?string
    {
        $currentPage = $this->getCurrentPage(); // Récupère le numéro de la page actuelle en appelant la méthode getCurrentPage de l'objet actuel. 
         // Si on est sur la première page, aucun lien à retourner
        if ($currentPage <= 1) return null; //si on est sur une page <= à la page 1, alors ne fait rien
        // Si on est sur une page > 2, on ajoute ?page=N-1 au lien
        if ($currentPage > 2) $link .= "?page=" . ($currentPage - 1); //tu prends $link, tu rajoutes le numéro de la page - 1. Si on est sur la page 4 il s'affichera 3... 

        // Retourne le lien HTML vers la page précédente
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
        $currentPage = $this->getCurrentPage(); // Récupère le numéro de la page actuelle en appelant la méthode getCurrentPage de l'objet actuel. 
        $pages = $this->getPages(); // Récupère le nombre total de pages en appelant la méthode getPages de l'objet actuel.

        // Si on est sur la dernière page, aucun lien à retourner
        if ($currentPage >= $pages) return null; //Vérifie si la page actuelle est la dernière page. retourne null car il n'y a pas de page suivante.
        // Ajoute ?page=N+1 au lien
        $link .= "?page=" . ($currentPage + 1); //tu prends $link, tu rajoutes le numéro de la page + 1. Si on est sur la page 4 il s'affichera 5...

        //Utilise la syntaxe Heredoc pour retourner une chaîne de caractères représentant un lien HTML formaté avec le lien généré pour la page suivante.
        // Retourne le lien HTML vers la page suivante
        return <<<HTML
            <a href="{$link}" class="btn btn-primary ml-auto">Page suivante &raquo;</a> <!-- &raquo: >= -->
             <!-- margin-left auto: pour aligner l'élément à droite. -->
HTML;
    }
}