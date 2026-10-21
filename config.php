<?php

define("BASE", "");
define("ASSET", BASE . "/public/assets");

date_default_timezone_set("America/Toronto");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}