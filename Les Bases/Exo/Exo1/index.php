<?php 

include 'Class/Autoloader.php';

$guerrier = new Guerrier("Alexandre", 100, 10);
$mage = new Mage("Gandal", 50, 15);
$voleur = new Voleur("Bil", 15, 5);

// Test
$mage->attaque($voleur);
