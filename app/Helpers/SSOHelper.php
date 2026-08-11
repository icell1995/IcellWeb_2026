<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class SSOHelper
{
    /**
     * Generate SSO token untuk user yang diberikan.
     *
     * @param int $userId
     * @return string
     */
    public static function generateToken($userId)
    {
        try {
            $token = Str::random(64);

            DB::table('sso_tokens')->insert([
                'user_id'    => $userId,
                'token'      => $token,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $token;
        } catch (\Exception $e) {
            // Optional: log error jika dibutuhkan
            \Log::error('SSO Token Generation Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validasi token SSO.
     *
     * @param string $token
     * @return array
     */
    public static function validateToken($token)
    {
        $record = DB::table('sso_tokens')->where('token', $token)->first();

        if (!$record) {
            return [
                'status'  => 'not_found',
                'message' => 'Token tidak ditemukan',
                'user'    => null,
            ];
        }

        if (now()->greaterThan($record->expires_at)) {
            DB::table('sso_tokens')->where('token', $token)->delete();

            return [
                'status'  => 'expired',
                'message' => 'Token sudah kadaluarsa',
                'user'    => null,
            ];
        }

        // Ganti Sentinel dengan model User
        $user = User::find($record->user_id);

        DB::table('sso_tokens')->where('token', $token)->delete();

        return [
            'status'  => 'success',
            'message' => 'Token valid',
            'user'    => $user,
        ];
    }

}
