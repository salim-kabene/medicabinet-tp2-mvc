<?php

namespace App\Controllers;

use App\Models\Users;
use App\Providers\Validator;
use App\Providers\View;
use PDOException;
use App\Providers\Lang;

class AuthController
{
    public function index(): void
    {
        $this->renderForm();
    }

    private function renderForm(
        array $values = [],
        array $errors = []
    ): void {
        View::render("auth/index", [
            "pageTitle" => Lang::get("auth.title") . " — MédiCabinet",
            "values" => $values,
            "errors" => $errors,
            "csrfToken" => $_SESSION["csrf_token"],
        ]);
    }

    public function store(array $data): void
    {
        $token = $data["csrf_token"] ?? "";

        if (
            !is_string($token)
            || !hash_equals($_SESSION["csrf_token"], $token)
        ) {
            http_response_code(403);

            View::render("error", [
                "pageTitle" => Lang::get("error.request_denied") . " — MédiCabinet",
                "message" => Lang::get("error.form_expired_reopen"),
            ]);

            return;
        }

        $username = $data["username"] ?? "";
        $password = $data["password"] ?? "";

        if (!is_string($username) || !is_string($password)) {
            http_response_code(400);

            $this->renderForm([], ["message" => Lang::get("error.invalid_data")]);

            return;
        }

        $username = trim($username);

        $validator = new Validator();

        $validator->field("username", $username, Lang::get("field.username"))
            ->required()->email()->max(50);

        $validator->field("password", $password, Lang::get("field.password"))
            ->required();

        // Conserver uniquement l’identifiant pour réafficher le formulaire.
        $values = ["username" => $username];

        if (!$validator->isSuccess()) {
            http_response_code(422);
            $this->renderForm($values, $validator->getErrors());
            return;
        }

        try {
            $user = new Users();
            $connected = $user->checkUser($username, $password);
        } catch (PDOException $exception) {
            error_log(
                "Erreur SQL dans AuthController : " . $exception->getMessage()
            );

            http_response_code(500);

            View::render("error", [
                "pageTitle" => Lang::get("error.service_unavailable") . " — MédiCabinet",
                "message" => Lang::get("auth.unavailable"),
            ]);

            return;
        }

        if (!$connected) {
            http_response_code(401);

            $this->renderForm($values, [
                "message" => Lang::get("auth.invalid_credentials"),
            ]);

            return;
        }

        if ($_SESSION["privilege_id"] === 1) {
            View::redirect("appointments");
        }

        if ($_SESSION["privilege_id"] === 2) {
            View::redirect("client");
        }

        View::redirect("home");
    }

    public function delete(array $data): void
    {
        $token = $data["csrf_token"] ?? "";

        if (
            !is_string($token)
            || !hash_equals($_SESSION["csrf_token"], $token)
        ) {
            http_response_code(403);

            View::render("error", [
                "pageTitle" => Lang::get("error.request_denied") . " — MédiCabinet",
                "message" => Lang::get("error.form_expired_retry"),
            ]);

            return;
        }

        // Vider les informations de l’utilisateur connecté.
        $_SESSION = [];

        // Supprimer le cookie de session du navigateur.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(session_name(), "", [
                "expires" => time() - 3600,
                "path" => $params["path"],
                "domain" => $params["domain"],
                "secure" => $params["secure"],
                "httponly" => $params["httponly"],
                "samesite" => $params["samesite"],
            ]);
        }

        session_destroy();

        View::redirect("login");
    }
}
