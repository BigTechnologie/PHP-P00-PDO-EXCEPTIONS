<?php 

class Guerrier extends Personnage {
    function charge (): void 
    {
        echo "$this->nom charge l'ennemi !";
    }
}