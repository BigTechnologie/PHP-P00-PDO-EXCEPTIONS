<?php 

namespace Animaux;

class Jaguar extends Animal {
    public function seNourrir(int $quantite)
    {
        $this->faim += $quantite;
    }
    public function dormir(int $duree)
    {
        $this->fatigue -= $duree;
    }
}