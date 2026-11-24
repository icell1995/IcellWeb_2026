<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogViewerCspMiddleware
{
    /**
     * Override Content-Security-Policy header for Log Viewer routes.
     * Vue.js (used by Log Viewer) requires 'unsafe-eval' to compile templates at runtime.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self' data: blob:; script-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob:; style-src 'self' 'unsafe-inline'; font-src 'self' data:; connect-src 'self' http: https: ws: wss:;"
        );

        return $response;
    }
}
