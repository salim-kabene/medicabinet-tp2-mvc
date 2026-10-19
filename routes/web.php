<?php

use App\Routes\Route;

Route::get("/", "HomeController@index");
Route::get("/home", "HomeController@index");

Route::get("/patients", "PatientController@index");
Route::get("/patient/show", "PatientController@show");

Route::get("/appointments", "AppointmentController@index");
Route::get("/appointment/create", "AppointmentController@create");

Route::dispatch();