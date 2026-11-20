<?php

namespace App\Http\Middleware;

use App\Models\PusiknasApiToken;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, ?string $scope = null)
    {
        $configuredTokens = array_filter((array) config('services.icell_api_tokens', []));

        $rawAuth = $request->header('AUTHORIZATION') ?? $request->header('Authorization');

        // Normalisasi token string (bisa dikirim dengan 'Bearer ' atau raw token)
        $token = $request->bearerToken();
        if (!$token && $rawAuth) {
            $token = trim(preg_replace('/^Bearer\s+/i', '', $rawAuth));
        }

        $isPusiknasDoc = $scope === 'pusiknas-doc' || $request->is('icell-services/api-pusiknasbareskrim/doc*');

        // 1. Cek static token bawaan ICELL sesuai scope service (HANYA untuk endpoint non-Pusiknas Dokumen)
        if (!$isPusiknasDoc && $token) {
            if ($scope && isset($configuredTokens[$scope])) {
                if (hash_equals((string) $configuredTokens[$scope], (string) $token)) {
                    return $next($request);
                }
            } elseif (!$scope) {
                foreach ($configuredTokens as $configuredToken) {
                    if (hash_equals((string) $configuredToken, (string) $token)) {
                        return $next($request);
                    }
                }
            }
        }

        // 2. Cek dynamic token (Pusiknas API Token)
        if ($token && ($isPusiknasDoc || !$scope)) {
            $pusiknasToken = PusiknasApiToken::where('token', $token)->first();

            if ($pusiknasToken) {
                if ($pusiknasToken->expires_at < now()) {
                    return response()->json([
                        'code'    => "401",
                        'status'  => 'UNAUTHORIZED',
                        'message' => 'Token has expired. Please generate a new token.',
                    ], 401);
                }

                // Catat IP dan waktu penggunaan token setiap kali hit
                $pusiknasToken->update([
                    'last_used_ip' => $request->ip(),
                    'last_used_at' => now(),
                ]);

                return $next($request);
            }
        }

        return response()->json([
            'code'    => "401",
            'status'  => 'UNAUTHORIZED',
            'message' => $isPusiknasDoc
                ? 'Akses ditolak. Endpoint dokumen Pusiknas wajib menggunakan Bearer Token dari API GetTokenICELL.'
                : 'Unauthorized.',
        ], 401);
    }
}
