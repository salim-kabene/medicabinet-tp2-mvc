<?php

namespace App\Models;

class Log extends CRUD
{
    protected $table = "logs";
    protected $primaryKey = "id";

    protected $fillable = [
        "ip_address",
        "username",
        "user_id",
        "page",
        "http_method"
    ];

    public function selectPage(int $limit, int $offset): array
    {
        $sql = "SELECT *
            FROM logs
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(":limit", $limit, \PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
