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
        if ($this->value === "") {
            $this->errors[$this->key] = "$this->name est obligatoire.";
        }

        return $this;
    }

    public function int()
    {
        if (
            $this->value !== ""
            && filter_var(
                $this->value,
                FILTER_VALIDATE_INT
            ) === false
        ) {
            $this->errors[$this->key] = "$this->name doit être un entier.";
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
}