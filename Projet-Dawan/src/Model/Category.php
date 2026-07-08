<?php 

namespace App\Model;

class Category {
    private ?int $id = null;
    private ?string $slug = null;
    private ?string $name = null;

    // stocke l'identifiant de l'article associé (clé étrangère)
    private ?int $article_id = null;

    // Objet Article lié à cette catégorie
    private ?Article $article = null;

    public function getID(): ?int
    {
        return $this->id;
    }

    public function setID(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // Recupère l'identifiant de l'article associé à la catégorie
    public function getArticleId(): ?int
    {
        return $this->article_id;
    }

    // Permet de définir l'identifiant de l'article associé
    public function setArticleId(?int $article_id): self
    {
        $this->article_id = $article_id;

        return $this;
    }

    // Récupère l'objet Article lié
    public function getArticle(): ?Article 
    {
        return $this->article;
    }

    // Associe un article à cette catégorie
    public function setArticle(Article $article): self
    {
        $this->article = $article;
        return $this;
    }

}