<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;

class RedirectIfSessionKeyNotFound
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $key
     * @param string $route
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $key, string $route)
    {
        if (!$request->session()->has($key)) {
            Helper::flashAlertMessage('error', Helper::__('common.invalid_session'));
            return Helper::redirect(Helper::route($route));
        }

        return $next($request);
    }
}
