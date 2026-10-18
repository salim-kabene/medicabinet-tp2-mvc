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
        $sql = "
            SELECT
                appointments.id,
                appointments.starts_at,
                appointments.status,
                doctors.first_name AS doctor_first_name,
                doctors.last_name AS doctor_last_name,
                GROUP_CONCAT(
                    services.name
                    ORDER BY services.name
                    SEPARATOR ' + '
                ) AS service_names
            FROM appointments

            INNER JOIN doctors
                ON appointments.doctor_id = doctors.id

            LEFT JOIN appointment_service
                ON appointments.id = appointment_service.appointment_id

            LEFT JOIN services
                ON appointment_service.service_id = services.id

            WHERE appointments.patient_id = :patient_id

            GROUP BY
                appointments.id,
                appointments.starts_at,
                appointments.status,
                doctors.first_name,
                doctors.last_name

            ORDER BY appointments.starts_at DESC, appointments.id DESC
        ";

        $stmt = $this->prepare($sql);

        $stmt->bindValue(
            ":patient_id",
            $patientId,
            \PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }
}