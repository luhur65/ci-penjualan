<?php

namespace App\Validation;

class LoginValidation
{
    public function email(string $str): bool
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function username(string $str): bool
    {
        return preg_match('/^[a-zA-Z0-9_]+$/', $str) === 1;
    }

    public function password(string $str): bool
    {
        return strlen($str) >= 8;
    }

    public function fullname(string $str): bool
    {
        return strlen($str) >= 3;
    }
}
