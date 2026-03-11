<?php 

namespace App\Controller\Exception;

class NotFoundException extends \Exception {
    public function __construct(string $table, $id)
    {
        $this->message = "Aucun enregistrement ne correspond à #$id dans la '$table'";
    }
}