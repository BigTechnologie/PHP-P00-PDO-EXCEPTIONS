<?php

namespace App\Model;

use App\Helpers\Text;
use DateTime;

// Définition de la classe Article représentant un article de blog ou une publication
class Article
{
    // Identifiant unique de l'article (peut être null tant que l'article n'est pas en base)
    private ?int $id = null;

    // Slug de l'article (URL SEO-friendly, ex: "mon-article-exemple")
    private ?string $slug = null;

    // Titre ou nom de l'article
    private ?string $name = null;

    // Contenu principal de l'article (texte HTML ou brut)
    private ?string $content = null;

    // Date de création de l'article (stockée sous forme de chaîne, convertie ensuite en DateTime)
    private ?string $created_at = null;

    /**
     * Liste des catégories associées à l'article
     * @var Category[] tableau d'objets Category
     */
    private array $categories = [];

    // Nom du fichier image associé à l'article (ex: "photo.jpg")
    private ?string $image = null;

    // Ancienne image conservée lors d'une mise à jour (utile pour suppression ou historique)
    private ?string $oldImage = null;

    // Indique si une nouvelle image est en attente d'upload (true = upload à traiter)
    private bool $pendingUpload = false;

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

    /**
     * Retourne le tableau des catégories associées à l'article
     * @return Category[] 
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Définit les catégories associées à un article.
     *
     * @param Category[] $categories
     * @return self
     */
    public function setCategories(array $categories): self 
    {
        $this->categories = $categories; 

        return $this; 
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * méthode pour définir une image.
     * @param mixed $image 
     * @return Article
     */
    public function setImage($image): self
    {
        
        if (is_array($image) && !empty($image['tmp_name'])) {
            
            if (!empty($this->image)) {
                $this->oldImage = $this->image;
            }
           
            $this->pendingUpload = true; 
            $this->image = $image['tmp_name']; 
        }
        
        if (is_string($image) && !empty($image)) {
            
            $this->image = $image;
        }

        return $this; 
    }

    // Getter pour l’ancienne image (pas de setter prévu)
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

    // Retourne un tableau des identifiants des catégories associées à l’objet article
    public function getCategoriesIds(): array 
    {
        $ids = []; 
        foreach ($this->categories as $category) { 
            $ids[] = $category->getID(); 
        }
        return $ids; 
    }

    // Ajoute une catégorie à l'article et établit la relation inverse
    public function addCategory(Category $category): void 
    {
        
        $this->categories[] = $category; 
        // On appelle la méthode setArticle() de la catégorie en lui passant l’objet courant ($this).
        $category->setArticle($this); 
    }

    // Retourne l’URL de l’image avec un format donné (ex: "small", "large")
    public function getImageURL(string $format): ?string 
    {
        if (empty($this->image)) { 
            return null;
        }
        //Sinon, on construit une URL dynamique vers le fichier image, en fonction du nom de base de l’image ($this->image) et du format demandé.
        return '/uploads/posts/' . $this->image . '_' . $format . '.jpg';
        // Exemple : si $this->image = 'photo1' et $format = 'thumb', ça retourne : /uploads/posts/photo1_thumb.jpg
    }

    // Indique si une image est en attente d'upload // méthode qui indique s’il faut uploader quelque chose. bool : elle retourne un booléen (true ou false).
    public function shouldUpload(): bool
    {
        return $this->pendingUpload; 
    }


}

