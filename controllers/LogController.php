<?php

namespace App\Controllers;

use App\Models\Log;
use App\Providers\Auth;
use App\Providers\View;

class LogController
{
    public function __construct()
    {
        Auth::privilege(1);
    }

    public function index()
    {
        try {
            $log = new Log();
            $logs = $log->select("id", "DESC");
        } catch (\PDOException $e) {
            http_response_code(500);

            View::render("error", [
                "message" => "Impossible de charger le journal des visites."
            ]);

            return;
        }

        View::render("log/index", [
            "pageTitle" => "Journal de bord — MédiCabinet",
            "logs" => $logs
        ]);
    }
}
