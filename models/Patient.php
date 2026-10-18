<?php

namespace App\Models;

class Patient extends CRUD
{
    protected $table = "patients";
    protected $primaryKey = "id";
    protected $fillable = [
        "first_name",
        "last_name",
        "birth_date",
        "phone",
        "email",
    ];
}