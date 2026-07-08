<?php 

class Personnage {
    protected string $nom;
    protected int $pdv;
    protected int $pa;

    public function __construct(string $nom, int $pdv, int $pa)
    {
        $this->nom = $nom;
        $this->pdv = $pdv;
        $this->pa = $pa;
    }
    
    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPdv(): int
    {
        return $this->pdv;
    }

    public function setPdv(int $pdv)
    {
        return $this->pdv = $pdv;
    }

    public function attaque(Personnage $cible): void 
    {
        // Réduit les points de vie de la cible en fonction des points d'attaque de l'attaquant
        $cible->setPdv($cible->getPdv() - $this->pa);

        echo "$this->nom attaque " . $cible->getNom() . " avec $this->pa points de dégâts.";

        // Vérifie si les points de vie de la cible sont inférieurs ou égaux à Zero
        if($cible->getPdv() <= 0) {
            echo $cible->getNom() . " est mort.";
        } else {
            echo $cible->getNom() . " est vivant.";
        }

    }

    
}