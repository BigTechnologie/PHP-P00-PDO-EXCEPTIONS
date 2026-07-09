<?php
namespace App\Attachment;

use Intervention\Image\ImageManager;
use App\Model\Article;
use Intervention\Image\Drivers\Gd\Driver;

class ArticleAttachment {

    const DIRECTORY = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'posts';

    /**
     * Méthode permettant d’uploader une image pour un article.
     * Elle génère plusieurs formats et remplace l'ancienne image si nécessaire.
     *
     * @param Article $article Article concerné par l'upload
    */
    public static function upload (Article $article) 
    {
        // Récupération de l’image associée à l’article
        $image = $article->getImage();
        
        if (empty($image) || $article->shouldUpload() === false) {
            return;
        }

        // Définition du dossier de destination pour l'upload
        $directory = self::DIRECTORY;
        
        if (file_exists($directory) === false) {
            // 0777 : permissions de lecture, écriture et exécution pour le propriétaire, le groupe et les autres. true : permet de créer les dossiers parents si nécessaire
            mkdir($directory, 0777, true);
        }

        // Si une ancienne image est associée à l'article, on la supprime
        if (!empty($article->getOldImage())) {
            // Liste des formats à supprimer
            $formats = ['small', 'large'];

           // Liste des extensions d'images prises en compte pour la suppression
            $extensions = ['jpg', 'jpeg', 'png', 'webp'];
            
            foreach($formats as $format) {
                foreach ($extensions as $ext) {

                    $oldFile = $directory . DIRECTORY_SEPARATOR . $article->getOldImage() . '_' . $format . '.' . $ext;
                    
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            }
        }

        $filename = uniqid("", true);

        $manager = new ImageManager(new Driver()); 

        $manager
            ->decode($image) 
            ->cover(350, 200) 
            ->save($directory . DIRECTORY_SEPARATOR . $filename . '_small.jpg');

        // Création de la version "large" (largeur 1280px, hauteur proportionnelle)
        $manager
            ->decode($image)
            ->scale(1280) 
            ->save($directory . DIRECTORY_SEPARATOR . $filename . '_large.jpg');

        // Mise à jour du nom de l’image dans l’article
        $article->setImage($filename);
    }

    /**
     * Méthode permettant de supprimer les images associées à un article.
     * Elle supprime les différentes versions (small et large) de l'image.
     *
     * @param Article $article Article dont les images doivent être supprimées
     */
    public static function detach (Article $article) 
    {
        // Vérifie qu'il y a bien une image à supprimer
        if (!empty($article->getImage())) {
            // Liste des formats d'images à supprimer
            $formats = ['small', 'large'];

            $extensions = ['jpg', 'jpeg', 'png', 'webp'];
            foreach($formats as $format) {
                foreach ($extensions as $ext) {
                    
                    $file = self::DIRECTORY . DIRECTORY_SEPARATOR . $article->getImage() . '_' . $format . '.' . $ext;
                    
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }
}
