<?php

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Providers\View;
use PDOException;

class AppointmentController
{
    public function index(array $data = []): void
    {
        $statusLabels = [
            "planned" => "Planifié",
            "confirmed" => "Confirmé",
            "completed" => "Terminé",
            "cancelled" => "Annulé",
            "absent" => "Absent",
        ];

        $date = $data["date"] ?? "";
        $status = $data["status"] ?? "";

        $error = null;

        if (!is_string($date) || !is_string($status)) {
            $error = "Les filtres transmis sont invalides.";
        } elseif ($status !== "" && !array_key_exists($status, $statusLabels)) {
            $error = "Le statut sélectionné est invalide.";
        } elseif ($date !== "") {
            $validDate = false;

            if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D", $date) === 1) {
                [$year, $month, $day] = explode("-", $date);

                $validDate = checkdate((int) $month, (int) $day, (int) $year);
            }

            if (!$validDate) {
                $error = "La date sélectionnée est invalide.";
            }
        }

        if ($error !== null) {
            http_response_code(400);

            View::render("error", [
                "pageTitle" => "Filtres invalides — MédiCabinet",
                "message" => $error,
            ]);

            return;
        }

        try {
            $appointmentModel = new Appointment();
            $appointments = $appointmentModel->selectWithDetails($date, $status);

            $todayCount = $appointmentModel->countToday();
            $upcomingCount = $appointmentModel->countUpcoming();

            $patientModel = new Patient();
            $patientCount = $patientModel->countAll();
        } catch (PDOException $exception) {
            http_response_code(500);
            exit("Le service est temporairement indisponible.");
        }

        View::render("appointment/index", [
            "pageTitle" => "Rendez-vous — MédiCabinet",
            "appointments" => $appointments,
            "todayCount" => $todayCount,
            "upcomingCount" => $upcomingCount,
            "patientCount" => $patientCount,
            "statusLabels" => $statusLabels,
            "date" => $date,
            "status" => $status,
        ]);
    }
}