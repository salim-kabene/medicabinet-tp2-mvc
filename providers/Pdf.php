<?php

namespace App\Providers;

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf
{
    public static function download(string $html, string $filename): void
    {
        $options = new Options();

        $options->set("defaultFont", "DejaVu Sans");
        $options->set("isRemoteEnabled", false);
        $options->set("isPhpEnabled", false);
        $options->set("isJavascriptEnabled", false);
        $options->set("chroot", realpath(__DIR__ . "/../public/assets"));

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, "UTF-8");
        $dompdf->setPaper("A4", "portrait");
        $dompdf->render();

        header("Cache-Control: private, no-store, max-age=0");
        header("X-Content-Type-Options: nosniff");

        $dompdf->stream($filename, [
            "Attachment" => true
        ]);

        exit;
    }
}
