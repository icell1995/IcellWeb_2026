<?php

namespace App\Console\Commands\Production;

use App\Services\ESignature\ESignatureTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RenewESignatureAPIToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:esignature:renew-api-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew E-Signature API Token and store it securely in shared runtime storage';

    /**
     * Execute the console command.
     */
    public function handle(ESignatureTokenService $tokenService): int
    {
        $lock = Cache::lock('esignature:renew-api-token', 60);

        if (!$lock->get()) {
            $this->warn('Another E-Signature token refresh process is already running.');
            return self::SUCCESS;
        }

        try {
            $host = $tokenService->getHost();
            $username = (string) config('services.esignature.username', '');
            $password = (string) config('services.esignature.password', '');

            if ($host === '' || $username === '' || $password === '') {
                $this->error('E-Signature configuration is incomplete.');
                Log::error('E-Signature token renewal aborted: incomplete configuration.');
                return self::FAILURE;
            }

            $response = Http::timeout(30)
                ->connectTimeout(10)
                ->post($host . '/api/values/GetTokenICELL', [
                    'Username' => $username,
                    'Password' => $password,
                ]);

            if (!$response->successful()) {
                $this->error('Failed to renew E-Signature API token (HTTP ' . $response->status() . ').');
                Log::error('E-Signature token renewal HTTP failure.', [
                    'status' => $response->status(),
                ]);
                return self::FAILURE;
            }

            $result = $response->json();
            $newToken = is_array($result) ? ($result['data'] ?? null) : null;

            if (!is_string($newToken) || trim($newToken) === '') {
                $this->error('Failed to renew E-Signature API token: provider response did not contain a valid token.');
                Log::error('E-Signature token renewal failed: empty token in provider response.');
                return self::FAILURE;
            }

            $tokenService->storeToken($newToken);

            $this->info('Successfully renewed E-Signature API token at ' . now()->toDateTimeString());
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed to renew E-Signature API token due to an unexpected error.');
            Log::error('E-Signature token renewal exception.', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);
            return self::FAILURE;
        } finally {
            $lock->release();
        }
    }
}
