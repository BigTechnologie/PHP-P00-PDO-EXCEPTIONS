<?php
session_start();
session_destroy();

// Générer l'URL de la page d'accueil avec le routeur
header('Location: ' . $router->url('home'));
exit();

