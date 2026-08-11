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
            "default-src 'self' http: https: ws: wss: data: blob: 'unsafe-inline' 'unsafe-eval';"
        );

        return $response;
    }
}
