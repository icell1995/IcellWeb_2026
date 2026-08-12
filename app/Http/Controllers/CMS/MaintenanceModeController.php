<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceModeController extends Controller
{
    /**
     * Show the maintenance mode management page.
     */
    public function index()
    {
        $isMaintenanceActive = app()->maintenanceMode()->active();
        $maintenanceData = [];

        if ($isMaintenanceActive) {
            $maintenanceData = app()->maintenanceMode()->data();
        }

        $logs = \App\Models\Log\LogMaintenance::with('user')->orderBy('created_at', 'desc')->get();

        return view('cms.maintenance-mode.index', compact('isMaintenanceActive', 'maintenanceData', 'logs'));
    }

    /**
     * Activate maintenance mode.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'secret'   => 'required|string|min:6|max:50',
            'duration' => 'required|integer|min:1|max:1440', // 1 minute to 24 hours
        ]);

        $secret   = $request->input('secret');
        $duration = (int) $request->input('duration');
        $now      = Carbon::now();
        $endTime  = $now->copy()->addMinutes($duration);

        $payload = [
            'except'           => [],
            'redirect'         => null,
            'retry'            => $duration * 60, // retry-after in seconds
            'refresh'          => null,
            'secret'           => $secret,
            'status'           => 503,
            'template'         => null,
            'end_time'         => $endTime->timestamp,
            'duration_minutes' => $duration,
            'started_at'       => $now->timestamp,
            'activated_by'     => auth()->user() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : 'System',
        ];

        app()->maintenanceMode()->activate($payload);

        \App\Models\Log\LogMaintenance::create([
            'action' => 'activated',
            'duration_minutes' => $duration,
            'secret' => $secret,
            'ip_address' => $request->ip(),
            'user_id' => auth()->id()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Maintenance mode diaktifkan selama {$duration} menit."]);
        }

        return redirect()->route('cms.maintenance-mode.index')
            ->with('success', "Maintenance mode diaktifkan selama {$duration} menit. Bypass URL: " . url($secret));
    }

    /**
     * Deactivate maintenance mode.
     */
    public function deactivate(Request $request)
    {
        if (app()->maintenanceMode()->active()) {
            app()->maintenanceMode()->deactivate();

            \App\Models\Log\LogMaintenance::create([
                'action' => 'deactivated',
                'ip_address' => $request->ip(),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Aplikasi kembali online.']);
            }

            return redirect()->route('cms.maintenance-mode.index')
                ->with('success', 'Maintenance mode dinonaktifkan. Aplikasi kembali online.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Aplikasi sudah dalam keadaan online.']);
        }

        return redirect()->route('cms.maintenance-mode.index')
            ->with('info', 'Aplikasi sudah dalam keadaan online.');
    }

    /**
     * Get maintenance status as JSON (for AJAX polling).
     */
    public function status()
    {
        $active = app()->maintenanceMode()->active();
        $data   = $active ? app()->maintenanceMode()->data() : [];

        return response()->json([
            'active'           => $active,
            'end_time'         => $data['end_time'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'started_at'       => $data['started_at'] ?? null,
            'secret'           => $data['secret'] ?? null,
            'activated_by'     => $data['activated_by'] ?? null,
        ]);
    }
}
