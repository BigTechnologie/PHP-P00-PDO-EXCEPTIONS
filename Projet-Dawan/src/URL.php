<?php
namespace App;

class URL {

    /**
    * Cette méthode permet d'extraire un entier à partir des paramètres de l'URL (query string) via la méthode GET
    *$name : Une chaîne de caractères représentant le nom du paramètre dans la query string.
    *$default : Une valeur optionnelle par défaut de type entier. 
    */
    public static function getInt(string $name, ?int $default = null): ?int
    {
        if (!isset($_GET[$name])) return $default; // vérifie si la clé $name existe dans la query string.
        if ($_GET[$name] === '0') return 0;//Cela gère le cas où la valeur du paramètre est la chaîne de caractères '0' (zéro).Dans ce cas, la méthode retourne explicitement l'entier 0.
    
        //vérifier si la valeur du paramètre est un entier valide. Si la valeur n'est pas un entier valide, la méthode lance une exception avec un message d'erreur.
        if (!filter_var($_GET[$name], FILTER_VALIDATE_INT)) {
            // Exception: Exception est la classe de base pour toutes les exceptions.
            throw new \Exception("Le paramètre '$name' dans l'url n'est pas un entier");
        }
        //Si toutes les vérifications passent, la valeur de la query string est convertie en entier à l'aide de la conversion de type (int) et renvoyée.
        return (int)$_GET[$name];
    }

    /**
     * fonction utilitaire qui utilise la méthode getInt pour récupérer un entier à partir des paramètres de l'URL (query string) et s'assure que 
     * la valeur est un entier positif.
    */
    public static function getPositiveInt(string $name, ?int $default = null): ?int
    {
        // Cette ligne utilise la méthode getInt pour récupérer un entier à partir de la query string. La valeur est stockée dans la variable $param.
        $param = self::getInt($name, $default);
        if ($param !== null && $param <= 0) {
            throw new \Exception("Le paramètre '$name' dans l'url n'est pas un entier positif");
        }
        return $param; // Si toutes les vérifications passent, la valeur récupérée est renvoyée.
    }

}