<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $admin = auth()->guard('admins')->user();

        foreach ($roles as $role) {
            if ($admin->hasRole($role)) {
                return $next($request);
            }
        }

        abort(404);
    }
}
