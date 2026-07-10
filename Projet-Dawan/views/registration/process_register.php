<?php

use App\Connection;
use App\Model\User;
use App\Model\UserManager;

$pdo = Connection::getPDO();

$userManager = new UserManager($pdo);

if(isset($_POST['username'], $_POST['password'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(empty($username) || empty($password)) {
        die('Tous les champs sont obligatoires.');
    }

    if($userManager->userExists($username)) {
        die('Ce nom d\'utilisateur est déjà pris.');
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $user = new User();

    $user->setUsername($username)
        ->setPassword($hashedPassword);

    if($userManager->addUser($user)) {
        echo 'Inscription réussie !';
    } else {
        echo 'Erreur lors de l\'inscription.';
    }

} else {
    echo 'Formulaire incomplet';
}