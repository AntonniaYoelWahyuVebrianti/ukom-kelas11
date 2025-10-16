<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => __('A user with this email already exists.'),
            ]);
        }

        $rateLimiterKey = $this->rateLimiterKey($data['email'], $request->ip());

        if (RateLimiter::tooManyAttempts($rateLimiterKey, 3)) {
            throw ValidationException::withMessages([
                'email' => __('Too many OTP requests. Try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($rateLimiterKey),
                ]),
            ])->status(429);
        }

        $code = (string) random_int(100000, 999999);
        $cacheKey = $this->otpCacheKey($data['email']);

        Cache::put($cacheKey, [
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ], now()->addMinutes(10));

        Mail::to($data['email'])->send(new RegistrationOtp($code));

        RateLimiter::hit($rateLimiterKey, 60);

        return response()->json([
            'status' => 'ok',
            'message' => __('Verification code sent successfully.'),
        ]);
    }

    private function otpCacheKey(string $email): string
    {
        return 'register_otp_'.Str::lower($email);
    }

    private function rateLimiterKey(string $email, string $ip): string
    {
        return 'register_otp_rate:'.Str::lower($email).'|'.$ip;
    }
}
