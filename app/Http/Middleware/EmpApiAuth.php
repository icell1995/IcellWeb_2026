<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmpApiAuth
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
        $token = config('services.emp.api_token');

        if ($request->header('AUTHORIZATION') !== $token) {
            return response()->json([
                'code' => "401",
                'status' => 'UNAUTHORIZED',
            ], 401);
        }
 
        return $next($request);
    }
}
