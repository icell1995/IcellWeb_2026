<?php

namespace App\Services\DivtikService;

use App\Mail\OtpMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;

class DivtikOTPService
{
    protected $expiryMinutes;
    protected $otpLength;

    public function __construct($expiryMinutes = 3, $otpLength = 6)
    {
        $this->expiryMinutes = $expiryMinutes;
        $this->otpLength = $otpLength;
    }

    public function generateOtp()
    {
        return Str::padLeft(random_int(0, pow(10, $this->otpLength) - 1), $this->otpLength, '0');
    }

    public function saveOtpUser(User $user, $otp)
    {
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes($this->expiryMinutes);

        $timestamps = ($user->timestamps != null && $user->timestamps != 1) ? $user->timestamps : [];
        $timestamps = array_merge($timestamps, ['last_login_attempt' => now()]);
        $user->timestamps = $timestamps;

        $user->save();
    }

    public function sendOtpToEmail(User $user, $customSubject = null)
    {
        if (empty($user->email)) {
            throw new \Exception('Email Pengguna Tidak Terdaftar');
        }

        try {
            Mail::to($user->email)->send(
                new OtpMail(
                    $user->otp_code,
                    $this->expiryMinutes,
                    $customSubject ?? 'Kode OTP Anda'
                )
            );

            Log::info("OTP Sent Successfully to User: $user->email");

            return true;
        } catch (\Exception $e) {
            $statusCode = method_exists($e, 'getCode') ? $e->getCode() : 500;

            Log::error("Email Polri Error: ". $e->getMessage());
            Log::error('Email Polri Error', [
                'status_code' => $statusCode,
                'error' => $e->getMessage(),
                'request' => request()->all() // Opsional: log data request
            ]);

            if($statusCode === 0){
                return 'email_error';
            }
        }
    }
}
