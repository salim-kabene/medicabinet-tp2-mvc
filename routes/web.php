<?php

use App\Routes\Route;

Route::get("/", "HomeController@index");
Route::get("/home", "HomeController@index");

Route::get("/patients", "PatientController@index");
Route::get("/patient/show", "PatientController@show");

Route::get("/appointment/delete", "AppointmentController@confirmDelete");
Route::get("/appointment/create", "AppointmentController@create");
Route::get("/appointment/edit", "AppointmentController@edit");
Route::get("/appointments", "AppointmentController@index");

Route::post("/appointment/create", "AppointmentController@store");
Route::post("/appointment/edit", "AppointmentController@update");
Route::post("/appointment/delete", "AppointmentController@delete");


Route::dispatch();