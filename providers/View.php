<?php

namespace App\Providers;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View
{
    public static function render(string $template, array $data = []): void
    {
        $loader = new FilesystemLoader(__DIR__ . "/../views");
        $twig = new Environment($loader);

        $twig->addGlobal("asset", ASSET);
        $twig->addGlobal("base", BASE);

        echo $twig->render($template . ".php", $data);
    }

    public static function redirect(string $url): void
    {
        header("Location: " . BASE . "/" . $url, true, 303);
        exit;
    }
}