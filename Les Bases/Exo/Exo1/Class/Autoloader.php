<?php 

class Autoloader {
    static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }
    static function autoload(string $class): void 
    {
        require 'Class/' . $class . '.php'; // 'Class/index.php'; Class/Voiture.php
    }
}

Autoloader::register();