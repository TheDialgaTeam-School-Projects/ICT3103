<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * @inheritDoc
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return Helper::route('user_authentication.login_post');
        }
    }
}
