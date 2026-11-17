<?php

namespace App\Controllers;

use App\Providers\View;
use App\Providers\Lang;

class LanguageController
{
    public function update(array $data = []): void
    {
        $token = $data["csrf_token"] ?? null;

        if (
            !is_string($token)
            || empty($_SESSION["csrf_token"])
            || !hash_equals($_SESSION["csrf_token"], $token)
        ) {
            http_response_code(403);

            View::render("error", [
                "pageTitle" => Lang::get("error.invalid_request") . " — MédiCabinet",
                "message" => Lang::get("error.invalid_data"),
            ]);

            return;
        }

        $locale = $data["lang"] ?? null;

        if (!in_array($locale, ["fr", "en"], true)) {
            http_response_code(400);

            View::render("error", [
                "pageTitle" => Lang::get("error.invalid_request") . " — MédiCabinet",
                "message" => Lang::get("language.invalid"),
            ]);

            return;
        }

        $_SESSION["lang"] = $locale;

        $destination = $this->getReturnPage($data["return_to"] ?? null);

        View::redirect($destination);
    }

    private function getReturnPage(mixed $url): string
    {
        if (
            !is_string($url)
            || preg_match('/[\x00-\x20\x7F\\\\]/', $url)
        ) {
            return "";
        }

        $parts = parse_url($url);

        if (
            $parts === false
            || isset($parts["scheme"])
            || isset($parts["host"])
        ) {
            return "";
        }

        // Seules les pages de l’interface sont autorisées.
        $allowedPages = [
            "",
            "home",
            "login",
            "patients",
            "patient/show",
            "appointments",
            "appointment/create",
            "appointment/edit",
            "appointment/delete",
            "user/create",
            "client",
            "logs",
            "profile",
        ];

        $path = $parts["path"] ?? "";

        foreach ($allowedPages as $page) {
            if ($path !== BASE . "/" . $page) {
                continue;
            }

            // Conserver les paramètres : id, page, filtres, etc.
            $parameters = [];
            parse_str($parts["query"] ?? "", $parameters);

            $query = http_build_query(
                $parameters,
                "",
                "&",
                PHP_QUERY_RFC3986
            );

            return $page . ($query !== "" ? "?" . $query : "");
        }

        return "";
    }
}
