<?php

namespace App\Controller;

use PDO;
use App\Controller\Exception\NotFoundException;

abstract class Controller {

    protected PDO $pdo;   // Instance PDO (connexion base de données)
    protected ?string $table = null;  // Nom de la table associée à cette classe
    protected ?string $class = null;  // Classe associée aux objets récupérés

    // Le constructeur est une méthode spéciale appelée automatiquement lorsqu’un objet est créé à partir de la classe.
    // Quand on crée un objet User, le constructeur permet de lui attribuer directement des valeurs sans avoir besoin d'appeler des méthodes set.
    // Le constructeur prévoit aussi des valeurs par défaut.
    public function __construct(PDO $pdo)
    {
        // Vérifie que la propriété $table est définie
        if ($this->table === null) {
            throw new \Exception("La class " . get_class($this) . " n'a pas de propriété \$table");
        }
        // Vérifie que la propriété $class est définie
        if ($this->class === null) {
            throw new \Exception("La class " . get_class($this) . " n'a pas de propriété \$class");
        }
        // Initialise la propriété $pdo avec l'instance passée en paramètre
        $this->pdo = $pdo; 
    }

    // Le polymorphisme, c’est le fait de pouvoir appeler la même méthode sur des objets de classes différentes, et que chaque objet réagisse à sa manière.
    // Ici find() est la même méthode, mais chaque classe va l’implémenter différemment.
    // Cette methode récupère un enregistrement par son identifiant id.
    public function find (int $id) 
    {
        $query = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id'); // id est un paramètre nommé, sécurisé contre les injections SQL
        $query->execute(['id' => $id]); // On exécute la requête en liant la valeur $id au paramètre :id
        //On dit à PDO : Quand tu récupères une ligne, instancie un objet de la classe définie dans $this->class
        //Les colonnes de la BDD seront mappées automatiquement sur les propriétés publiques (ou accédées via des setters si disponibles)
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        //On récupère une seule ligne du résultat (On cherche par id, donc unique). Le résultat est un objet de la classe $this->class, ou bien false si aucun résultat trouvé
        $result = $query->fetch(); 

        if ($result === false) { // Si aucun résultat n’a été trouvé : On lève une exception personnalisée NotFoundException, en passant le nom de la table et l’ID
            throw new NotFoundException($this->table, $id); // Cela permet de gérer proprement l’erreur (ex: page 404)
        }
        return $result; // Si tout va bien, on retourne l’objet trouvé
    }

    /**
     * Vérifie si une valeur existe dans la table
     * 
     * @param string $field Champs à rechercher
     * @param mixed $value Valeur associée au champs
     */
    public function exists (string $field, $value, ?int $except = null): bool
    {
        // On selectionne le nombre d'enregistrement qui corresponde sur la table recuperée, et on lui demandera que la valeur du champ corresponde
        //On construit une requête SQL dynamique. Le ? est un paramètre préparé pour sécuriser la requête (anti injection SQL). $field est directement injecté dans  
        $sql = "SELECT COUNT(id) FROM {$this->table} WHERE $field = ?";
        // tableau qui contient les parametres à utiliser dans la requete preparée. $field = ?: est le systeme de requete préparée, car je contrôle les 
        //params et non les valeurs qui proviennet de l'utilisateur, On ne peux avoir un champ de nom dynamique dans 1 requete preparée

        //Un tableau $params est initialisé avec la valeur fournie ($value). Ce tableau sera utilisé pour passer les valeurs des paramètres à la requête préparée.
        $params = [$value]; 

          // On vérifie si un identifiant à exclure ($except) a été fourni.
        if ($except !== null) { // Si un ID à exclure est fourni : 
            //Si un identifiant à exclure est présent, cette ligne ajoute à la requête SQL une condition pour exclure cet identifiant.
            $sql .= " AND id != ?"; // Je rajoute à ma requete un second WHERE, l'id doit être different de l'id qui est passé en parametre

            //La valeur de l'identifiant à exclure est ajoutée au tableau $params, qui sera utilisé comme paramètre dans la requête préparée.
            $params[] = $except; // On rajoute au tab des param qu'on va envoyer à notre requete preparée, la valeur qui correspond à l'id
        }
        $query = $this->pdo->prepare($sql); // On prépare la requête sql avec PDO (sécurisée)

        //On lui envoi les parametres. On execute la requete en lui passant comme parametre les parms de valeurs
        $query->execute($params); // On exécute la requête avec tous les paramètres fournis ($value et éventuellement $except)

        // Je lui demande de recuperer les informations sous forme de tableau numerique, je recupère le premier element[0], et il faut qu'il soit > 0
        return (int)$query->fetch(PDO::FETCH_NUM)[0] > 0; // Si c'est > 0 on n'a bien les enregistrements, si non NON
    }

