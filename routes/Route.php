<?php

namespace App\Routes;

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

                if (isset($urlSegments[1])) {
                    parse_str($urlSegments[1], $queryParams);
                    $controllerInstance->$methodName($queryParams);
                } else {
                    $controllerInstance->$methodName();
                }

                return;
            }
        }

        http_response_code(404);
        echo "404 - Page introuvable";
    }
}