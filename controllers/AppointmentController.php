<?php

namespace App\Controllers;

use App\Providers\Validator;
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
            "success" => $data["success"] ?? "",
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
            "canSubmit" => true,
        ]);
    }

    public function save(array $data, ?int $appointmentId = null): void
    {
        $csrfToken = $data["csrf_token"] ?? "";

        if (
            !is_string($csrfToken)
            || !hash_equals($_SESSION["csrf_token"], $csrfToken)
        ) {
            http_response_code(403);

            View::render("error", [
                "pageTitle" => "Demande refusée — MédiCabinet",
                "message" => "Le formulaire a expiré. Veuillez le rouvrir.",
            ]);

            return;
        }

        $statusLabels = [
            "planned" => "Planifié",
            "confirmed" => "Confirmé",
            "completed" => "Terminé",
            "cancelled" => "Annulé",
            "absent" => "Absent",
        ];

        $values = [];
        $errors = [];

        foreach (["patient_id", "doctor_id", "date", "time", "status", "reason"] as $field) {
            $value = $data[$field] ?? "";

            if (!is_string($value)) {
                $errors[$field] = "La valeur du champ est invalide.";
                $value = "";
            }

            $values[$field] = trim($value);
        }

        $isEdit = $appointmentId !== null;

        $heading = $isEdit ? "Modifier le rendez-vous" : "Planifier un rendez-vous";

        $formAction = $isEdit
            ? BASE . "/appointment/edit?id=" . $appointmentId
            : BASE . "/appointment/create";

        $submitLabel = $isEdit ? "Enregistrer les modifications" : "Enregistrer le rendez-vous";

        $validator = new Validator();

        $validator->field("patient_id",  $values["patient_id"],  "Patient")->required()->int();
        $validator->field("doctor_id", $values["doctor_id"], "Médecin")->required()->int();
        $validator->field("date", $values["date"], "La date")->required();
        $validator->field("time", $values["time"], "L’heure")->required();
        $validator->field("status", $values["status"], "Le statut")->required();
        $validator->field("reason", $values["reason"], "Le motif")->max(500);

        $errors = array_merge($errors, $validator->getErrors());

        if (!isset($errors["date"])) {
            $validDate = false;

            if (
                preg_match(
                    "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D",
                    $values["date"]
                ) === 1
            ) {
                [$year, $month, $day] = explode("-", $values["date"]);
                $validDate = checkdate((int) $month, (int) $day, (int) $year);
            }

            if (!$validDate) {
                $errors["date"] = "La date est invalide.";
            }
        }

        if (
            !isset($errors["time"])
            && preg_match(
                "/^([01][0-9]|2[0-3]):[0-5][0-9]$/D",
                $values["time"]
            ) !== 1
        ) {
            $errors["time"] = "L’heure est invalide.";
        }

        if (
            !isset($errors["status"])
            && !array_key_exists($values["status"], $statusLabels)
        ) {
            $errors["status"] = "Le statut est invalide.";
        }

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

        if (
            !isset($errors["patient_id"])
            && !in_array(
                (int) $values["patient_id"],
                array_column($patients, "id")
            )
        ) {
            $errors["patient_id"] = "Le patient est introuvable.";
        }

        if (
            !isset($errors["doctor_id"])
            && !in_array(
                (int) $values["doctor_id"],
                array_column($doctors, "id")
            )
        ) {
            $errors["doctor_id"] = "Le médecin est introuvable.";
        }

        $selectedServices = [];
        $submittedServices = $data["services"] ?? [];

        $validServiceIds = array_map("intval", array_column($services, "id"));

        if (!is_array($submittedServices)) {
            $errors["services"] = "La sélection des services est invalide.";
        } else {
            foreach ($submittedServices as $value) {
                $serviceId = is_string($value)
                    ? filter_var(
                        $value,
                        FILTER_VALIDATE_INT,
                        ["options" => ["min_range" => 1]]
                    )
                    : false;

                if (
                    $serviceId === false
                    || !in_array($serviceId, $validServiceIds, true)
                ) {
                    $errors["services"] = "Un des services sélectionnés est invalide.";
                    continue;
                }

                $selectedServices[] = $serviceId;
            }
        }

        $selectedServices = array_values(array_unique($selectedServices));

        if (empty($selectedServices)) {
            $errors["services"] = "Sélectionnez au moins un service valide.";
        }

        if (!empty($errors)) {
            http_response_code(422);

            View::render(
                $isEdit ? "appointment/edit" : "appointment/create",
                [
                    "pageTitle" => $heading . " — MédiCabinet",
                    "heading" => $heading,
                    "patients" => $patients,
                    "doctors" => $doctors,
                    "services" => $services,
                    "statusLabels" => $statusLabels,
                    "values" => $values,
                    "selectedServices" => $selectedServices,
                    "errors" => $errors,
                    "csrfToken" => $_SESSION["csrf_token"],
                    "formAction" => $formAction,
                    "submitLabel" => $submitLabel,
                    "canSubmit" => true,
                ]
            );

            return;
        }

        $record = [
            "patient_id" => (int) $values["patient_id"],
            "doctor_id" => (int) $values["doctor_id"],
            "starts_at" => $values["date"]
                . " "
                . $values["time"]
                . ":00",
            "status" => $values["status"],
            "reason" => $values["reason"] === "" ? null : $values["reason"],
        ];

        try {
            $appointmentModel = new Appointment();

            if ($appointmentId === null) {
                $appointmentModel->createWithServices(
                    $record,
                    $selectedServices
                );

                $success = "created";
            } else {
                $updated = $appointmentModel->updateWithServices(
                    $appointmentId,
                    $record,
                    $selectedServices
                );

                if (!$updated) {
                    http_response_code(404);

                    View::render("error", [
                        "pageTitle" => "Rendez-vous introuvable — MédiCabinet",
                        "message" => "Ce rendez-vous est introuvable.",
                    ]);

                    return;
                }

                $success = "updated";
            }
        } catch (PDOException $exception) {
            http_response_code(500);

            exit("Le rendez-vous n’a pas pu être enregistré.");
        }

        View::redirect("appointments?success=" . $success);
    }

    public function edit(array $data = []): void
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
                "message" => "L’identifiant du rendez-vous est invalide.",
            ]);

            return;
        }

        try {
            $appointmentModel = new Appointment();
            $appointment = $appointmentModel->selectId($id);

            if ($appointment === false) {
                http_response_code(404);

                View::render("error", [
                    "pageTitle" => "Rendez-vous introuvable — MédiCabinet",
                    "message" => "Ce rendez-vous est introuvable.",
                ]);

                return;
            }

            $patientModel = new Patient();
            $patients = $patientModel->select("last_name");

            $doctorModel = new Doctor();
            $doctors = $doctorModel->select("last_name");

            $serviceModel = new Service();
            $services = $serviceModel->select("name");

            $selectedServices = $appointmentModel->selectServiceIds($id);
        } catch (PDOException $exception) {
            http_response_code(500);

            exit("Le service est temporairement indisponible.");
        }

        View::render("appointment/edit", [
            "pageTitle" => "Modifier le rendez-vous — MédiCabinet",
            "heading" => "Modifier le rendez-vous",
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
                "patient_id" => $appointment["patient_id"],
                "doctor_id" => $appointment["doctor_id"],
                "date" => substr($appointment["starts_at"], 0, 10),
                "time" => substr($appointment["starts_at"], 11, 5),
                "status" => $appointment["status"],
                "reason" => $appointment["reason"],
            ],
            "selectedServices" => $selectedServices,
            "errors" => [],
            "csrfToken" => $_SESSION["csrf_token"],
            "formAction" => BASE . "/appointment/edit?id=" . $id,
            "submitLabel" => "Enregistrer les modifications",
            "canSubmit" => true,
        ]);
    }

    public function store(array $data, array $query = []): void
    {
        $this->save($data);
    }

    public function update(array $data, array $query = []): void
    {
        $id = filter_var(
            $query["id"] ?? null,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        if ($id === false) {
            http_response_code(400);

            View::render("error", [
                "pageTitle" => "Demande invalide — MédiCabinet",
                "message" => "L’identifiant du rendez-vous est invalide.",
            ]);

            return;
        }

        $this->save($data, $id);
    }
}