<?php

namespace App\Providers;

class Lang
{
    public static function locale(): string
    {
        $locale = $_SESSION["lang"] ?? "fr";

        return in_array($locale, ["fr", "en"], true)
            ? $locale
            : "fr";
    }

    public static function translations(): array
    {
        $locale = self::locale();

        return require __DIR__ . "/../lang/" . $locale . ".php";
    }

    public static function get(string $key, array $parameters = []): string
    {
        static $catalogues = [];

        $locale = self::locale();

        if (!isset($catalogues[$locale])) {
            $catalogues[$locale] = self::translations();
        }

        $text = $catalogues[$locale][$key] ?? $key;

        return strtr($text, $parameters);
    }
    public static function serviceLabel(string $name): string
    {
        $keys = [
            "Consultation générale" => "service.general",
            "Suivi médical" => "service.follow_up",
            "Bilan préventif" => "service.preventive",
        ];

        return isset($keys[$name]) ? self::get($keys[$name]) : $name;
    }

    public static function serviceList(?string $names): string
    {
        if ($names === null || $names === "") {
            return self::get("appointment.no_service");
        }

        // Même séparateur que GROUP_CONCAT dans Appointment.
        $labels = [];

        foreach (explode(" + ", $names) as $name) {
            $labels[] = self::serviceLabel($name);
        }

        return implode(" + ", $labels);
    }
}
