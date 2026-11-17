<?php

namespace App\Controllers;

use App\Models\Log;
use App\Providers\Auth;
use App\Providers\View;
use App\Providers\Lang;

class LogController
{
    public function __construct()
    {
        Auth::privilege(1);
    }

    public function index()
    {
        $page = filter_var(
            $_GET["page"] ?? 1,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        if ($page === false) {
            $page = 1;
        }

        $perPage = 12;

        try {
            $log = new Log();

            $total = (int) $log->countAll();
            $totalPages = max(1, (int) ceil($total / $perPage));

            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;

            $logs = $log->selectPage($perPage, $offset);
        } catch (\PDOException $e) {
            http_response_code(500);

            View::render("error", [
                "pageTitle" => Lang::get("error.service_unavailable") . " — MédiCabinet",
                "message" => Lang::get("log.failed")
            ]);

            return;
        }

        View::render("log/index", [
            "pageTitle" => Lang::get("nav.logs") . " — MédiCabinet",
            "logs" => $logs,
            "currentPage" => $page,
            "totalPages" => $totalPages,
            "total" => $total,
            "firstEntry" => $total > 0 ? $offset + 1 : 0,
            "lastEntry" => min($offset + $perPage, $total)
        ]);
    }
}
