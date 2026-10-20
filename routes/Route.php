<?php

namespace App\Routes;

use App\Providers\View;

class Route
{
    private static array $routes = [];

    public static function get(string $url, string $controller): void
    {
        self::$routes[] = ["url" => $url, "controller" => $controller, "method" => "GET"];
    }

    public static function dispatch(): void
    {
        $url = $_SERVER["REQUEST_URI"];
        $urlSegments = explode("?", $url);
        $urlPath = $urlSegments[0];

        $method = $_SERVER["REQUEST_METHOD"];

        foreach (self::$routes as $route) {
            if (BASE . $route["url"] === $urlPath && $route["method"] === $method) {
                $controllerSegments = explode("@", $route["controller"]);

                $controllerName = "App\\Controllers\\" . $controllerSegments[0];
                $methodName = $controllerSegments[1];

                $controllerInstance = new $controllerName();

                $queryParams = [];

                if (isset($urlSegments[1])) {
                    parse_str($urlSegments[1], $queryParams);
                }

                if ($method === "POST") {
                    $controllerInstance->$methodName($_POST, $queryParams);
                } else {
                    if (isset($urlSegments[1])) {
                        $controllerInstance->$methodName($queryParams);
                    } else {
                        $controllerInstance->$methodName();
                    }
                }

                return;
            }
        }

        http_response_code(404);
        http_response_code(404);

        View::render("error", [
            "pageTitle" => "Page introuvable — MédiCabinet",
            "message" => "La page demandée n’existe pas ou son adresse a changé.",
        ]);
    }

    public static function post(string $url, string $controller): void
    {
        self::$routes[] = [
            "url" => $url,
            "controller" => $controller,
            "method" => "POST",
        ];
    }
}