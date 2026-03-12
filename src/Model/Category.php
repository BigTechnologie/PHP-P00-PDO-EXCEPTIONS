<?php
namespace App\Model;

class Category {

    private $id;

    private $slug;

    private $name;

    private $article_id;

    private $article;

    public function getID (): ?int {
        return $this->id;
    }

    public function setID (int $id): self
    {
        $this->id = $id;
        
        return $this;
    }

    public function getSlug (): ?string {
        return $this->slug;
    }

    public function setSlug (string $slug): self
    {
        $this->slug = $slug;
        
        return $this;
    }

    public function getName (): ?string {
        return $this->name;
    }

    public function setName (string $name): self
    {
        $this->name = $name;
        
        return $this;
    }

    //Sert à récupérer l'identifiant de l'article (article_id).
    public function getArticleID (): ?int
    {
        return $this->article_id; 
    }

    // Methode permettant d'associer un article à une catégorie. 
    public function setArticle (Article $article) {
        $this->article = $article; 
    }

}