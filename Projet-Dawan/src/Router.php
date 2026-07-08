<?php
namespace App;

use App\Security\ForbiddenException;
use AltoRouter;

class Router {

    /**
     * Chemin absolu vers le dossier contenant les vues (.php). Utilisé pour construire les chemins des fichiers à inclure dans run(). Par exemple : /var/www/html/views
     * @var string
     */
    private $viewPath; 

    /**
     * Instance de la classe AltoRouter, responsable du routage. Permet de faire correspondre l’URL actuelle à une route définie
     * Utilisé dans la méthode run() via $this->router->match()
     * @var AltoRouter
     */
    private $router; // Est une variable de type Altorouter

    public function __construct(string $viewPath) // Est le constructeur de la class, qui a pour parametre le chemin vers les vues
    {
        $this->viewPath = $viewPath; // Stocke le chemin vers les vues
        $this->router = new AltoRouter(); // Stocke le routeur passé en paramètre
    }

    public function get(string $url, string $view, ?string $name = null): self // param: l'url, la vue qu'on souhaite chargée, le nom de notre route
    {
        $this->router->map('GET', $url, $view, $name); // map en GET l'url(url appelée en GET), tu charges la vue et son nom

        return $this; // On renvoit l'objet en cours, ce qui permet de dire que le retour ça sera la class, d'ou self à la ligne 24
    }

    public function post(string $url, string $view, ?string $name = null): self
    {
        $this->router->map('POST', $url, $view, $name);

        return $this; //La méthode renvoie l'instance actuelle de la classe.
    }

    public function match(string $url, string $view, ?string $name = null): self
    {
        $this->router->map('POST|GET', $url, $view, $name);

        return $this;
    }

    public function url (string $name, array $params = []) //$name: Le nom de la route, $params = []: un tableau de parametres par defaut vide
     {
        //La méthode utilise un objet $router pour générer l'URL associée à la route spécifiée par le nom ($name). Les paramètres de la route sont fournis à partir du tableau $params.
        return $this->router->generate($name, $params);
    }

     /**
     * verification auprès du rooter si l'url taper correspond à une de mes routes
     * Contrairement à switch, la comparaison est une vérification d'identité (===) plutôt qu'un contrôle d'égalité faible (==)
     * 
    */
    public function run(): self
    {
        // Interroge le router pour savoir si l'URL demandée correspond à une route définie 
        $match = $this->router->match(); 
        //var_dump($match);
        // Si aucune route ne correspond, on prépare une vue d'erreur 404
        if ($match === false) {
            $view = 'e404'; // Fichier de vue à afficher en cas d'erreur 404
            $params = []; // Aucun paramètre à passer à la vue
        } else {
            // Si une route est trouvée, on récupère le nom de la vue cible ou 'e404' si la cible est vide
            $view = $match['target'] ?: 'e404'; 
            $params = $match['params']; // Paramètres dynamiques de la route
        }
    
        // On assigne l'objet courant à une variable pour usage interne (utile dans certaines vues anonymes)
        $router = $this;

        // Détection si la vue concerne une zone admin en vérifiant la présence de 'admin/' dans le nom de la vue
        $isAdmin = strpos($view, 'admin/') !== false;

        // Choix du layout en fonction du type de page (admin ou publique)
        $layout = $isAdmin ? 'admin/layouts/default' : 'layouts/default';

        // Construction du chemin complet vers le fichier de la vue à inclure
        $viewFile = $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';
    
        try {
            // Vérifie si le fichier de la vue existe
            if (!file_exists($viewFile)) {
                // Si le fichier de vue n'existe pas, on redirige vers la vue 404
                $view = 'e404';
                $viewFile = $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';

                // Si la vue 404 est aussi introuvable, on lève une exception
                if (!file_exists($viewFile)) {
                    throw new \Exception("La vue 'e404' est introuvable.");
                }
            }
    

            // Démarre la temporisation de sortie pour capturer le contenu de la vue
            ob_start();

            require $viewFile; // Inclusion de la vue // 'views/home.php
            
            $content = ob_get_clean(); // Récupère le contenu généré dans la variable $content

            // Inclusion du layout global (public ou admin) avec le contenu de la vue
            require $this->viewPath . DIRECTORY_SEPARATOR . $layout . '.php';

        } catch (ForbiddenException $e) {
            // En cas d'accès interdit, on redirige vers la page de login avec un paramètre d'erreur
            header('Location: ' . $this->url('login') . '?forbidden=1');
            exit();
        } catch (\Exception $e) {
            // Capture de toute autre erreur non prévue
            echo "Une erreur s'est produite : " . htmlspecialchars($e->getMessage());
            exit();
        }
    
        // Retourne l'objet courant pour permettre le chaînage de méthodes
        return $this;
    }
    
    

}

