<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Password implements Rule
{
    public function passes($attribute, $value)
    {
        $capitalRule = false;
        $numberRule = false;
        $specialCharacterRule = false;

        $charValue = str_split($value);

        foreach ($charValue as $char) {
            if (preg_match('/[A-Z]/', $char) === 1) {
                $capitalRule = true;
            } else if (preg_match('/[0-9]/', $char) === 1) {
                $numberRule = true;
            } else if (preg_match('/[^A-Za-z0-9]/', $char) === 1) {
                $specialCharacterRule = true;
            }
        }

        return $capitalRule && $numberRule && $specialCharacterRule;
    }

    public function message()
    {
        return 'Your password must be at least 8 characters long, contains at least one capital letter, one number, and one special characters.';
    }
}