    // Cette méthode permet de recuperer tous les enregistrements
    public function all (): array
    {
        // On selectionne tous les champs et on utilise le nom de la table
        $sql = "SELECT * FROM {$this->table}";

        // On recupère l'instance de PDO, on lui donne en parametre notre $sql
        // et comment on veut recuperer les resultats, on fait un fetchAll pour recuperer tous les enregistrements et on retourne 
        return $this->pdo->query($sql, PDO::FETCH_CLASS, $this->class)->fetchAll();
    }

      // Cette methode permet de supprimer, elle prend en parametre l'id de l'element que l'on veut supprimer
    public function delete (int $id) 
    {
        //prépare une requête SQL DELETE pour supprimer l'enregistrement de la table spécifiée ($this->table) où l'identifiant (id) 
        //est égal à celui fourni en paramètre. La méthode prepare est utilisée pour préparer la requête SQL.
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");

        //Cette ligne exécute la requête préparée avec l'identifiant fourni. La méthode execute prend un tableau d'arguments à remplacer dans la requête préparée.
        $ok = $query->execute([$id]);

        //Si l'exécution a échoué (retourne false), cela signifie que la suppression n'a pas réussi.
        if ($ok === false) {
            throw new \Exception("Impossible de supprimer l'enregistrement $id dans la table {$this->table}");
        }
    }

    // Pour crée un enregistrement on n'a besoin de definir un tableau contenant les champs que l'on
    //Souhaite utiliser, ce qui implioque que cette methode prendra un tableau de données
    public function create (array $data): int //$data représentant les champs et les valeurs de l'enregistrement à insérer.
    {
        //On Initialise un tableau $sqlFields qui sera utilisé pour stocker les champs et les valeurs à insérer dans la requête SQL.
        $sqlFields = [];

        //Cette boucle parcourt le tableau associatif $data et construit une liste de champs et valeurs dans le format approprié pour la requête SQL.
        foreach ($data as $key => $value) {
            $sqlFields[] = "$key = :$key"; // On rajoute au tableau. C'est comme si on ecrivait: $name = :$name.
        }

        // Prépare une requête SQL INSERT INTO pour insérer les données dans la table spécifiée ($this->table). 
        //La méthode implode est utilisée pour concaténer les champs et valeurs avec une virgule.
        $query = $this->pdo->prepare("INSERT INTO {$this->table} SET " . implode(', ', $sqlFields));
        $ok = $query->execute($data);
        if ($ok === false) {
            throw new \Exception("Impossible de créer l'enregistrement dans la table {$this->table}");
        }
        // On retourne l'id du dernier enregistrement
        return (int)$this->pdo->lastInsertId(); 
    }

    // Il prend un tableau de données mais également l'id de l'element que l'on souhaite mettre à jour
    public function update (array $data, int $id) 
    {
        // Initialisons un tableau $sqlFields qui sera utilisé pour stocker les champs et les valeurs à mettre à jour dans la requête SQL.
        $sqlFields = [];
        foreach ($data as $key => $value) {
            $sqlFields[] = "$key = :$key"; 
        }

        //La clause WHERE indique quelle ligne de la table doit être mise à jour en fonction de l'identifiant.
        $query = $this->pdo->prepare("UPDATE {$this->table} SET " . implode(', ', $sqlFields) . " WHERE id = :id");

        $ok = $query->execute(array_merge($data, ['id' => $id]));

        if ($ok === false) {
            throw new \Exception("Impossible de modifier l'enregistrement dans la table {$this->table}");
        }
        // Pas besoin de faire un retour car on est dans le cas d'un update
    }

     /**
     * Cette méthode récupérer tous les résultats sous la forme d'un tableau d'objets d'une classe spécifiée
     */
    public function queryAndFetchAll (string $sql): array
    {
        return $this->pdo->query($sql, PDO::FETCH_CLASS, $this->class)->fetchAll();
    }




}