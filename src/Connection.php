<?php 

namespace App;

use PDO;

class Connection {

    public static function getPDO(): PDO
    {
        return new PDO('mysql:dbname=products2026_w11;host=127.0.0.1', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

}