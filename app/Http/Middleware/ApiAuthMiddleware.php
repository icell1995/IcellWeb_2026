<?php

namespace App\Http\Middleware;

use App\Models\PusiknasApiToken;
use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
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

        // 1. Cek static token bawaan ICELL
        if ($rawAuth && in_array($rawAuth, $tokens)) {
            return $next($request);
        }

        // 2. Cek dynamic token (Pusiknas API Token)
        $token = $request->bearerToken();
        if (!$token && $rawAuth) {
            $token = trim(preg_replace('/^Bearer\s+/i', '', $rawAuth));
        }

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
            'code' => "401",
            'status' => 'UNAUTHORIZED',
        ], 401);
    }
}
