<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class MaintenanceDownCommand extends Command
{
    /**
     * The name and signature of the console command.
     * Overrides the built-in "down" command.
     *
     * @var string
     */
    protected $signature = 'down
        {--redirect= : The path that users should be redirected to}
        {--render= : The view that should be prerendered for display during maintenance mode}
        {--retry= : The number of seconds after which the request may be retried}
        {--refresh= : The number of seconds after which the browser may refresh}
        {--secret= : The secret phrase that may be used to bypass maintenance mode}
        {--status=503 : The status code that should be used when returning the maintenance mode response}
        {--duration= : Duration of maintenance in minutes. App auto-recovers after this time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put the application into maintenance mode with optional auto-recovery duration';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $payload = $this->getDownPayload();

        $this->laravel->maintenanceMode()->activate($payload);

        $this->components->info('Application is now in maintenance mode.');

        if ($secret = $this->option('secret')) {
            $this->components->info('Bypass maintenance mode via: ' . url($secret));
        }

        $duration = $this->option('duration');
        if ($duration) {
            $endTime = Carbon::createFromTimestamp($payload['end_time']);
            $this->components->info("Maintenance will auto-end in {$duration} minutes (at {$endTime->format('d M Y H:i:s')}).");
        } else {
            $this->components->warn('No duration set. Use "php artisan up" to manually end maintenance.');
        }
    }

    /**
     * Build the maintenance mode payload.
     */
    protected function getDownPayload(): array
    {
        $payload = [
            'except'   => [],
            'redirect' => $this->option('redirect') ?: '',
            'retry'    => $this->getRetryTime(),
            'refresh'  => $this->option('refresh') ? (int) $this->option('refresh') : null,
            'secret'   => $this->option('secret') ?: '',
            'status'   => (int) $this->option('status'),
            'template' => null,
        ];

        // Add duration/end_time if specified
        $duration = $this->option('duration');
        if ($duration && is_numeric($duration)) {
            $endTime = Carbon::now()->addMinutes((int) $duration);
            $payload['end_time'] = $endTime->timestamp;
            $payload['duration_minutes'] = (int) $duration;
            $payload['started_at'] = Carbon::now()->timestamp;
        }

        return $payload;
    }

    /**
     * Get the retry-after time from the option.
     */
    protected function getRetryTime(): ?int
    {
        $retry = $this->option('retry');
        return is_numeric($retry) ? (int) $retry : null;
    }
}
