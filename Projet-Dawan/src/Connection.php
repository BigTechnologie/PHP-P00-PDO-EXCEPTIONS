<?php 

namespace App;

use PDO;

class Connection {
    public static function getPDO (): PDO 
    {
        return new PDO('mysql:dbname=newsproducts;host=127.0.0.1', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}

/*
// Tableau de la BDD
[
'id' => 1,
'name' => 'Mon article',
'content' => 'Mon contenu'
]

// Mapping avec PHP
$article = new Article ();
$article->id = 1,
$article->name = 'Mon article',
$article->contenu = 'Mon contenu'

// A la fin , on obtient:
Article object
(
[id] => 1
[name] => Mon article
[contenu] => Mon contenu
)

*/

