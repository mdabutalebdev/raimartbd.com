<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->withErrors([
                'google' => 'Google Login is not configured yet. Please set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in the .env file.',
            ]);
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'google' => 'Unable to connect to Google: ' . $e->getMessage(),
            ]);
        }
    }

    public function callback()
    {
        try {
            try {
                $googleUser = Socialite::driver('google')->user();
            } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
                // If session state was lost across cross-site redirect in production, fallback to stateless
                Log::info('Google Login: InvalidStateException occurred, retrying with stateless()');
                $googleUser = Socialite::driver('google')->stateless()->user();
            }
        } catch (\Throwable $e) {
            Log::error('Google Login Error: ' . ($e->getMessage() ?: get_class($e)), [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->withErrors([
                'google' => 'Google authentication failed: ' . ($e->getMessage() ?: 'Session expired or invalid state. Please try again.'),
            ]);
        }

        // Find user by google_id or email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id and avatar if missing
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            // Register new customer via Google
            $user = User::create([
                'name' => $googleUser->getName() ?: 'User',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(24)),
                'role' => 'customer',
                'is_admin' => false,
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);

        request()->session()->regenerate();

        if ($user->isStaff()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->route('user.dashboard')->with('status', "Welcome, {$user->name}!");
    }
}
