<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MobileNumber implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^[+]*[(]?[0-9]{1,4}[)]?[-\\s.\/0-9]*$/', $value) === 1;
    }

    public function message()
    {
        return 'Mobile number is invalid.';
    }
}
