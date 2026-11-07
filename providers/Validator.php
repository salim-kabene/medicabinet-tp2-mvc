<?php

namespace App\Providers;

class Validator
{
    private array $errors = [];

    private string $key;
    private string $value;
    private string $name;

    public function field(string $key, string $value, ?string $name = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->name = $name ?? ucfirst($key);

        return $this;
    }

    public function required()
    {
        if (trim($this->value) === "") {
            $this->errors[$this->key] = "$this->name est obligatoire.";
        }

        return $this;
    }

    public function int(?int $minimum = null)
    {
        if (trim($this->value) === "") {
            return $this;
        }

        $number = filter_var($this->value, FILTER_VALIDATE_INT);

        if ($number === false) {
            $this->errors[$this->key] = "$this->name doit être un entier.";
        } elseif ($minimum !== null && $number < $minimum) {
            $this->errors[$this->key] = "$this->name doit être supérieur ou égal à $minimum.";
        }

        return $this;
    }

    public function max(int $length)
    {
        $count = preg_match_all("/./us", $this->value);

        if ($count === false || $count > $length) {
            $this->errors[$this->key] = "$this->name doit contenir au maximum " . "$length caractères.";
        }

        return $this;
    }

    public function isSuccess(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function min(int $length)
    {
        $count = preg_match_all("/./us", $this->value);

        if ($count === false || $count < $length) {
            $this->errors[$this->key] = "$this->name doit contenir au minimum $length caractères.";
        }

        return $this;
    }

    public function email()
    {
        if (filter_var($this->value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$this->key] = "$this->name doit être une adresse courriel valide.";
        }

        return $this;
    }

    public function unique(string $model)
    {
        if (isset($this->errors[$this->key])) {
            return $this;
        }

        $modelClass = "App\\Models\\" . $model;
        $modelInstance = new $modelClass();

        $existing = $modelInstance->unique($this->key, $this->value);

        if ($existing !== false) {
            $this->errors[$this->key] = "$this->name est déjà utilisé.";
        }

        return $this;
    }
}
