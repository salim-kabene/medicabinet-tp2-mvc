<?php

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Providers\View;
use App\Providers\Lang;
use PDOException;
use App\Providers\Auth;

class PatientController
{
    public function __construct()
    {
        Auth::privilege(1);
    }
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
            "pageTitle" => Lang::get("nav.patients") . " — MédiCabinet",
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
                "pageTitle" => Lang::get("error.invalid_request") . " — MédiCabinet",
                "message" => Lang::get("patient.invalid_id"),
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
                "pageTitle" => Lang::get("error.patient_not_found") . " — MédiCabinet",
                "message" => Lang::get("patient.not_found"),
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
                "planned" => Lang::get("status.planned"),
                "confirmed" => Lang::get("status.confirmed"),
                "completed" => Lang::get("status.completed"),
                "cancelled" => Lang::get("status.cancelled"),
                "absent" => Lang::get("status.absent"),
            ],
        ]);
    }

    private function databaseError(PDOException $exception): void
    {
        // Informations techniques réservées au journal du serveur.
        error_log("Erreur SQL dans PatientController : " . $exception->getMessage());
        http_response_code(500);

        View::render("error", [
            "pageTitle" => Lang::get("error.service_unavailable") . " — MédiCabinet",
            "message" => Lang::get("error.operation_failed"),
        ]);
    }
}
