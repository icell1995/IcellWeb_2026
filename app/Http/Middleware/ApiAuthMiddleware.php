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
    public function handle(Request $request, Closure $next)
    {
        $tokens = [
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtRU1QIiwicHJvdmlkZXIiOiJJQ0VMTC1BUEkifQ.EhjiING3v9rX54P3afd29H4TRzV0LlI9t3AHyjiWGKg', //ICELL-EMP
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtVEFSIiwicHJvdmlkZXIiOiJJQ0VMTC1BUEkifQ.2A2TDuDtWXyNLHiUB4LTPpBC6L5nIlerFI07pEnuQeI', //ICELL-TAR
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtSVJTTVMiLCJwcm92aWRlciI6IklDRUxMLUFQSSJ9.BEy5KZ6CQqAZgRI6nnwEW-u80WB4zKcO_hJBuABPqWE', //ICELL-IRSMS
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtIiwicHJvdmlkZXIiOiJJQ0VMTC1BUEkifQ.4v3w3pXrX1sY4mJtX5sT8eGf6b0g9yZ1zQ9v62H3Fs=divtik', //ICELL-DIVTIK
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtU1AySFAiLCJwcm92aWRlciI6IklDRUxMLUFQSSJ9.7kZ9mN2pQ8vWxJ4sL3rY6tH5uA1bC0gD9fE2_sp2hp', //ICELL-SP2HP        
        ];

        $rawAuth = $request->header('AUTHORIZATION') ?? $request->header('Authorization');

        // Normalisasi token string (bisa dikirim dengan 'Bearer ' atau raw token)
        $token = $request->bearerToken();
        if (!$token && $rawAuth) {
            $token = trim(preg_replace('/^Bearer\s+/i', '', $rawAuth));
        }

        $isPusiknasDoc = $request->is('icell-services/api-pusiknasbareskrim/doc*');

        // 1. Cek static token bawaan ICELL (HANYA untuk endpoint non-Pusiknas Dokumen)
        if (!$isPusiknasDoc) {
            if ($token && in_array($token, $tokens)) {
                return $next($request);
            }
        }

        // 2. Cek dynamic token (Pusiknas API Token)
        if ($token) {
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
