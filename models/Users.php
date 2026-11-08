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

    public function checkUser(string $username, string $password): bool
    {
        $user = $this->unique("username", $username);

        if ($user === false) {
            return false;
        }

        if (!password_verify($password, $user["password"])) {
            return false;
        }

        session_regenerate_id(true);

        $_SESSION["user_id"] = (int) $user["id"];
        $_SESSION["user_name"] = $user["name"];
        $_SESSION["privilege_id"] = (int) $user["privilege_id"];

        $_SESSION["patient_id"] = $user["patient_id"] === null
            ? null
            : (int) $user["patient_id"];

        $_SESSION["fingerPrint"] = md5(($_SERVER["HTTP_USER_AGENT"] ?? "") . ($_SERVER["REMOTE_ADDR"] ?? ""));

        return true;
    }
}
