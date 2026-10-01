<?php

namespace App\Http\Middleware;

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
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtUFVTSUtOQVMiLCJwcm92aWRlciI6IklDRUxMLUFQSSJ9.pusiknasbareskrim2026IcellKorlantas', //ICELL-PUSIKNASBARESKRIM
        ];

        $authHeader = (string) $request->header('AUTHORIZATION');
        
        // Remove 'Bearer ' prefix if it exists
        if (str_starts_with($authHeader, 'Bearer ')) {
            $authHeader = substr($authHeader, 7);
        }

        if (!in_array($authHeader, $tokens)) {
            return response()->json([
                'code' => "401",
                'status' => 'UNAUTHORIZED',
            ], 401);
        }
 
        return $next($request);
    }
}
