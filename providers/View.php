<?php

namespace App\Providers;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View
{
    public static function render(string $template, array $data = []): void
    {
        echo self::renderToString($template, $data);
    }

    public static function renderToString(
        string $template,
        array $data = []
    ): string {
        $loader = new FilesystemLoader(__DIR__ . "/../views");

        $twig = new Environment($loader, [
            "autoescape" => "html",
        ]);

        $twig->addGlobal("asset", ASSET);
        $twig->addGlobal("base", BASE);
        $twig->addGlobal("httpStatus", http_response_code());
        $twig->addGlobal("session", $_SESSION);
        $twig->addGlobal("guest", !Auth::check());
        $twig->addGlobal("locale", Lang::locale());
        $twig->addGlobal("trans", Lang::translations());

        $twig->addGlobal(
            "currentUrl",
            $_SERVER["REQUEST_URI"] ?? BASE . "/"
        );

        return $twig->render($template . ".php", $data);
    }

    public static function redirect(string $url): void
    {
        header("Location: " . BASE . "/" . $url, true, 303);
        exit;
    }
}
