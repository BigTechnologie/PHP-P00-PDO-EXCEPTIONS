<?php 

namespace App\Controller;

use App\Controller\Exception\NotFoundException;
use App\Model\User;

final class UserController extends Controller {
    protected ?string $table = "user";
    protected ?string $class = User::class;

    public function findByUsername(string $username)
    {
        $query = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE username = :username');
        $query->execute(['username' => $username]);

        $query->setFetchMode(\PDO::FETCH_CLASS, $this->class);
        $result = $query->fetch();
        if($result === false) {
            throw new NotFoundException($this->table, $username);
        }

        return $result;
    }
}