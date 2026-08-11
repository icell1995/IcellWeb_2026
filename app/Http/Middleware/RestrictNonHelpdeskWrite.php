<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictNonHelpdeskWrite
{
    /**
     * Handle an incoming request.
     * 
     * Block write operations (POST, PUT, PATCH, DELETE) for users with
     * role_id == 1 whose username does NOT start with 'Helpdesk' (case-insensitive).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (
            $user &&
            $user->role_id == 1 &&
            stripos($user->username, 'Helpdesk') !== 0 &&
            in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses untuk melakukan aksi ini.',
                ], 403);
            }

            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        return $next($request);
    }
}
