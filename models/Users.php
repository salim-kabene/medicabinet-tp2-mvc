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
}
