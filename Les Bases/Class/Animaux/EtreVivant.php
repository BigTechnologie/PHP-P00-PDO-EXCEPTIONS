<?php 

namespace Animaux;

interface EtreVivant {
    function seNourrir(int $quantite);
    function dormir(int $duree);
}