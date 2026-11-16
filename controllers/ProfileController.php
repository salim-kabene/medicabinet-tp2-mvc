<?php

namespace App\Controllers;

use App\Models\Users;
use App\Providers\Auth;
use App\Providers\ImageUpload;
use App\Providers\View;
use App\Providers\Lang;

class ProfileController
{
    public function __construct()
    {
        Auth::session();
    }

    public function index(): void
    {
        $this->renderForm();
    }

    private function renderForm(array $errors = []): void
    {
        $success = $_SESSION["profile_success"] ?? null;
        unset($_SESSION["profile_success"]);

        View::render("profile/index", [
            "pageTitle" => Lang::get("account.profile") . " — MédiCabinet",
            "errors" => $errors,
            "success" => $success,
        ]);
    }

    public function updatePhoto(array $data = []): void
    {
        $token = $data["csrf_token"] ?? null;

        if (
            !is_string($token)
            || empty($_SESSION["csrf_token"])
            || !hash_equals($_SESSION["csrf_token"], $token)
        ) {
            http_response_code(403);
            $this->renderForm([
                Lang::get("profile.invalid_request")
            ]);
            return;
        }

        $file = $_FILES["photo"] ?? null;

        if (!is_array($file)) {
            http_response_code(422);
            $this->renderForm([
                Lang::get("profile.select_image")
            ]);
            return;
        }

        $filename = null;

        try {
            $userId = (int) $_SESSION["user_id"];
            $model = new Users();
            $user = $model->selectId($userId);

            if ($user === false) {
                http_response_code(404);
                $this->renderForm([
                    Lang::get("profile.account_not_found")
                ]);
                return;
            }

            $filename = ImageUpload::saveAvatar($file);

            if (!$model->updatePhoto($userId, $filename)) {
                throw new \RuntimeException(
                    "Impossible de mettre à jour la photo du compte."
                );
            }
        } catch (\InvalidArgumentException $exception) {
            http_response_code(422);
            $this->renderForm([$exception->getMessage()]);
            return;
        } catch (\Exception $exception) {
            // Si la base n’a pas pu être mise à jour,
            // on retire le nouveau fichier.
            $this->deleteStoredPhoto($filename);

            error_log("Erreur de photo de profil : " . $exception->getMessage());

            http_response_code(500);
            $this->renderForm([
                Lang::get("profile.save_failed")
            ]);
            return;
        }

        $_SESSION["user_photo"] = $filename;

        // L’ancienne photo est supprimée seulement après la mise à jour.
        $this->deleteStoredPhoto($user["photo"] ?? null);

        $_SESSION["profile_success"] = Lang::get("profile.updated");
        View::redirect("profile");
    }

    public function photo(): void
    {
        try {
            $model = new Users();
            $user = $model->selectId((int) $_SESSION["user_id"]);
        } catch (\PDOException $exception) {
            error_log("Impossible de charger la photo du compte.");
            http_response_code(500);
            return;
        }

        $filename = $user === false ? null : ($user["photo"] ?? null);

        if (!$this->isValidFilename($filename)) {
            http_response_code(404);
            return;
        }

        $path = __DIR__ . "/../storage/avatars/" . $filename;

        if (!is_file($path) || !is_readable($path)) {
            http_response_code(404);
            return;
        }

        $mimeType = pathinfo($filename, PATHINFO_EXTENSION) === "png"
            ? "image/png"
            : "image/jpeg";

        header("Content-Type: " . $mimeType);
        header("X-Content-Type-Options: nosniff");
        header("Cache-Control: private, no-store");

        readfile($path);
        exit;
    }

    private function isValidFilename(?string $filename): bool
    {
        return $filename !== null
            && preg_match('/\A[a-f0-9]{32}\.(jpg|png)\z/', $filename) === 1;
    }

    private function deleteStoredPhoto(?string $filename): void
    {
        if (!$this->isValidFilename($filename)) {
            return;
        }

        $path = __DIR__ . "/../storage/avatars/" . $filename;

        if (is_file($path) && !@unlink($path)) {
            error_log("Impossible de supprimer une ancienne photo de profil.");
        }
    }

    public function deletePhoto(array $data = []): void
    {
        $token = $data["csrf_token"] ?? null;

        if (
            !is_string($token)
            || empty($_SESSION["csrf_token"])
            || !hash_equals($_SESSION["csrf_token"], $token)
        ) {
            http_response_code(403);
            $this->renderForm([
                Lang::get("profile.invalid_request")
            ]);
            return;
        }

        try {
            $userId = (int) $_SESSION["user_id"];
            $model = new Users();
            $user = $model->selectId($userId);

            if ($user === false) {
                http_response_code(404);
                $this->renderForm([
                    Lang::get("profile.account_not_found")
                ]);
                return;
            }

            if (!$model->updatePhoto($userId, null)) {
                throw new \RuntimeException(
                    "Impossible de retirer la photo du compte."
                );
            }
        } catch (\Exception $exception) {
            error_log(
                "Erreur de suppression de photo : " . $exception->getMessage()
            );

            http_response_code(500);
            $this->renderForm([
                Lang::get("profile.delete_failed")
            ]);
            return;
        }

        $_SESSION["user_photo"] = null;

        $this->deleteStoredPhoto($user["photo"] ?? null);

        $_SESSION["profile_success"] = Lang::get("profile.deleted");
        View::redirect("profile");
    }
}
