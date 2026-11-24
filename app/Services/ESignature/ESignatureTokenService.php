<?php

namespace App\Services\ESignature;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ESignatureTokenService
{
    public const PROVIDER_KEY = 'icell_tte';
    private const CACHE_KEY = 'esignature:runtime_token:icell_tte';

    /**
     * Get the active E-Signature Authorization header value (e.g. "Bearer ...").
     * Safe to call with config:cache enabled across web, queue workers, and scheduler.
     */
    public function getToken(): ?string
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_string($cached) && $cached !== '') {
            try {
                return Crypt::decryptString($cached);
            } catch (Throwable) {
                Cache::forget(self::CACHE_KEY);
            }
        }

        try {
            $record = DB::table('esignature_tokens')
                ->where('provider', self::PROVIDER_KEY)
                ->first();

            if ($record && !empty($record->encrypted_token)) {
                $decrypted = Crypt::decryptString($record->encrypted_token);
                Cache::put(self::CACHE_KEY, $record->encrypted_token, now()->addMinutes(30));
                return $decrypted;
            }
        } catch (Throwable $e) {
            Log::warning('Unable to read runtime E-Signature token from shared storage; falling back to config.', [
                'error' => $e->getMessage(),
            ]);
        }

        $fallback = config('services.esignature.token');
        return is_string($fallback) && trim($fallback) !== '' ? trim($fallback) : null;
    }

    /**
     * Atomically store a newly validated E-Signature token in encrypted shared storage.
     */
    public function storeToken(string $rawToken, ?CarbonInterface $expiresAt = null): void
    {
        $normalized = $this->normalizeBearerToken($rawToken);
        $encrypted = Crypt::encryptString($normalized);
        $now = now();

        DB::transaction(function () use ($encrypted, $now, $expiresAt): void {
            DB::table('esignature_tokens')->updateOrInsert(
                ['provider' => self::PROVIDER_KEY],
                [
                    'encrypted_token' => $encrypted,
                    'refreshed_at' => $now,
                    'expires_at' => $expiresAt,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        });

        Cache::put(self::CACHE_KEY, $encrypted, now()->addMinutes(30));
    }

    /**
     * Get configured E-Signature API host without trailing slash.
     */
    public function getHost(): string
    {
        return rtrim((string) config('services.esignature.host', ''), '/');
    }

    private function normalizeBearerToken(string $token): string
    {
        $trimmed = trim($token);
        if (str_starts_with(strtolower($trimmed), 'bearer ')) {
            return 'Bearer ' . trim(substr($trimmed, 7));
        }

        return 'Bearer ' . $trimmed;
    }
}
