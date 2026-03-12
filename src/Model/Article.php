<?php

namespace App\Model;

use App\Helpers\Text;
use DateTime;

class Article
{
    private $id;

    private $slug;

    private $name;

    private $content;

    private $created_at;

    private $categories = []; // Tableau de catégories associées à l'article

    private $image; // Nom du fichier image lié à l'article
    private $oldImage; // Ancienne image (utile lors d’une mise à jour) 

    private $pendingUpload = false;  // Indique si une image est en attente d’upload

    public function getID(): ?int
    {
        return $this->id;
    }

    public function setID(int $id): self
    {
        
        $this->id = $id;

        return $this;

    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return new DateTime($this->created_at);
    }

    public function setCreatedAt(string $date): self
    {
        $this->created_at = $date;

        return $this;
    }

    // Retourne le tableau des catégories associées à l'article
    public function getCategories(): array
    {
        return $this->categories; 
    }

    // Cette méthode permet de définir les catégories associées à un article.
    public function setCategories(array $categories): self 
    {
        $this->categories = $categories; 

        return $this; 
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    // Méthode pour définir une image.
    public function setImage($image): self
    {
        //Si $image est un tableau et qu’il contient une clé 'tmp_name' non vide (typique de $_FILES['image'])
        if (is_array($image) && !empty($image['tmp_name'])) {
            
            if (!empty($this->image)) {
                $this->oldImage = $this->image;
            }
        
            $this->pendingUpload = true; 
            $this->image = $image['tmp_name']; 
        }
        //Autre cas : si $image est une chaîne non vide (ex. un nom de fichier comme "photo.jpg"), alors on le stocke directement.
        if (is_string($image) && !empty($image)) {
            
            $this->image = $image;
        }

        return $this; 
    }

    // Getter pour l’ancienne image 
    public function getOldImage(): ?string
    {
        return $this->oldImage;
    }

    // Retourne le contenu mis en forme (HTML sécurisé avec sauts de ligne)
    public function getFormattedContent(): ?string
    {
        return nl2br(htmlentities($this->content));
    }

    // Retourne un extrait du contenu (60 caractères max), sécurisé pour le HTML
    public function getExcerpt(): ?string
    {
        if ($this->content === null) {
            return null;
        }
        return nl2br(htmlentities(Text::excerpt($this->content, 60)));
    }

    // Permet de retourner un tableau des identifiants des catégories associées à l’objet article
    public function getCategoriesIds(): array 
    {
        $ids = []; 
        foreach ($this->categories as $category) { 
            $ids[] = $category->getID(); 
        }
        return $ids; 
    }

    // Permet d'ajouter une catégorie à l'article et établit la relation inverse 
    public function addCategory(Category $category): void 
    {
        $this->categories[] = $category; 
        
        $category->setArticle($this); 
    }

    // méthode pour récupérer l'URL d'une image. elle attend une chaîne (ex: "thumb", "medium", "large", etc.). 
    public function getImageURL(string $format): ?string 
    {
        if (empty($this->image)) { 
            return null;
        }

        // Cas 1 : image distante (Picsum ou autre URL)
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Cas 2 : image locale 
        return '/uploads/posts/' . $this->image . '_' . $format . '.jpg';
        // Exemple : si $this->image = 'photo1' et $format = 'thumb', ça retourne : /uploads/posts/photo1_thumb.jpg
    }

    // Méthode qui indique s’il faut uploader quelque chose. Elle retourne un booléen (true ou false).
    public function shouldUpload(): bool
    {
        return $this->pendingUpload; 
    }


}