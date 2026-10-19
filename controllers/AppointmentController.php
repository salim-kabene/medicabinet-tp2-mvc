<?php

namespace App\Controllers;

use App\Models\Doctor;
use App\Models\Service;
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

    public function create(array $data = []): void
    {
        try {
            $patientModel = new Patient();
            $patients = $patientModel->select("last_name");

            $doctorModel = new Doctor();
            $doctors = $doctorModel->select("last_name");

            $serviceModel = new Service();
            $services = $serviceModel->select("name");
        } catch (PDOException $exception) {
            http_response_code(500);
            exit("Le service est temporairement indisponible.");
        }

        $preselectedPatient = "";

        if (array_key_exists("patient_id", $data)) {
            $requestedPatient = filter_var(
                $data["patient_id"],
                FILTER_VALIDATE_INT,
                ["options" => ["min_range" => 1]]
            );

            if (
                $requestedPatient === false
                ||
                !in_array($requestedPatient, array_column($patients, "id"))
            ) {
                http_response_code(400);

                View::render("error", [
                    "pageTitle" => "Patient invalide — MédiCabinet",
                    "message" => "Le patient demandé est invalide.",
                ]);

                return;
            }

            $preselectedPatient = (string) $requestedPatient;
        }

        View::render("appointment/create", [
            "pageTitle" => "Planifier un rendez-vous — MédiCabinet",
            "heading" => "Planifier un rendez-vous",
            "patients" => $patients,
            "doctors" => $doctors,
            "services" => $services,
            "statusLabels" => [
                "planned" => "Planifié",
                "confirmed" => "Confirmé",
                "completed" => "Terminé",
                "cancelled" => "Annulé",
                "absent" => "Absent",
            ],
            "values" => [
                "patient_id" => $preselectedPatient,
                "doctor_id" => "",
                "date" => "",
                "time" => "",
                "status" => "planned",
                "reason" => "",
            ],
            "selectedServices" => [],
            "errors" => [],
            "csrfToken" => $_SESSION["csrf_token"],
            "formAction" => BASE . "/appointment/create",
            "submitLabel" => "Enregistrer le rendez-vous",
            "canSubmit" => false,
        ]);
    }
}