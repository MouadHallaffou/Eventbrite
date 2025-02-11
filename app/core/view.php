<?php
namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View {
    private static $twig;

    public static function render($template, $data = []) {
        if (!self::$twig) {
            $loader = new FilesystemLoader(__DIR__ . '/../views/');
            self::$twig = new Environment($loader);
        }
        echo self::$twig->render($template, $data);
    }
}
