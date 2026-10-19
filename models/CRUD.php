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
}