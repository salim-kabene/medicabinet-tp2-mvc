<?php

namespace App\Controllers;

use App\Models\Privilege;
use App\Models\Patient;
use App\Providers\View;
use App\Providers\Lang;
use PDOException;
use App\Models\Users;
use App\Providers\Validator;
use App\Providers\Auth;

class UserController
{
    public function __construct()
    {
        Auth::privilege(1);
    }
    public function create(): void
    {
        $this->renderForm();
    }

    private function renderForm(array $values = [], array $errors = []): void
    {
        try {
            $privilegeModel = new Privilege();
            $privileges = $privilegeModel->select("privilege");

            $patientModel = new Patient();
            $patients = $patientModel->select("last_name");
        } catch (PDOException $exception) {
            error_log("Erreur SQL dans UserController : " . $exception->getMessage());

            http_response_code(500);

            View::render("error", [
                "pageTitle" => Lang::get("error.service_unavailable") . " — MédiCabinet",
                "message" => Lang::get("user.form_failed"),
            ]);

            return;
        }
        $success = $_SESSION["user_success"] ?? "";
        unset($_SESSION["user_success"]);

        View::render("user/create", [
            "pageTitle" => Lang::get("user.title") . " — MédiCabinet",
            "privileges" => $privileges,
            "patients" => $patients,
            "values" => $values,
            "errors" => $errors,
            "success" => $success,
            "csrfToken" => $_SESSION["csrf_token"],
        ]);
    }

    public function store(array $data): void
    {
        // Vérifier que la soumission provient de notre formulaire.
        $token = $data["csrf_token"] ?? "";

        if (!is_string($token) || !hash_equals($_SESSION["csrf_token"], $token)) {
            http_response_code(403);

            View::render("error", [
                "pageTitle" => Lang::get("error.request_denied") . " — MédiCabinet",
                "message" => Lang::get("error.form_expired_reopen"),
            ]);

            return;
        }

        $values = [];
        $errors = [];

        // Préparer les champs et refuser les valeurs de type tableau.
        foreach (["name", "username", "email", "privilege_id", "patient_id"] as $field) {
            $value = $data[$field] ?? "";

            if (!is_string($value)) {
                $errors[$field] = Lang::get("error.invalid_field");
                $value = "";
            }

            $values[$field] = trim($value);
        }

        // Ne pas supprimer les espaces du mot de passe.
        $password = $data["password"] ?? "";

        if (!is_string($password)) {
            $errors["password"] = Lang::get("user.invalid_password");
            $password = "";
        }

        $validator = new Validator();
        $validator->field("name", $values["name"], Lang::get("field.name"))
            ->required()->min(2)->max(50);

        $validator->field("username", $values["username"], Lang::get("field.username"))
            ->required()->email()->max(50);

        $validator->field("email", $values["email"], Lang::get("field.email"))
            ->required()->email()->max(100);

        $validator->field("password", $password, Lang::get("field.password"))
            ->required()->min(8);

        $validator->field("privilege_id", $values["privilege_id"], Lang::get("field.privilege"))
            ->required()->int(1);

        // Bcrypt prend en compte au maximum 72 octets.
        if (strlen($password) > 72) {
            $errors["password"] = Lang::get("user.password_bytes");
        }

        $errors = array_merge($validator->getErrors(), $errors);

        if (!in_array($values["privilege_id"], ["1", "2"], true)) {
            $errors["privilege_id"] = Lang::get("user.invalid_privilege");
        }

        if ($values["privilege_id"] === "2") {
            $validator->field("patient_id", $values["patient_id"], Lang::get("field.patient"))
                ->required()->int(1);

            $errors = array_merge($validator->getErrors(), $errors);
        } elseif ($values["privilege_id"] === "1" && $values["patient_id"] !== "") {
            $errors["patient_id"] = Lang::get("user.admin_patient");
        }

        if (!empty($errors)) {
            http_response_code(422);
            $this->renderForm($values, $errors);
            return;
        }

        try {
            $userModel = new Users();

            // Vérifier les champs uniques à l’aide du validateur.
            $validator->field("username", $values["username"], Lang::get("field.username"))
                ->unique("Users");

            $validator->field("email", $values["email"], Lang::get("field.email"))
                ->unique("Users");

            $errors = $validator->getErrors();

            // Vérifier que le rôle existe réellement dans la base.
            $privilegeModel = new Privilege();

            if ($privilegeModel->selectId($values["privilege_id"]) === false) {
                $errors["privilege_id"] = Lang::get("user.privilege_not_found");
            }

            if ($values["privilege_id"] === "2") {
                $patientModel = new Patient();

                if ($patientModel->selectId($values["patient_id"]) === false) {
                    $errors["patient_id"] = Lang::get("patient.not_found");
                } elseif (
                    $userModel->unique("patient_id", $values["patient_id"]) !== false
                ) {
                    $errors["patient_id"] = Lang::get("user.patient_used");
                }
            }

            if (!empty($errors)) {
                http_response_code(422);
                $this->renderForm($values, $errors);
                return;
            }

            // Construire explicitement les données à enregistrer.
            $record = [
                "name" => $values["name"],
                "username" => $values["username"],
                "email" => $values["email"],
                "password" => $userModel->hashPassword($password),
                "privilege_id" => (int) $values["privilege_id"],
                "patient_id" => $values["privilege_id"] === "2" ? (int) $values["patient_id"] : null,
            ];

            $userModel->insert($record);
        } catch (PDOException $exception) {
            error_log(
                "Erreur SQL lors de la création d’un utilisateur : " . $exception->getMessage()
            );

            http_response_code(500);

            View::render("error", [
                "pageTitle" => Lang::get("error.service_unavailable") . " — MédiCabinet",
                "message" => Lang::get("user.failed"),
            ]);

            return;
        }

        $_SESSION["user_success"] = Lang::get("user.created");

        View::redirect("user/create");
    }
}
