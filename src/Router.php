<?php
namespace App;

use App\Security\ForbiddenException;

use AltoRouter;

class Router {

    /**
     * Chemin absolu vers le dossier contenant les vues (.php). 
     * @var string
     */
    private $viewPath; 

    /**
     * Instance de la classe AltoRouter, responsable du routage. Permet de faire correspondre l’URL actuelle à une route définie
     * Utilisé dans la méthode run() via $this->router->match()
     * @var AltoRouter
     */
    private $router; 

    public function __construct(string $viewPath) 
    {
        $this->viewPath = $viewPath; 
        $this->router = new AltoRouter(); 
    }

    public function get(string $url, string $view, ?string $name = null): self 
    {
        $this->router->map('GET', $url, $view, $name); 

        return $this; 
    }

    public function post(string $url, string $view, ?string $name = null): self
    {
        $this->router->map('POST', $url, $view, $name);

        return $this; 
    }

    public function match(string $url, string $view, ?string $name = null): self
    {
        $this->router->map('POST|GET', $url, $view, $name);

        return $this;
    }

    public function url (string $name, array $params = []) 
     {
        return $this->router->generate($name, $params);
    }

     
    //Verification auprès du rooter si l'url taper correspond à une de mes routes
    public function run(): self
    { 
        $match = $this->router->match(); 
    
        if ($match === false) {
            $view = 'e404'; 
            $params = []; 
        } else {
            $view = $match['target'] ?: 'e404'; 
            $params = $match['params']; 
        }
    
        $router = $this;

        $isAdmin = strpos($view, 'admin/') !== false;

        $layout = $isAdmin ? 'admin/layouts/default' : 'layouts/default';

        $viewFile = $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';
    
        try {
            if (!file_exists($viewFile)) {
                $view = 'e404';
                $viewFile = $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';

                if (!file_exists($viewFile)) {
                    throw new \Exception("La vue 'e404' est introuvable.");
                }
            }
    
            ob_start();
            require $viewFile; 
            $content = ob_get_clean(); 

            require $this->viewPath . DIRECTORY_SEPARATOR . $layout . '.php';

        } catch (ForbiddenException $e) {
            header('Location: ' . $this->url('login') . '?forbidden=1');
            exit();
        } catch (\Exception $e) {
            echo "Une erreur s'est produite : " . htmlspecialchars($e->getMessage());
            exit();
        }
        return $this;
    }
    
    

}