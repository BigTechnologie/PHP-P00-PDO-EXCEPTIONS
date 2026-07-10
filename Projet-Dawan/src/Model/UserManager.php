<?php 

namespace App\Model;

use PDO;

class UserManager {
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Permet d'ajouter un utilisateur en BDD
    public function addUser(User $user): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO user (username, password) VALUES (?, ?)');
        return $stmt->execute([
            $user->getUsername(),
            $user->getPassword()
        ]);
    }

    // Permet de vérifier si un utilisateur existe dans la BDD avec le même username
    public function userExists(string $username): bool 
    {
        $stmt = $this->pdo->prepare('SELECT id FROM user WHERE username = ?');
        $stmt->execute([$username]);

        return (bool) $stmt->fetch();
    }


}