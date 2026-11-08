<?php

namespace App\Controllers;

use App\Models\Privilege;
use App\Models\Patient;
use App\Providers\View;
use PDOException;

class UserController
{
    public function create(): void
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
                "pageTitle" => "Service indisponible — MédiCabinet",
                "message" => "Impossible de charger le formulaire.",
            ]);

            return;
        }

        View::render("user/create", [
            "pageTitle" => "Créer un utilisateur — MédiCabinet",
            "privileges" => $privileges,
            "patients" => $patients,
            "values" => [],
            "errors" => [],
            "csrfToken" => $_SESSION["csrf_token"],
        ]);
    }
}
