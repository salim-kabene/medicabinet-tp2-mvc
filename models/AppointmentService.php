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
}