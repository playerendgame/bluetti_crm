<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiToken;

class EnsureApiTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if(!$token){
        
            $token = str_replace('Bearer ', '', $token);

            $hashedToken = hash('sha256', $token);

            $apiToken = ApiToken::valid()->where('token', $hashedToken)->first();

            if(!$apiToken){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API token'
                ], 401);
            }

            $apiToken->update(['last_used_at' => now()]);
            $request->attributes->set('admin', $apiToken->admin);
            $request->attributes->set('api_token', $apiToken);

            return $next($request);
        }
    }

}
