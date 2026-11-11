<?php

use App\Providers\Logger;

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/config.php";

Logger::record();

require_once __DIR__ . "/routes/web.php";
