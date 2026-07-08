<?php 

namespace Animaux;

class Chat implements EtreVivant {
    public function seNourrir(int $quantite)
    {
        echo "Le chat mange $quantite grammes de pâtée.";
    }
    public function dormir(int $duree)
    {
        echo "Le chat dort pendant $duree heures.";
    }
}