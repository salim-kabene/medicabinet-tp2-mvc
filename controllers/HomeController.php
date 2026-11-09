<?php

namespace App\Controllers;

use App\Providers\Auth;
use App\Providers\View;

class HomeController
{
    public function index(): void
    {
        if (Auth::check()) {
            if ($_SESSION["privilege_id"] === 1) {
                View::redirect("appointments");
            }

            if ($_SESSION["privilege_id"] === 2) {
                View::redirect("client");
            }
        }

        View::render("home", [
            "pageTitle" => "Bienvenue — MédiCabinet",
        ]);
    }
}
