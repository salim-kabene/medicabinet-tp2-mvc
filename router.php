<?php

// Lancement : php -S localhost:8001 router.php

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if (!is_string($path)) {
    http_response_code(400);
    exit;
}

$path = rawurldecode($path);

if (str_contains($path, "\0")) {
    http_response_code(400);
    exit;
}

$file = realpath(__DIR__ . $path);

$assetsDirectory = realpath(__DIR__ . "/public/assets");

$allowedExtensions = [
    "css",
    "png",
    "jpg",
    "jpeg",
    "gif",
    "webp",
    "svg",
    "ico",
    "woff",
    "woff2",
    "ttf",
];

if (
    $file !== false
    && $assetsDirectory !== false
    && is_file($file)
    && str_starts_with($file, $assetsDirectory . DIRECTORY_SEPARATOR)
    && in_array(
        strtolower(pathinfo($file, PATHINFO_EXTENSION)),
        $allowedExtensions,
        true
    )
) {
    return false;
}

// Les fichiers internes ne doivent pas être accessibles directement.
if ($file !== false && is_file($file)) {
    http_response_code(403);
    exit("Accès interdit.");
}

require __DIR__ . "/index.php";
