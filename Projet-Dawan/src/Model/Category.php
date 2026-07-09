<?php

namespace App\Model;

use App\Model\Article;

/**
 * Classe Category
 * Représente une catégorie liée à un article.
 */
class Category
{
    /**
     * Identifiant unique de la catégorie (clé primaire).
     */
    private ?int $id = null;

    /**
     * Slug de la catégorie (version URL-friendly du nom).
     */
    private ?string $slug = null;

    /**
     * Nom de la catégorie.
     */
    private ?string $name = null;

    /**
     * Identifiant de l'article associé (clé étrangère).
     */
    private ?int $article_id = null;

    /**
     * Objet Article lié à cette catégorie.
     * Relation objet (et non juste ID).
     */
    private ?Article $article = null;

    /**
     * Récupère l'ID de la catégorie.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Définit l'ID de la catégorie.
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Récupère le slug de la catégorie.
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Définit le slug de la catégorie.
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Récupère le nom de la catégorie.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la catégorie.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Récupère l'identifiant de l'article associé.
     */
    public function getArticleId(): ?int
    {
        return $this->article_id;
    }

    /**
     * Définit l'identifiant de l'article associé.
     */
    public function setArticleId(int $article_id): self
    {
        $this->article_id = $article_id;
        return $this;
    }

    /**
     * Récupère l'objet Article lié.
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * Associe un article à cette catégorie.
     */
    public function setArticle(Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}