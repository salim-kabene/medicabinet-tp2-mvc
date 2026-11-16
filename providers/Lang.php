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
}
