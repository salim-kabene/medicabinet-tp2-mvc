<?php

namespace App\Providers;

use App\Models\Log;

class Logger
{
    public static function record(): void
    {
        $connected = Auth::check();

        $page = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH);

        if (!is_string($page) || $page === "") {
            $page = "/";
        }

        try {
            $log = new Log();

            $log->insert([
                "ip_address" => $_SERVER["REMOTE_ADDR"] ?? "",
                "username" => $connected ? $_SESSION["username"] : "Visiteur",
                "user_id" => $connected ? (int) $_SESSION["user_id"] : null,
                "page" => substr($page, 0, 2048),
                "http_method" => substr(
                    $_SERVER["REQUEST_METHOD"] ?? "GET",
                    0,
                    10
                )
            ]);
        } catch (\PDOException $e) {
            error_log("Impossible d'enregistrer la visite dans le journal.");
        }
    }
}
