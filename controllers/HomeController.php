<?php

namespace App\Controllers;

use App\Providers\View;

class HomeController
{
    public function index(): void
    {
        View::render("home", [
            "pageTitle" => "Accueil — MédiCabinet",
            "message" => "Bienvenue dans MédiCabinet",
        ]);
    }
}