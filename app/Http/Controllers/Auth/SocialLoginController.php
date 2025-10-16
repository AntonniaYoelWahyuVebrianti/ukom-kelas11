<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialLoginController extends Controller
{
    public function redirect(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $exception) {
            Log::warning('Google login failed', ['error' => $exception->getMessage()]);

            return redirect()->route('login')->withErrors([
                'email' => __('Unable to authenticate with Google. Please try again.'),
            ]);
        }

        $user = User::firstOrNew(['google_id' => $googleUser->getId()]);

        if (! $user->exists) {
            $user = User::where('email', $googleUser->getEmail())->first() ?? $user;
        }

        $user->forceFill([
            'google_id' => $googleUser->getId(),
            'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: $googleUser->getEmail()),
            'email' => $googleUser->getEmail(),
            'avatar' => $googleUser->getAvatar(),
            'password' => Str::random(40),
            'email_verified_at' => now(),
        ])->save();

        Auth::login($user, true);

        session()->regenerate();

        return redirect()->intended('/');
    }
}
