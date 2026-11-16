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
            $this->errors[$this->key] = Lang::get("validation.required", [
                "{field}" => $this->name,
            ]);
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
            $this->errors[$this->key] = Lang::get("validation.integer", [
                "{field}" => $this->name,
            ]);
        } elseif ($minimum !== null && $number < $minimum) {
            $this->errors[$this->key] = Lang::get("validation.minimum", [
                "{field}" => $this->name,
                "{min}" => (string) $minimum,
            ]);
        }

        return $this;
    }

    public function max(int $length)
    {
        $count = preg_match_all("/./us", $this->value);

        if ($count === false || $count > $length) {
            $this->errors[$this->key] = Lang::get("validation.max", [
                "{field}" => $this->name,
                "{max}" => (string) $length,
            ]);
        }

        return $this;
    }

    public function min(int $length)
    {
        $count = preg_match_all("/./us", $this->value);

        if ($count === false || $count < $length) {
            $this->errors[$this->key] = Lang::get("validation.min", [
                "{field}" => $this->name,
                "{min}" => (string) $length,
            ]);
        }

        return $this;
    }

    public function email()
    {
        if (filter_var($this->value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$this->key] = Lang::get("validation.email", [
                "{field}" => $this->name,
            ]);
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
            $this->errors[$this->key] = Lang::get("validation.unique", [
                "{field}" => $this->name,
            ]);
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
