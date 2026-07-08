<?php 

namespace Animaux;

class Chien extends Animal {
    public function seNourrir(int $quantite)
    {
        $this->faim += $quantite;
    }
    public function dormir(int $duree)
    {
        $this->fatigue -= $duree;
    }
}