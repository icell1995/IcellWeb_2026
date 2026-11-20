<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Mews\Captcha\CaptchaServiceProvider;
use Mews\Captcha\Facades\Captcha;
use App\Services\Whatsapp\WhatsappWebhookService;
use App\Services\DivtikService\DivtikOTPService;


use App\Models\Log\UserLogin as LogUserLogin;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update;

class LoginController extends Controller
{

    public function login()
    {
        return redirect('login');
    }

    public function get_login()
    {

        if (Auth::check()) { // true sekalian session field di users nanti bisa dipanggil via Auth
            //Login Success
            // dd(Auth::check());
            return redirect('home');
        }

        // dd(Auth::check());
        return view('Auth.login');
    }

    public function myCaptcha()
    {
        return view('myCaptcha');
    }

    protected $otpService;

    public function __construct(DivtikOTPService $otpService)
    {
        $this->otpService = $otpService;
    }



    public function authenticate(Request $request, Captcha $captcha)
    {
        // Inputan yg diambil
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
            'captcha' => 'required|captcha'
        ], ['captcha.captcha' => 'Kode CAPTCHA yang dimasukkan tidak sesuai.']);

        $user = User::with('officer')->where('username', $request->username)->first();

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 'errorCaptcha'
            ]);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kombinasi username dan password salah'
            ]);
        }

        if (!$user->is_active || !$user->officer || empty($user->officer)) {
            return response()->json(['status' => 'error', 'message' => 'Harap masukan pengguna yang aktif atau hubungi admin satker']);
        }
        if (!in_array($user->officer->status, ['PRESENT', 'ASSISTANCE'])) {
            return response()->json(['status' => 'error', 'message' => 'Akun anda sedang tidak aktif, silahkan hubungi admin satker']);
        }
        if (empty($user->officer->position_id) && $user->role_id != 1) {
            return response()->json(['status' => 'error', 'message' => 'Akun anda sedang di nonaktifkan karena datanya belum lengkap, silahkan hubungi admin satker untuk melengkapi data anda']);
        }

        try {
            $otp = $this->otpService->generateOtp();
            $this->otpService->saveOtpUser($user, $otp);

            switch ($user->role_id) {
                case 1:
                    $response = [
                        'status' => 'otp_required',
                        'message' => 'Kode OTP Super Admin:',
                        'otp' => $otp
                    ];
                    break;

                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                default:
                    $emailPolri = $this->otpService->sendOtpToEmail($user);

                    if ($emailPolri === 'email_error') {
                        $response = [
                            'status' => 'email_error',
                            'message' => 'OTP Gagal Kirim ke Email Polri',
                            'otp' => null
                        ];
                    } else {
                        $response = [
                            'status' => 'otp_required',
                            'message' => 'OTP Anda Telah Terkirim ke Email Polri Anda',
                            'otp' => null
                        ];
                    }
                    break;
            }

            session(['otp_user_id' => $user->id]);
        } catch (\Exception $e) {
            $statusCode = method_exists($e, 'getCode') ? $e->getCode() : 500;

            Log::error("Gagal mengirim OTP", [
                'status_code' => $statusCode,
                'error' => $e->getMessage()
            ]);

            $response = [
                'status' => 'error',
                'message' => 'Gagal Mengirim OTP ke Email Polri',
                'otp' => null
            ];
        }
        return response()->json($response);
    }


    private function sendOTPViaWhatsapp($user, $code)
    {
        $phoneNumber = $user->officer->phone_number ?? $user->phone ?? null;
        if (!$phoneNumber || empty($phoneNumber)) {
            Log::warning("No phone number for user ID {$user->id}");
            return 'invalid_phone';
        }

        // Normalisasi otomatis: Ubah awalan '0' atau '+62' menjadi '62'
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        } else if (str_starts_with($phoneNumber, '+62')) {
            $phoneNumber = substr($phoneNumber, 1);
        }

        if (!preg_match('/^62[0-9]{8,15}$/', $phoneNumber)) {
            Log::warning("Invalid phone number format for user ID {$user->id} : {$phoneNumber}");
            return 'invalid_format';
        }

        try {
            $whatsapp = new WhatsappWebhookService();
            $result = $whatsapp->sendMessageTemplate(
                destinationPhoneNumber: $phoneNumber, // Ganti dengan field phone user
                templateId: config('services.whatsapp.templates.otp_code'),
                props: [
                    '{officerName}' => $user->officer->full_name ?? $user->name,
                    '{codeOTP}' => $code,
                ]
            );

            return $result;
        } catch (\Exception $e) {
            $statusCode = method_exists($e, 'getCode') ? $e->getCode() : 500;

            Log::error("Gagal mengirim OTP WhatsApp: " . $e->getMessage());

            Log::error('WhatsApp API Error', [
                'status_code' => $statusCode,
                'error' => $e->getMessage(),
                'request' => request()->except(['password', 'password_confirmation', 'otp'])
            ]);

            if ($statusCode === 502 || $statusCode === 500) {
                return 'api_error';
            }

            if ($statusCode === 400) {
                return 'unauthorized';
            }

            if ($statusCode === 401) {
                return 'saungwa_logout';
            }

            return 'other_error';
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = session('otp_user_id');

        if (!$userId) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Session OTP tidak ditemukan, Silahkan Login Ulang'
            ]);
        }

        $user = User::with('role', 'police')->find($userId);

        // Hitung jumlah percobaan OTP dari session
        $otpAttempts = session('otp_attempts', 0);

        if (!$user || $user->otp_code !== $request->otp || now()->gt($user->otp_expires_at)) {
            $otpAttempts++;
            session(['otp_attempts' => $otpAttempts]);

            // Jika salah 5 kali, reset OTP dan beri pesan
            if ($otpAttempts >= 5) {
                if ($user) {
                    $user->otp_code = null;
                    $user->otp_expires_at = null;
                    $user->save();
                }
                session()->forget('otp_attempts');

                return response()->json([
                    'status' => 'error',
                    'message' => 'Percobaan melebihi batas! Silakan menunggu untuk mengirim ulang OTP.'
                ], 422);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'OTP Salah. Silahkan Coba Kembali'
            ], 422);
        }

        // Jika OTP benar, reset percobaan
        session()->forget('otp_attempts');
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user);

        LogUserLogin::create([
            'user_id' => $user->id,
            'username' => $user->username,
            'name' => $user->first_name . ' ' . $user->last_name,
            'role_id' => $user->role_id,
            'police_id' => $user->police_id,
            'role_name' => $user->role->full_name ?? '',
            'police_name' => $user->police->full_name ?? '',
            'user_agent' => $request->server('HTTP_USER_AGENT'),
            'ip_address' => $this->getPublicIpAddress($request),
            'login_at' => Carbon::now()
        ]);

        session()->forget('otp_user_id');

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'redirect' => route('home')
        ]);
    }

    public function refreshCaptcha()
    {
        return response()->json(['captcha' => Captcha::img()]);
    }

    public function forget_password()
    {
        return view('auth.passwords.forget-password');
    }

    public function post_forget_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);
        $token = Str::random(60);

        DB::table('password_resets')->insert(
            ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
        );

        Mail::send('auth.passwords.verify', ['token' => $token], function ($message) use ($request) {
            $message->from('icell.korlantas@gmail.com');
            $message->to($request->email);
            $message->subject('Reset Password Notification');
        });

        return back()->with('message', 'We have e-mailed your password reset link!');
    }


    public function reset_password($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }


    public function post_reset_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',

        ]);

        $updatePassword = DB::table('password_resets')
            ->where(['email' => $request->email, 'token' => $request->token])
            ->first();

        if (!$updatePassword)
            return back()->withErrors(['Invalid Token'])->withInput();

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect('/login')->with('message', 'Your password has been changed!');
    }

    private function getPublicIpAddress(Request $request)
    {
        return $request->ip();
    }

    public function resendOtp(Request $request)
    {
        return $this->resendsOtp($request);
    }

    public function resendsOtp(Request $request)
    {
        try {
            // Get user from session or previous login attempt
            $username = session('otp_user_id');

            if (!$username) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi OTP tidak valid. Silakan login ulang.'
                ], 400);
            }

            $user = User::where('id', $username)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan. Silakan login ulang.'
                ], 400);
            }

            // Generate new OTP
            $otp = random_int(100000, 999999);
            $user->otp_code = $otp;
            $user->otp_expires_at = now()->addMinute(3);
            $user->save();

            // Log the resend attempt
            Log::info('OTP Resent', [
                'user_id' => $user->id,
                'username' => $user->username,
                'role_id' => $user->role_id,
                'timestamp' => now()
            ]);

            if ((int) $user->role_id === 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kode OTP baru Super Admin:',
                    'otp' => $otp
                ]);
            }

            $emailPolri = $this->otpService->sendOtpToEmail($user);
            if ($emailPolri === 'email_error') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim OTP ke Email Polri. Silakan coba kembali.',
                    'otp' => null
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP baru telah dikirim ke Email Polri Anda.',
                'otp' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Resend OTP Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Silakan coba lagi.'
            ], 500);
        }
    }
}
