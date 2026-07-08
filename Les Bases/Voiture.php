<?php 

class Voiture {

    public string $marque;
    private int $prix;
    protected string $moteur;

    function __construct(string $marque, int $prix, string $moteur)
    {
        $this->marque = $marque;
        $this->prix = $prix;
        $this->moteur = $moteur;
    }

    function getPrix(): int 
    {
        return $this->prix;
    }

    // Permet de définir le prix de la voiture
    function setPrix (int $prix): void 
    {
        $this->prix = $prix;
    }

    static function vroom (): void // NomDeLaClass::methode()
    {
        echo 'VROOM !';
    }
}

// Création d'une nouvelle instance de la classe Voiture
$test = new Voiture('Renault', 10000, 'V22');
//var_dump($test);

//var_dump($test->getPrix());

// $test->setPrix(700);
// var_dump($test);

// Appelle de la méthode static
//$test->vroom();

//Voiture::vroom();

class VoitureSport extends Voiture {
    public function getMoteur(): void 
    {
        echo $this->moteur;
    }
}

//$demo = new VoitureSport('Renault', 5000, 'V12');

// Paramètres nommés (PHP 8+)
$demo = new VoitureSport(prix: 20000, marque: 'Renaults', moteur: 'V46');

$demo->getMoteur();



