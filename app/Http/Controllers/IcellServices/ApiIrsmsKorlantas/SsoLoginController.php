<?php

namespace App\Http\Controllers\IcellServices\ApiIrsmsKorlantas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\SSOHelper;

class SsoLoginController extends Controller
{
    /**
     * Menangani login via SSO dengan token dari IRSMS.
     */
    public function handleSSOLogin(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect('/login')->withErrors(['Token tidak ditemukan']);
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json'
        ])->get('https://irsms.korlantas.polri.go.id/verify-token', [
            'token' => $token,
        ]);

        if ($response->failed()) {
            return redirect('/login')->withErrors(['Terjadi kesalahan saat verifikasi token']);
        }

        $data = $response->json();

        if ($data['status'] === 'success' && isset($data['user']['username'])) {
            $username = $data['user']['username'];

            $user = User::where('username', $username)->first();

            if (!$user) {
                return redirect('/login')->withErrors(['Data pengguna tidak terdaftar di ICELL']);
            }

            Auth::login($user);

            return redirect('/home')->with('success','Berhasil Login dari IRSMS');
        } elseif ($data['status'] === 'expired') {
            return redirect('/login')->withErrors(['Token login kadaluarsa']);
        }

        return redirect('/login')->withErrors([$data['message'] ?? 'Token tidak valid']);
    }

    /**
     * Redirect pengguna ICELL ke IRSMS dengan token SSO.
     */
    public function redirectTo($target)
    {
        $user = Auth::user(); // Gantikan Sentinel::check()
        
        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized',
            ], 401);
        }

        $token = SSOHelper::generateToken($user->id);

        if ($target === 'irsms') {
            if (!$user->hasPermission('irsms.R')) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Akses IRSMS tidak diizinkan untuk role Anda',
                ], 403);
            }
            $redirectUrl = 'https://irsms.korlantas.polri.go.id/sso-login?token=' . $token;
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unknown target',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'redirect_url' => $redirectUrl,
        ]);
    }

    /**
     * Endpoint untuk memverifikasi token dari SSO.
     */
    public function verifyToken(Request $request)
    {
        if (!$request->has('token')) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Token tidak ditemukan',
            ], 400);
        }

        $result = SSOHelper::validateToken($request->token);

        if ($result['status'] !== 'success') {
            $httpCode = $result['status'] === 'expired' ? 401 : 404;

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
            ], $httpCode);
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'user' => [
                'id' => $result['user']->id,
                'username' => $result['user']->username,
            ]
        ]);
    }
}
