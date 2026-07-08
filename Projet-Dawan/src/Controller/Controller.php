<?php 

namespace App\Controller;

use App\Controller\Exception\NotFoundException;
use PDO;

abstract class Controller {

    protected PDO $pdo;
    protected ?string $table = null;
    protected ?string $class = null;

    public function __construct(PDO $pdo)
    {
        // Vérifie que la propriété $table est défine
        if($this->table === null) {
            throw new \Exception("La class " . get_class($this) . " n'a pas de propriété \$table");
        }
        // Vérifie que la propriété $class est définie
        if($this->class === null) {
            throw new \Exception("La class " . get_class($this) . " n'a pas de propriété \$class");
        }

        $this->pdo = $pdo;
    }

    public function find(int $id)
    {
        $query = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $query->execute(['id' => $id]);

        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $result = $query->fetch();

        if($result === false) {
            throw new NotFoundException($this->table, $id);
        }

        return $result;
    }

    /**
     * Permet de vérifier si une valeur existe dans la table
     * @param mixed $value valeur associée au champ
     */
    public function exists(string $field, $value, ?int $except = null): bool 
    {
        $sql = "SELECT COUNT(id) FROM {$this->table} WHERE $field = ?";

        $params = [$value];

        // On vérifie si un identifiant à exclure ($except) a été fourni
        if($except !== null) {
            $sql .= " AND id != ?";

            $params[] = $except;
        }
        $query = $this->pdo->prepare($sql);

        $query->execute($params);

        return (int)$query->fetch(PDO::FETCH_NUM)[0] > 0;
    }

    public function all(): array 
    {
        $sql = "SELECT * FROM {$this->table}";

        return $this->pdo->query($sql, PDO::FETCH_CLASS, $this->class)->fetchAll();
    }

    public function delete(int $id)
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $ok = $query->execute([$id]);

        if($ok === false) {
            throw new \Exception("Impossible de supprimer l'enregistrement $id dans la table {$this->table}");
        }

    }

    // Permet de créer un enregistement
    public function create(array $data): int 
    {
        $sqlFields = [];
        foreach($data as $key => $value) {
            $sqlFields[] = "$key = :$key"; // $name = :$name
        }

        $query = $this->pdo->prepare("INSERT INTO {$this->table} SET " . implode(', ', $sqlFields));
        $ok = $query->execute($data);

        if($ok === false) {
            throw new \Exception("Impossible de créer l'enregistrement dans la table {$this->table}");
        }
        // On retourne l'id du dernier enregistrement
        return (int)$this->pdo->lastInsertId();
    }

    // Permet de modifier un enregistrement
    public function update (array $data, int $id)
    {
        $sqlFields = [];
        foreach($data as $key => $value) {
            $sqlFields[] = "$key = :$key"; // $name = :$name
        }

        $query = $this->pdo->prepare("UPDATE {$this->table} SET " . implode(', ', $sqlFields) . " WHERE id = :id");
        $ok = $query->execute(array_merge($data, ['id' => $id]));

        if($ok === false) {
            throw new \Exception("Impossible de modifier l'enregistrement dans la table {$this->table}");
        }
    }

    // Permet de recuperer tous les enregistrements sous forme d'un tableau d'objets d'une classe spécifiée
    public function queryAndFetchAll(string $sql): array
    {
        return $this->pdo->query($sql, PDO::FETCH_CLASS, $this->class)->fetchAll();
    }
}