<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim;

use App\Http\Controllers\Controller;
use App\Models\PusiknasApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Endpoint Get Token untuk integrasi Pusiknas Bareskrim.
     * 
     * Method : POST
     * Path   : /icell-services/api-pusiknasbareskrim/GetTokenICELL
     */
    public function getToken(Request $request)
    {
        // Support parameter dengan format Username / username dan Password / password
        $username = $request->input('Username', $request->input('username'));
        $password = $request->input('Password', $request->input('password'));

        if (empty($username) || empty($password)) {
            return response()->json([
                'code'    => '422',
                'status'  => 'UNPROCESSABLE_ENTITY',
                'message' => 'Field Username dan Password wajib diisi.',
            ], 422);
        }

        $validUsername = config('services.pusiknas.api_username');
        $validPassword = config('services.pusiknas.api_password');

        if (empty($validUsername) || empty($validPassword)) {
            return response()->json([
                'code'    => '500',
                'status'  => 'INTERNAL_SERVER_ERROR',
                'message' => 'Konfigurasi kredensial API Pusiknas belum diatur di server.',
            ], 500);
        }

        if (!hash_equals((string) $validUsername, (string) $username) || !hash_equals((string) $validPassword, (string) $password)) {
            return response()->json([
                'code'    => '401',
                'status'  => 'UNAUTHORIZED',
                'message' => 'Username atau Password tidak valid.',
            ], 401);
        }

        // Generate token acak 64 karakter (32 bytes hex)
        $token = bin2hex(random_bytes(32));

        // Token berlaku hingga akhir hari ini pukul 23:59:59
        $expiresAt = now()->endOfDay();

        // Simpan ke database ICELL
        PusiknasApiToken::create([
            'token'      => $token,
            'username'   => $username,
            'ip_address' => $request->ip(),
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'data' => $token,
        ], 200);
    }
}
