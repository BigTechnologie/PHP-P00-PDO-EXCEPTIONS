<?php 

namespace App\Model;

use App\Helpers\Text;
use DateTime;

class Article {
    private ?int $id = null;
    private ?string $slug = null;
    private ?string $name = null;
    private ?string $content = null;
    private ?string $created_at = null;
    /**
     * Liste des catégories associées à l'article
     * @var Category[] tableau d'objet category
     */
    private array $categories = [];
    private ?string $image = null; // Exple : "photo.jpg"
    // Permet d'indiquer si une nouvelle image est en attente d'upload(true = upload à traiter, si non false)
    private bool $pendingUpload = false;
    // Va contenir l'ancienne image
    private ?string $oldImage = null;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return new DateTime($this->created_at);
    }

    public function setCreatedAt(?string $date): self
    {
        $this->created_at = $date;

        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        // Cas 1 : Si $image est un tableau
        if(is_array($image) && !empty($image['tmp_name'])) {
            if(!empty($this->image)) {
                $this->oldImage = $this->image;
            }
            $this->pendingUpload = true;
            $this->image = $image['tmp_name'];
        }

        // Cas 2 : Si $image est une chaine de caractère
        if(is_string($image) && !empty($image)) {
            $this->image = $image;
        }

        return $this;
    }

    public function getOldImage(): ?string
    {
        return $this->oldImage;
    }

    // Permet de retourner le contenu mis en forme (HTM sécurisé)
    public function getFormattedContent(): ?string
    {
        return nl2br(htmlentities($this->content));
    }

    // Permet de retourner un extrait du contenu (60 caractères au max)
    public function getExcerpt(): ?string 
    {
        if($this->content  === null) {
            return null;
        }
        return nl2br(htmlentities(Text::excerpt($this->content, 60)));
    }

    // Permet de retourner un tableau des identifiants des catégories associées à mon objet Article
    public function getCategoriesIds(): array 
    {
        $ids = [];
        foreach($this->categories as $category) {
            $ids[] = $category->getID();
        }
        return $ids;
    }

    // Permet d'ajouter une catégorie à un article et d'établir la relation inverse
    public function addCategory(Category $category): void 
    {
        $this->categories[] = $category; // setArticle()
        $category->setArticle($this);
    }

    // Permet de récupérer l'URL d'une image
    public function getImageURL(string $format): ?string 
    {
        if(empty($this->image)) {
            return null;
        }
        // On retourne : /uploads/posts/photo1_thumb.jpg
        return '/uploads/posts/' . $this->image . '_' . $format . '.jpg';
    }

    // Permet d'indiquer si une image est en attente d'upload
    public function shouldUpload(): bool
    {
        return $this->pendingUpload;
    }

}