<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

date_default_timezone_set("America/Toronto");

define("BASE", "");
define("ASSET", BASE . "/public/assets");