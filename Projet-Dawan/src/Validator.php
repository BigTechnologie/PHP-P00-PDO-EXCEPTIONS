<?php
namespace App;

use finfo; 
use Valitron\Validator as ValitronValidator; 


class Validator extends ValitronValidator { 
    protected static $_lang = "fr";

    /**
    * Constructeur de la classe Validator
    * 
    * @param array $data   Les données à valider
    * @param array $fields Les champs spécifiques à valider
    * @param mixed $lang   (Optionnel) Langue personnalisée
    * @param mixed $langDir (Optionnel) Répertoire de langue personnalisé
    */
    public function __construct($data = array(), $fields = array(), $lang = null, $langDir = null) 
    {
        parent::__construct($data, $fields, $lang, $langDir); 

        self::addRule('image', function($field, $value, array $params, array $fields) {
          
            if ($value['size'] === 0) {
                return true;
            }

            $mimes = ['image/jpeg', 'image/png'];

            $finfo = new finfo(); 

            $info = $finfo->file($value['tmp_name'], FILEINFO_MIME_TYPE); 
            return in_array($info, $mimes); 
        }, 'Le fichier n\'est pas une image valide'); 
    }

    
    /**
     * Méthode surchargée pour enlever le nom du champ dans les messages d'erreur
     * 
     * @param string $field   Le nom du champ
     * @param string $message Le message d'erreur original
     * @param array  $params  Les paramètres éventuels
     * @return string         Le message d'erreur sans le nom du champ
    */
    protected function checkAndSetLabel($field, $message, $params)
    {
        return str_replace('{field}', '', $message);
    }


}