<?php 
namespace App\Model;

use App\Helpers\Text;
use DateTime;

class Article {
    private $id;
    private $slug;
    private $name;
    private $content;
    private $created_at;
    private $categories = [];
    private $image;
    private $oldImage;
    private $pendingUpload = false;

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

    public function setImage($image): self
    {
        // Cas 1
        if(is_array($image) && !empty($image['tmp_name'])) {
            if(!empty($this->image)) {
                $this->oldImage = $this->image;
            }

            $this->pendingUpload = true;
            $this->image = $image['tmp_name'];

        }

        // Cas 2 
        if(is_string($image) && !empty($image)) {
            $this->image = $image;
        }

        return $this;
    }
    public function getOldImage(): ?string
    {
        return $this->oldImage;
    }

    public function getFormattedContent(): ?string
    {
        return nl2br(htmlentities($this->content));
    }

    public function getExcerpt(): ?string
    {
        if($this->content === null) {
            return null;
        }
        return nl2br(htmlentities(Text::excerpt($this->content, 60)));
    }

    public function getCategoriesIds(): array 
    {
        $ids = [];
        foreach ($this->categories as $category) {
            $ids[] = $category->getID();
        }
        return $ids;
    }

    public function addCategory(Category $category): void 
    {
        $this->categories[] = $category;
        $category->setArticle($this);
    }

    public function getImageURL(string $format): ?string 
    {
        if(empty($this->image)) {
            return null;
        }

        // Cas 1: Image distante
        if(str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Cas 2: Image locale
        return '/uploads/posts/' . $this->image . '_' . $format . '.jpg';  // /uploads/posts/photo1_thumb.jpg
    }

    public function shouldUpload(): bool
    {
        return $this->pendingUpload;
    }

}