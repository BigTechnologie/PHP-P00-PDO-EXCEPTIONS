<?php 

namespace App\Validators;

use App\Controller\ArticleController;

class ArticleValidator extends AbstractValidator {

    public function __construct(array $data, ArticleController $table, array $categories, ?int $articleID = null)
    {
        parent::__construct($data);

        $this->validator->rule('required', ['name', 'slug']);
        $this->validator->rule('lengthBetween', ['name', 'slug'], 3, 200);

        $this->validator->rule('slug', 'slug');

        $this->validator->rule('subset', 'categories_ids', array_keys($categories));

        $this->validator->rule('image', 'image');

        $this->validator->rule(function($fieds, $value) use ($table, $articleID) {
            return !$table->exists($fieds, $value, $articleID);
        }, ['slug', 'name'], 'Cette valeur est déjà utilisée');
        
    }

}