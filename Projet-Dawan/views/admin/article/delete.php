<?php

/** @var array $params */

use App\Attachment\ArticleAttachment;
use App\Auth;
use App\Connection;
use App\Controller\ArticleController;

Auth::check();

$pdo = Connection::getPDO();

$table = new ArticleController($pdo);
$article = $table->find($params['id']);

ArticleAttachment::detach($article);

$table->delete($params['id']);

header('Location: ' . $router->url('admin_articles') . '?delete=1');