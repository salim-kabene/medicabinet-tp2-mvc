<?php

use App\Routes\Route;

Route::get("/", "HomeController@index");
Route::get("/home", "HomeController@index");
Route::get("/patients", "PatientController@index");

Route::dispatch();