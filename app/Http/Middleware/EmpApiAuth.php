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
        $token = env('EMP_API_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtRU1QIiwicHJvdmlkZXIiOiJJQ0VMTC1BUEkifQ.EhjiING3v9rX54P3afd29H4TRzV0LlI9t3AHyjiWGKg');

        if ($request->header('AUTHORIZATION') !== $token) {
            return response()->json([
                'code' => "401",
                'status' => 'UNAUTHORIZED',
            ], 401);
        }
 
        return $next($request);
    }
}
