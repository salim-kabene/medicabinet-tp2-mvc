<?php

namespace App\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Providers\Auth;
use App\Providers\View;
use App\Providers\Lang;
use PDOException;

class ClientController
{
    public function __construct()
    {
        Auth::privilege(2);
    }

    public function index(): void
    {
        // Le patient vient de la session, jamais de l’URL.
        $patientId = $_SESSION["patient_id"] ?? null;

        if (!is_int($patientId) || $patientId < 1) {
            http_response_code(403);

            View::render("error", [
                "pageTitle" => Lang::get("error.access_unavailable")
                    . " — MédiCabinet",
                "message" => Lang::get("client.no_patient"),
            ]);

            return;
        }

        try {
            $patientModel = new Patient();
            $patient = $patientModel->selectId($patientId);

            if ($patient === false) {
                http_response_code(404);

                View::render("error", [
                    "pageTitle" => Lang::get("error.patient_not_found")
                        . " — MédiCabinet",
                    "message" => Lang::get("client.patient_not_found"),
                ]);

                return;
            }

            $appointmentModel = new Appointment();
            $appointments = $appointmentModel->selectByPatient($patientId);
        } catch (PDOException $exception) {
            error_log(
                "Erreur SQL dans ClientController : "
                    . $exception->getMessage()
            );

            http_response_code(500);

            View::render("error", [
                "pageTitle" => Lang::get("error.service_unavailable")
                    . " — MédiCabinet",
                "message" => Lang::get("client.appointments_unavailable"),
            ]);

            return;
        }

        View::render("client/index", [
            "pageTitle" => Lang::get("nav.client") . " — MédiCabinet",
            "patient" => $patient,
            "appointments" => $appointments,
        ]);
    }
}
