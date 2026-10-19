<?php

namespace App\Models;

abstract class CRUD extends \PDO
{
    final public function __construct()
    {
        $config = require __DIR__ . "/../config/database.php";

        [
            "host" => $host,
            "port" => $port,
            "dbname" => $dbname,
            "username" => $username,
            "password" => $password,
            "charset" => $charset,
        ] = $config;

        parent::__construct(
            "mysql:host=$host;dbname=$dbname;port=$port;charset=$charset",
            $username,
            $password
        );

        $this->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );

        $this->setAttribute(
            \PDO::ATTR_DEFAULT_FETCH_MODE,
            \PDO::FETCH_ASSOC
        );
    }

    final public function select(?string $field = null, string $order = "ASC"): array
    {
        if ($field === null) {
            $field = $this->primaryKey;
        }
        $sql = "SELECT * FROM $this->table ORDER BY $field $order";
        $stmt = $this->query($sql);

        return $stmt->fetchAll();
    }

    final public function selectId(int|string $value): array|false
    {
        $sql = "SELECT * FROM $this->table WHERE $this->primaryKey = :value";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(":value", $value);
        $stmt->execute();

        return $stmt->fetch();
    }

    final public function countAll(): int
    {
        $sql = "SELECT COUNT(*) FROM $this->table";
        $stmt = $this->query($sql);
        return (int) $stmt->fetchColumn();
    }

    final public function insert(array $data): int
    {
        $allowedFields = array_fill_keys($this->fillable, "");

        $data = array_intersect_key($data, $allowedFields);

        if (empty($data)) {
            throw new \InvalidArgumentException("Aucune donnée autorisée à enregistrer.");
        }

        $fieldNames = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO $this->table ($fieldNames) VALUES ($placeholders)";

        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $type = $value === null ? \PDO::PARAM_NULL : \PDO::PARAM_STR;
            $stmt->bindValue(":$key", $value, $type);
        }

        $stmt->execute();
        return (int) $this->lastInsertId();
    }
}