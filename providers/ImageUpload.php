<?php

namespace App\Providers;

class ImageUpload
{
    public static function saveAvatar(array $file): string
    {
        if (
            !isset($file["error"], $file["tmp_name"])
            || !is_int($file["error"])
            || !is_string($file["tmp_name"])
        ) {
            throw new \InvalidArgumentException(Lang::get("upload.invalid_file"));
        }

        if ($file["error"] === UPLOAD_ERR_NO_FILE) {
            throw new \InvalidArgumentException(Lang::get("profile.select_image"));
        }

        if ($file["error"] === UPLOAD_ERR_INI_SIZE || $file["error"] === UPLOAD_ERR_FORM_SIZE) {
            throw new \InvalidArgumentException(Lang::get("upload.too_large"));
        }

        if ($file["error"] !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException(Lang::get("upload.failed"));
        }

        $temporaryPath = $file["tmp_name"];

        if (!is_uploaded_file($temporaryPath)) {
            throw new \InvalidArgumentException(Lang::get("upload.invalid_file"));
        }

        $size = filesize($temporaryPath);

        if ($size === false || $size === 0 || $size > 2 * 1024 * 1024) {
            throw new \InvalidArgumentException(Lang::get("upload.invalid_size"));
        }

        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($temporaryPath);

        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
        ];

        if (!isset($allowedTypes[$mimeType])) {
            throw new \InvalidArgumentException(Lang::get("upload.invalid_type"));
        }

        $dimensions = @getimagesize($temporaryPath);

        if ($dimensions === false || ($dimensions["mime"] ?? "") !== $mimeType) {
            throw new \InvalidArgumentException(Lang::get("upload.invalid_image"));
        }

        if (
            $dimensions[0] < 1
            || $dimensions[1] < 1
            || $dimensions[0] > 3000
            || $dimensions[1] > 3000
        ) {
            throw new \InvalidArgumentException(
                Lang::get("upload.invalid_dimensions")
            );
        }

        $directory = __DIR__ . "/../storage/avatars";

        if (!is_dir($directory) || !is_writable($directory)) {
            throw new \RuntimeException(
                "Le dossier des photos est absent ou inaccessible en écriture."
            );
        }

        $filename = bin2hex(random_bytes(16))
            . "."
            . $allowedTypes[$mimeType];

        $destination = $directory . "/" . $filename;

        if (!move_uploaded_file($temporaryPath, $destination)) {
            throw new \RuntimeException(
                "Impossible d’enregistrer la photo sur le serveur."
            );
        }

        return $filename;
    }
}
