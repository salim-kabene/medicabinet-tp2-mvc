<?php

namespace App\Providers;

class Auth
{
    public static function check(): bool
    {
        if (!isset($_SESSION["user_id"], $_SESSION["privilege_id"], $_SESSION["fingerPrint"])) {
            return false;
        }

        $fingerPrint = md5(
            ($_SERVER["HTTP_USER_AGENT"] ?? "") . ($_SERVER["REMOTE_ADDR"] ?? "")
        );

        return $_SESSION["fingerPrint"] === $fingerPrint;
    }

    public static function session(): bool
    {
        if (self::check()) {
            return true;
        }

        View::redirect("login");
        exit;
    }

    public static function privilege(int $id): bool
    {
        self::session();

        if ((int) $_SESSION["privilege_id"] === $id) {
            return true;
        }

        http_response_code(403);

        View::render("error", [
            "pageTitle" => "Accès interdit — MédiCabinet",
            "message" => "Vous n’avez pas accès à cette page.",
        ]);

        exit;
    }
}
