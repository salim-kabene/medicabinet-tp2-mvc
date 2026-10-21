<?php

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Providers\View;
use PDOException;

class PatientController
{
    public function index(): void
    {
        try {
            $model = new Patient();
            $patients = $model->select("last_name");
        } catch (PDOException $exception) {
            $this->databaseError($exception);
            return;
        }

        View::render("patient/index", [
            "pageTitle" => "Patients — MédiCabinet",
            "patients" => $patients,
            "patientCount" => count($patients),
        ]);
    }

    public function show(array $data = []): void
    {
        $id = filter_var(
            $data["id"] ?? null,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        if ($id === false) {
            http_response_code(400);

            View::render("error", [
                "pageTitle" => "Demande invalide — MédiCabinet",
                "message" => "L’identifiant du patient est invalide.",
            ]);

            return;
        }

        try {
            $model = new Patient();
            $patient = $model->selectId($id);
        } catch (PDOException $exception) {
            $this->databaseError($exception);
            return;
        }

        if ($patient === false) {
            http_response_code(404);

            View::render("error", [
                "pageTitle" => "Patient introuvable — MédiCabinet",
                "message" => "Ce patient est introuvable.",
            ]);

            return;
        }

        try {
            $appointmentModel = new Appointment();
            $appointments = $appointmentModel->selectByPatient($id);
        } catch (PDOException $exception) {
            $this->databaseError($exception);
            return;
        }

        View::render("patient/show", [
            "pageTitle" => $patient["first_name"]
                . " "
                . $patient["last_name"]
                . " — MédiCabinet",

            "patient" => $patient,

            "appointments" => $appointments,

            "statusLabels" => [
                "planned" => "Planifié",
                "confirmed" => "Confirmé",
                "completed" => "Terminé",
                "cancelled" => "Annulé",
                "absent" => "Absent",
            ],
        ]);
    }

    private function databaseError(PDOException $exception): void
    {
        // Informations techniques réservées au journal du serveur.
        error_log("Erreur SQL dans PatientController : " . $exception->getMessage());
        http_response_code(500);

        View::render("error", [
            "pageTitle" => "Service indisponible — MédiCabinet",
            "message" => "L’opération n’a pas pu être effectuée. "
                . "Veuillez réessayer plus tard.",
        ]);
    }
}