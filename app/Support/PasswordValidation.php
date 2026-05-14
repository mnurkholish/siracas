<?php

namespace App\Support;

class PasswordValidation
{
    public const REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/';

    public static function rules(bool $confirmed = false): array
    {
        $rules = ['required', 'string', 'min:8', 'regex:' . self::REGEX];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    public static function message(): string
    {
        return 'Password harus minimal 8 karakter dan berisi huruf besar, huruf kecil, angka, serta simbol.';
    }
}
