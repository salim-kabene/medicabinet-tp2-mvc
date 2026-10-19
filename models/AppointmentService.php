<?php

namespace App\Models;

class AppointmentService
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function insert(int $appointmentId, int $serviceId): void
    {
        $sql = "INSERT INTO appointment_service
                    (appointment_id, service_id)
                VALUES
                    (:appointment_id, :service_id)";

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(
            ":appointment_id",
            $appointmentId,
            \PDO::PARAM_INT
        );

        $stmt->bindValue(
            ":service_id",
            $serviceId,
            \PDO::PARAM_INT
        );

        $stmt->execute();
    }

    public function selectIds(int $appointmentId): array
    {
        $sql = "SELECT service_id
            FROM appointment_service
            WHERE appointment_id = :appointment_id";

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(
            ":appointment_id",
            $appointmentId,
            \PDO::PARAM_INT
        );

        $stmt->execute();

        return array_map(
            "intval",
            $stmt->fetchAll(\PDO::FETCH_COLUMN)
        );
    }

    public function deleteByAppointment(int $appointmentId): void
    {
        $sql = "DELETE FROM appointment_service
            WHERE appointment_id = :appointment_id";

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(
            ":appointment_id",
            $appointmentId,
            \PDO::PARAM_INT
        );

        $stmt->execute();
    }
}