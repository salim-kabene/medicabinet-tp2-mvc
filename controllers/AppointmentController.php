<?php

namespace App\Controllers;

use App\Providers\Validator;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Patient;
use App\Providers\View;
use App\Providers\Lang;
use App\Providers\Auth;
use PDOException;

class AppointmentController
{
    public function __construct()
    {
        Auth::privilege(1);
    }
    public function index(array $data = []): void
    {
        $statusLabels = [
            "planned" => Lang::get("status.planned"),
            "confirmed" => Lang::get("status.confirmed"),
            "completed" => Lang::get("status.completed"),
            "cancelled" => Lang::get("status.cancelled"),
            "absent" => Lang::get("status.absent"),
        ];

        $date = $data["date"] ?? "";
        $status = $data["status"] ?? "";

        $error = null;

        if (!is_string($date) || !is_string($status)) {
            $error = Lang::get("appointment.invalid_filters");
        } elseif ($status !== "" && !array_key_exists($status, $statusLabels)) {
            $error = Lang::get("appointment.invalid_selected_status");
        } elseif ($date !== "") {
            $validDate = false;

            if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D", $date) === 1) {
                [$year, $month, $day] = explode("-", $date);

                $validDate = checkdate((int) $month, (int) $day, (int) $year);
            }

            if (!$validDate) {
                $error = Lang::get("appointment.invalid_selected_date");
            }
        }

        if ($error !== null) {
            http_response_code(400);

            View::render("error", [
                "pageTitle" => Lang::get("appointment.invalid_filters_title") . " — MédiCabinet",
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
            $this->databaseError($exception);
            return;
        }

        View::render("appointment/index", [
            "pageTitle" => Lang::get("nav.appointments") . " — MédiCabinet",
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
            $this->databaseError($exception);
            return;
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
                    "pageTitle" => Lang::get("appointment.invalid_patient_title") . " — MédiCabinet",
                    "message" => Lang::get("appointment.invalid_patient"),
                ]);

                return;
            }

            $preselectedPatient = (string) $requestedPatient;
        }

        View::render("appointment/create", [
            "pageTitle" => Lang::get("appointment.create") . " — MédiCabinet",
            "heading" => Lang::get("appointment.create"),
            "patients" => $patients,
            "doctors" => $doctors,
            "services" => $services,
            "statusLabels" => [
                "planned" => Lang::get("status.planned"),
                "confirmed" => Lang::get("status.confirmed"),
                "completed" => Lang::get("status.completed"),
                "cancelled" => Lang::get("status.cancelled"),
                "absent" => Lang::get("status.absent"),
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
            "submitLabel" => Lang::get("appointment.save"),
            "canSubmit" => true,
        ]);
    }

    private function save(array $data, ?int $appointmentId = null): void
    {
        $csrfToken = $data["csrf_token"] ?? "";

        if (
            !is_string($csrfToken)
            || !hash_equals($_SESSION["csrf_token"], $csrfToken)
        ) {
            http_response_code(403);

            View::render("error", [
                "pageTitle" => Lang::get("error.request_denied") . " — MédiCabinet",
                "message" => Lang::get("error.form_expired_reopen"),
            ]);

            return;
        }

        $statusLabels = [
            "planned" => Lang::get("status.planned"),
            "confirmed" => Lang::get("status.confirmed"),
            "completed" => Lang::get("status.completed"),
            "cancelled" => Lang::get("status.cancelled"),
            "absent" => Lang::get("status.absent"),
        ];

        $values = [];
        $errors = [];

        foreach (["patient_id", "doctor_id", "date", "time", "status", "reason"] as $field) {
            $value = $data[$field] ?? "";

            if (!is_string($value)) {
                $errors[$field] = Lang::get("error.invalid_field");
                $value = "";
            }

            $values[$field] = trim($value);
        }

        $isEdit = $appointmentId !== null;

        $heading = $isEdit ? Lang::get("appointment.edit") : Lang::get("appointment.create");

        $formAction = $isEdit
            ? BASE . "/appointment/edit?id=" . $appointmentId
            : BASE . "/appointment/create";

        $submitLabel = $isEdit ? Lang::get("common.save_changes") : Lang::get("appointment.save");

        $validator = new Validator();

        $validator->field("patient_id",  $values["patient_id"],  Lang::get("field.patient"))->required()->int(1);
        $validator->field("doctor_id", $values["doctor_id"], Lang::get("field.doctor"))->required()->int(1);
        $validator->field("date", $values["date"], Lang::get("field.date"))->required();
        $validator->field("time", $values["time"], Lang::get("field.time"))->required();
        $validator->field("status", $values["status"], Lang::get("field.status"))->required();
        $validator->field("reason", $values["reason"], Lang::get("field.reason"))->max(500);

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
                $validDate = (int) $year >= 1000
                    && checkdate((int) $month, (int) $day, (int) $year);
            }

