<?php 

namespace App\Controller;

use App\Controller\Controller;
use App\Model\Article;

class ArticleController extends Controller {
    protected $table = "article";
    protected $class = Article::class;

    public function updateArticle(Article $article): void 
    {
        $this->update([
            'name' => $article->getName(),
            'slug' => $article->getSlug(),
            'content' => $article->getContent(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'image' => $article->getImage()
        ], $article->getID());
    }

}