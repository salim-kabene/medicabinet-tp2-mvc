<?php

namespace App\Models;

class Appointment extends CRUD
{
    protected $table = "appointments";

    protected $primaryKey = "id";

    protected $fillable = [
        "patient_id",
        "doctor_id",
        "starts_at",
        "status",
        "reason",
    ];

    public function selectByPatient(int $patientId): array
    {
        return array_reverse(
            $this->selectWithDetails("", "", $patientId)
        );
    }

    public function selectWithDetails(string $date = "", string $status = "", ?int $patientId = null): array
    {
        $conditions = [];

        if ($date !== "") {
            $conditions[] = "DATE(appointments.starts_at) = :date";
        }

        if ($status !== "") {
            $conditions[] = "appointments.status = :status";
        }

        if ($patientId !== null) {
            $conditions[] = "appointments.patient_id = :patient_id";
        }

        $where = "";

        if (!empty($conditions)) {
            $where = "WHERE " . implode(" AND ", $conditions);
        }

        $sql = "
        SELECT
            appointments.id,
            appointments.starts_at,
            appointments.status,
            appointments.patient_id,
            patients.first_name AS patient_first_name,
            patients.last_name AS patient_last_name,
            doctors.first_name AS doctor_first_name,
            doctors.last_name AS doctor_last_name,
            GROUP_CONCAT(services.name ORDER BY services.name SEPARATOR ' + ') AS service_names

        FROM appointments

        INNER JOIN patients
            ON appointments.patient_id = patients.id

        INNER JOIN doctors
            ON appointments.doctor_id = doctors.id

        LEFT JOIN appointment_service
            ON appointments.id = appointment_service.appointment_id

        LEFT JOIN services
            ON appointment_service.service_id = services.id

        $where

        GROUP BY
            appointments.id,
            appointments.starts_at,
            appointments.status,
            appointments.patient_id,
            patients.first_name,
            patients.last_name,
            doctors.first_name,
            doctors.last_name

        ORDER BY appointments.starts_at ASC, appointments.id ASC
        ";

        $stmt = $this->prepare($sql);

        if ($date !== "") {
            $stmt->bindValue(":date", $date);
        }

        if ($status !== "") {
            $stmt->bindValue(":status", $status);
        }

        if ($patientId !== null) {
            $stmt->bindValue(":patient_id", $patientId, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countToday(): int
    {
        $sql = "SELECT COUNT(*)
            FROM appointments
            WHERE DATE(starts_at) = :today";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(":today", date("Y-m-d"));
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function countUpcoming(): int
    {
        $sql = "SELECT COUNT(*)
            FROM appointments
            WHERE starts_at > :now
            AND status IN ('planned', 'confirmed')";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(":now", date("Y-m-d H:i:s"));
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}