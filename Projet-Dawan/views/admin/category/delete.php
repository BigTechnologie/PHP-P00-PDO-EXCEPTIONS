<?php

/** @var array $params */

use App\Attachment\ArticleAttachment;
use App\Auth;
use App\Connection;
use App\Controller\CategoryController;


Auth::check();

$pdo = Connection::getPDO();

$table = new CategoryController($pdo);

$table->delete($params['id']);

header('Location: ' . $router->url('admin_categories') . '?delete=1');