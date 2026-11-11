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
}
