<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.portal', [
            'mode' => 'register',
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ]);

        $cacheKey = $this->otpCacheKey($validated['email']);
        $cachedOtp = Cache::get($cacheKey);

        if (! $cachedOtp || ! hash_equals($cachedOtp['code'], $validated['otp'])) {
            throw ValidationException::withMessages([
                'otp' => __('The verification code is invalid or has expired.'),
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'email_verified_at' => now(),
        ]);

        Cache::forget($cacheKey);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * Handle an incoming registration request via API.
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ]);

        $cacheKey = $this->otpCacheKey($validated['email']);
        $cachedOtp = Cache::get($cacheKey);

        if (! $cachedOtp || ! hash_equals($cachedOtp['code'], $validated['otp'])) {
            return response()->json([
                'message' => 'The verification code is invalid or has expired.',
                'errors' => [
                    'otp' => ['The verification code is invalid or has expired.']
                ]
            ], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'email_verified_at' => now(),
        ]);

        Cache::forget($cacheKey);

        Auth::login($user);

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user
        ], 201);
    }

    private function otpCacheKey(string $email): string
    {
        return 'register_otp_'.Str::lower($email);
    }
}
