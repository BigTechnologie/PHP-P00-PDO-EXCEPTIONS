<?php 

namespace App\Validators;

use App\Controller\CategoryController;
use App\Validators\AbstractValidator;



class CategoryValidator extends AbstractValidator {
    public function __construct(array $data, CategoryController $table, ?int $id = null)
    {
        parent::__construct($data);

        $this->validator->rule('required', ['name', 'slug']);

        $this->validator->rule('lengthBetween', ['name', 'slug'], 3, 200);

        $this->validator->rule('slug', 'slug');

        $this->validator->rule(function($fieds, $value) use ($table, $id) {
            return !$table->exists($fieds, $value, $id);
        }, ['slug', 'name'], 'Cette valeur est déjà utilisée');
        
    }

}