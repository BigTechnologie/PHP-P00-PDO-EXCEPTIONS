<?php

use Animaux\Chat;
use Animaux\EtreVivant;
use Animaux\Jaguar as AJaguar;
use Vehicules\Jaguar as VJaguar;

include 'Class/Autoloader.php';

$jaguar1 = new AJaguar();

$jaguar1->seNourrir(15); $jaguar1->dormir(24);
var_dump($jaguar1);

//**************************************************************** */

$jaguar2 = new VJaguar("Jaguar", 150000, 'V8', 3);
//var_dump($jaguar2);

$jaguar2->presenter();

echo "<br>";//**************************************************************** */
function nourrirAnimal(EtreVivant $animal, int $quantite)
{
    $animal->seNourrir($quantite);
}

$chat = new Chat();

nourrirAnimal($chat, 100);
