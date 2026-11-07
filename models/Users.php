<?php

namespace App\Models;

class Users extends CRUD
{
    protected $table = "users";
    protected $primaryKey = "id";

    protected $fillable = [
        "name",
        "username",
        "password",
        "email",
        "privilege_id",
        "patient_id",
    ];

    public function hashPassword(string $password, int $cost = 10): string
    {
        $options = ["cost" => $cost];

        return password_hash($password, PASSWORD_BCRYPT, $options);
    }
}
