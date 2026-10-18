<?php

namespace App\Controllers;

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
            http_response_code(500);
            exit("Le service est temporairement indisponible.");
        }

        View::render("patient/index", [
            "pageTitle" => "Patients — MédiCabinet",
            "patients" => $patients,
            "patientCount" => count($patients),
        ]);
    }
}