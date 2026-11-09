<?php

namespace App\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Providers\Auth;
use App\Providers\View;
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
                "pageTitle" => "Accès indisponible — MédiCabinet",
                "message" => "Votre compte n’est associé à aucun patient.",
            ]);

            return;
        }

        try {
            $patientModel = new Patient();
            $patient = $patientModel->selectId($patientId);

            if ($patient === false) {
                http_response_code(404);

                View::render("error", [
                    "pageTitle" => "Patient introuvable — MédiCabinet",
                    "message" => "Votre fiche patient est introuvable.",
                ]);

                return;
            }

            $appointmentModel = new Appointment();
            $appointments = $appointmentModel->selectByPatient($patientId);
        } catch (PDOException $exception) {
            error_log(
                "Erreur SQL dans ClientController : " . $exception->getMessage()
            );

            http_response_code(500);

            View::render("error", [
                "pageTitle" => "Service indisponible — MédiCabinet",
                "message" => "Impossible de charger vos rendez-vous.",
            ]);

            return;
        }

        View::render("client/index", [
            "pageTitle" => "Mon espace — MédiCabinet",
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
}
