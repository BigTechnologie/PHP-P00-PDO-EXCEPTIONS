<?php 

class Voleur extends Personnage {
    function volALaTire (): void 
    {
        echo "$this->nom dépuille l'ennemi !";
    }
}