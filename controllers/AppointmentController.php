<?php

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Providers\View;
use PDOException;

class AppointmentController
{
    public function index(): void
    {
        try {
            $appointmentModel = new Appointment();

            $appointments = $appointmentModel->selectWithDetails();
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