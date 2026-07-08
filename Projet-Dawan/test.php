<?php

use App\Connection;

require 'vendor/autoload.php';

try {
    $pdo = Connection::getPDO();

    echo "Connection à la base de données réussie !";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}