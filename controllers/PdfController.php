<?php

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Providers\Auth;
use App\Providers\Pdf;
use App\Providers\View;
use App\Providers\Lang;

class PdfController
{
    public function __construct()
    {
        Auth::session();
    }

    public function appointments(array $data = []): void
    {
        $privilegeId = (int) ($_SESSION["privilege_id"] ?? 0);

        if ($privilegeId === 1) {
            $patientId = filter_var(
                $data["id"] ?? null,
                FILTER_VALIDATE_INT,
                ["options" => ["min_range" => 1]]
            );

            if ($patientId === false) {
                $this->error(400, Lang::get("patient.invalid_id"));
                return;
            }
        } elseif ($privilegeId === 2) {
            // Pour le client, seul le patient de la session est utilisé.
            $patientId = $_SESSION["patient_id"] ?? null;

            if (!is_int($patientId) || $patientId < 1) {
                $this->error(
                    403,
                    Lang::get("client.no_patient")
                );
                return;
            }
        } else {
            $this->error(403, Lang::get("pdf.forbidden"));
            return;
        }

        try {
            $patientModel = new Patient();
            $patient = $patientModel->selectId($patientId);

            if ($patient === false) {
                $this->error(404, Lang::get("patient.not_found"));
                return;
            }

            $appointmentModel = new Appointment();
            $appointments = $appointmentModel->selectByPatient($patientId);

            $html = View::renderToString("pdf/appointments", [
                "patient" => $patient,
                "appointments" => $appointments,
                "generatedAt" => date("d/m/Y") . " " . Lang::get("common.at") . " " . date("H:i"),
                "statusLabels" => [
                    "planned" => Lang::get("status.planned"),
                    "confirmed" => Lang::get("status.confirmed"),
                    "completed" => Lang::get("status.completed"),
                    "cancelled" => Lang::get("status.cancelled"),
                    "absent" => Lang::get("status.absent"),
                ],
            ]);

            Pdf::download($html, (Lang::locale() === "en" ? "patient-appointments-" : "rendez-vous-patient-") . $patientId . ".pdf");
        } catch (\Exception $exception) {
            error_log("Erreur de génération du PDF : " . $exception->getMessage());

            $this->error(
                500,
                Lang::get("pdf.failed")
            );
        }
    }

    private function error(int $status, string $message): void
    {
        http_response_code($status);

        View::render("error", [
            "pageTitle" => Lang::get("pdf.unavailable") . " — MédiCabinet",
            "message" => $message,
        ]);
    }
}
