<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        'cms/maintenance-mode*',
    ];

    /**
     * Handle an incoming request.
     * Checks if maintenance duration has expired and auto-deactivates if so.
     */
    public function handle($request, Closure $next)
    {
        if ($this->app->maintenanceMode()->active()) {
            $data = $this->app->maintenanceMode()->data();

            // Auto-up: if end_time is set and has passed, deactivate maintenance
            if (isset($data['end_time']) && time() >= $data['end_time']) {
                $this->app->maintenanceMode()->deactivate();
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }
}
