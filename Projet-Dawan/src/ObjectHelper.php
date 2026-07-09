<?php

namespace App;

// Classe utilitaire qui permet d’hydrater dynamiquement un objet à partir d’un tableau de données
class ObjectHelper {

    /**
     * Méthode statique pour hydrater un objet à partir d’un tableau associatif
     * 
     * @param object $object L’objet à hydrater (instance d’une classe avec des setters)
     * @param array $data    Tableau associatif contenant les données à injecter (ex: $_POST)
     * @param array $fields  Liste des champs à hydrater (les clés à prendre dans $data)
     */
    public static function hydrate ($object, array $data, array $fields): void
     {
        // Parcourt tous les champs à hydrater
        foreach($fields as $field) {
            // Transforme le nom du champ en nom de méthode "setX". Exemple : 'first_name' devient 'setFirstName'
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

            // Appelle dynamiquement la méthode setter correspondante sur l’objet
            $object->$method($data[$field]);

            /*
             📌 Remarque de bonne pratique : Avant d’appeler dynamiquement une méthode en PHP, il est prudent de vérifier son existence avec
                method_exists() et éventuellement que la clé existe dans $data pour éviter des erreurs :
                if (method_exists($object, $method) && array_key_exists($field, $data)) {
                    $object->$method($data[$field]);
                }
            */
        }
    }

}

// str_replace('_', ' ', $field) : remplace les underscores par des espaces (ex: first_name → first name).
// ucwords(...) : met une majuscule à chaque mot (ex: first name → First Name).
// str_replace(' ', '', ...) : enlève les espaces (ex: First Name → FirstName).
// Résultat final : 'first_name' devient 'setFirstName', ce qui correspond à un setter.
// $object->$method(...) => appelle dynamiquement la méthode portant ce nom sur l’objet.

/*
$data = ['first_name' => 'Alice', 'last_name' => 'Dupont']; // récupère la valeur associée à la clé $field dans $data
$fields = ['first_name', 'last_name'];
$user = new User();

et que la classe User contient :
public function setFirstName($firstName) {
    $this->first_name = $firstName;
}
public function setLastName($lastName) {
    $this->last_name = $lastName;
}
// Alors : $method = 'setFirstName'; $object->$method('Alice');

ObjectHelper::hydrate($user, $data, $fields);
// équivaut à : $user->setFirstName('Alice'); $user->setLastName('Dupont');
*/