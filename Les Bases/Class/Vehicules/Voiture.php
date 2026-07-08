<?php 

namespace Vehicules;

use Override;

class Voiture extends Vehicule {
    private int $prix;
    private string $moteur;
    private int $portes;

    public function __construct(string $marque, int $prix, string $moteur, int $portes)
    {
        //return parent::__construct($marque);
        $this->marque = $marque;
        $this->prix = $prix;
        $this->moteur = $moteur;
        $this->portes = $portes;
    }

    function getPortes ()
    {
        return $this->portes;
    }

    function getPrix ()
    {
        return $this->prix;
    }

    function presenter()
    {
        echo "$this->marque, $this->prix" . " $, $this->moteur, $this->portes portes";
    }

}