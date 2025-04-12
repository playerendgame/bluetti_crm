<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            $redirectUri = RouteServiceProvider::HOME;
            if (strcmp($guard, 'admins') == 0) {
                $redirectUri = "/admin/dashboard";
            }
            if (Auth::guard($guard)->check()) {
                return redirect($redirectUri);
            }
        }

        return $next($request);
    }
}