            if (!$validDate) {
                $errors["date"] = Lang::get("appointment.invalid_date");
            }
        }

        if (
            !isset($errors["time"])
            && preg_match(
                "/^([01][0-9]|2[0-3]):[0-5][0-9]$/D",
                $values["time"]
            ) !== 1
        ) {
            $errors["time"] = Lang::get("appointment.invalid_time");
        }

        if (
            !isset($errors["status"])
            && !array_key_exists($values["status"], $statusLabels)
        ) {
            $errors["status"] = Lang::get("appointment.invalid_status");
        }

        try {
            $patientModel = new Patient();
            $patients = $patientModel->select("last_name");

            $doctorModel = new Doctor();
            $doctors = $doctorModel->select("last_name");

            $serviceModel = new Service();
            $services = $serviceModel->select("name");
        } catch (PDOException $exception) {
            $this->databaseError($exception);
            return;
        }

        if (
            !isset($errors["patient_id"])
            && !in_array(
                (int) $values["patient_id"],
                array_column($patients, "id")
            )
        ) {
            $errors["patient_id"] = Lang::get("patient.not_found");
        }

        if (
            !isset($errors["doctor_id"])
            && !in_array(
                (int) $values["doctor_id"],
                array_column($doctors, "id")
            )
        ) {
            $errors["doctor_id"] = Lang::get("appointment.doctor_not_found");
        }

        $selectedServices = [];
        $submittedServices = $data["services"] ?? [];

        $validServiceIds = array_map("intval", array_column($services, "id"));

        if (!is_array($submittedServices)) {
            $errors["services"] = Lang::get("appointment.invalid_services");
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
                    $errors["services"] = Lang::get("appointment.invalid_service");
                    continue;
                }

                $selectedServices[] = $serviceId;
            }
        }

        $selectedServices = array_values(array_unique($selectedServices));

        if (empty($selectedServices)) {
            $errors["services"] = Lang::get("appointment.service_required");
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
                        "pageTitle" => Lang::get("appointment.not_found_title") . " — MédiCabinet",
                        "message" => Lang::get("appointment.not_found"),
                    ]);

                    return;
                }

                $success = "updated";
            }
        } catch (PDOException $exception) {
            $this->databaseError($exception);
            return;
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
                "pageTitle" => Lang::get("error.invalid_request") . " — MédiCabinet",
                "message" => Lang::get("appointment.invalid_id"),
            ]);

            return;
        }

        try {
            $appointmentModel = new Appointment();
            $appointment = $appointmentModel->selectId($id);

            if ($appointment === false) {
                http_response_code(404);

                View::render("error", [
                    "pageTitle" => Lang::get("appointment.not_found_title") . " — MédiCabinet",
                    "message" => Lang::get("appointment.not_found"),
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
            $this->databaseError($exception);
            return;
        }

        View::render("appointment/edit", [
            "pageTitle" => Lang::get("appointment.edit") . " — MédiCabinet",
            "heading" => Lang::get("appointment.edit"),
            "patients" => $patients,
            "doctors" => $doctors,
            "services" => $services,
            "statusLabels" => [
                "planned" => Lang::get("status.planned"),
                "confirmed" => Lang::get("status.confirmed"),
                "completed" => Lang::get("status.completed"),
                "cancelled" => Lang::get("status.cancelled"),
                "absent" => Lang::get("status.absent"),
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
            "submitLabel" => Lang::get("common.save_changes"),
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
                "pageTitle" => Lang::get("error.invalid_request") . " — MédiCabinet",
                "message" => Lang::get("appointment.invalid_id"),
            ]);

            return;
        }

        $this->save($data, $id);
    }

    public function confirmDelete(array $data = []): void
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
                "message" => Lang::get("appointment.invalid_id"),
            ]);

            return;
        }

        try {
            $model = new Appointment();
            $appointment = $model->findWithDetails($id);
        } catch (PDOException $exception) {
            $this->databaseError($exception);
            return;
        }

        if ($appointment === false) {
            http_response_code(404);

            View::render("error", [
                "pageTitle" => Lang::get("appointment.not_found_title") . " — MédiCabinet",
                "message" => Lang::get("appointment.not_found"),
            ]);

            return;
        }

        View::render("appointment/delete", [
            "pageTitle" => Lang::get("appointment.delete") . " — MédiCabinet",
            "appointment" => $appointment,
            "csrfToken" => $_SESSION["csrf_token"],
        ]);
    }

    public function delete(array $data, array $query = []): void
    {
        $token = $data["csrf_token"] ?? "";

        if (
            !is_string($token)
            || !hash_equals($_SESSION["csrf_token"], $token)
        ) {
            http_response_code(403);

            View::render("error", [
                "pageTitle" => Lang::get("error.request_denied") . " — MédiCabinet",
                "message" => Lang::get("error.form_expired_reopen"),
            ]);

            return;
        }

        $id = filter_var(
            $data["id"] ?? null,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        if ($id === false) {
            http_response_code(400);

            View::render("error", [
                "pageTitle" => Lang::get("error.invalid_request") . " — MédiCabinet",
                "message" => Lang::get("appointment.invalid_id"),
            ]);

            return;
        }

        try {
            $model = new Appointment();
            $deleted = $model->delete($id);
        } catch (PDOException $exception) {
            $this->databaseError($exception);
            return;
        }

        if (!$deleted) {
            http_response_code(404);

            View::render("error", [
                "pageTitle" => Lang::get("appointment.not_found_title") . " — MédiCabinet",
                "message" => Lang::get("appointment.not_found"),
            ]);

            return;
        }

        View::redirect("appointments?success=deleted");
    }
    private function databaseError(PDOException $exception): void
    {
        // Détail technique réservé au journal du serveur.
        error_log("Erreur SQL dans MédiCabinet : " . $exception->getMessage());

        http_response_code(500);

        View::render("error", [
            "pageTitle" => Lang::get("error.service_unavailable") . " — MédiCabinet",
            "message" => Lang::get("error.operation_failed"),
        ]);
    }
}
