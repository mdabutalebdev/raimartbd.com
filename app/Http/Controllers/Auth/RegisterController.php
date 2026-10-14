<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegisterOtpMail;
use App\Models\User;
use App\Support\MailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    /**
     * Step 1: Send OTP to email
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email:rfc,filter', 'max:255'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('email'),
            ], 422);
        }

        $email = strtolower(trim($request->email));

        // Ensure user is not already registered
        if (User::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'An account with this email address already exists. Please sign in instead.',
            ], 422);
        }

        $emailHash = md5($email);
        $cooldownKey = 'reg_otp_cooldown_' . $emailHash;

        if (cache()->has($cooldownKey)) {
            $expireTime = (int) cache()->get($cooldownKey);
            $remaining = $expireTime - time();
            if ($remaining > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$remaining} second(s) before requesting a new OTP.",
                    'remaining' => $remaining,
                ], 429);
            }
        }

        $otp = (string) random_int(100000, 999999);
        $cacheKey = 'reg_otp_' . $emailHash;

        cache()->put($cacheKey, [
            'hash' => Hash::make($otp),
            'plain' => app()->environment('local') ? $otp : null,
            'email' => $email,
            'attempts' => 0,
            'created_at' => now()->timestamp,
        ], now()->addMinutes(15));

        // Cooldown for 45 seconds
        cache()->put($cooldownKey, time() + 45, 45);

        // Apply dynamic SMTP settings from database/admin if configured
        MailConfig::apply();

        try {
            Mail::to($email)->send(new RegisterOtpMail($otp, $email));
        } catch (\Throwable $e) {
            Log::error('Registration OTP email sending failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP email: ' . $e->getMessage() . '. Please verify your SMTP settings in Admin.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Verification code has been sent to {$email}.",
            'cooldown' => 45,
            'debug_otp' => app()->environment('local') ? $otp : null,
        ]);
    }

    /**
     * Step 2: Verify 6-digit OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email:rfc,filter'],
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Please enter the 6-digit verification code.',
            'otp.size' => 'The verification code must be exactly 6 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = strtolower(trim($request->email));
        $emailHash = md5($email);
        $cacheKey = 'reg_otp_' . $emailHash;

        $record = cache()->get($cacheKey);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'No OTP request found for this email or it has expired. Please request a new code.',
            ], 422);
        }

        if (($record['attempts'] ?? 0) >= 5) {
            cache()->forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'You have exceeded the maximum of 5 attempts. Please request a new OTP.',
            ], 429);
        }

        $isValid = Hash::check($request->otp, $record['hash'])
            || (isset($record['plain']) && $record['plain'] === $request->otp);

        if (!$isValid) {
            $record['attempts'] = ($record['attempts'] ?? 0) + 1;
            cache()->put($cacheKey, $record, now()->addMinutes(15));
            $attemptsLeft = max(0, 5 - $record['attempts']);

            return response()->json([
                'success' => false,
                'message' => "The verification code is incorrect. {$attemptsLeft} attempt(s) remaining.",
            ], 422);
        }

        // Email successfully verified: save flag in session
        session([
            'reg_verified_email' => $email,
            'reg_verified_at' => now()->timestamp,
        ]);

        cache()->forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully!',
        ]);
    }

    /**
     * Step 3: Complete Registration
     */
    public function register(Request $request)
    {
        $email = strtolower(trim($request->email));
        $verifiedEmail = session('reg_verified_email');
        $verifiedAt = session('reg_verified_at');

        // Security check: Must have verified email with OTP within 60 minutes
        if (!$verifiedEmail || $verifiedEmail !== $email || !$verifiedAt || (now()->timestamp - $verifiedAt > 3600)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email address with OTP first before submitting.',
                ], 422);
            }

            return back()->withErrors([
                'email' => 'Please verify your email address with OTP first.',
            ])->withInput();
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,filter', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Please provide your full name.',
            'phone.regex' => 'Phone number must be a valid 11-digit Bangladeshi mobile number (e.g. 01700000000).',
            'password.required' => 'Please enter a password.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => trim($request->name),
            'email' => $email,
            'phone' => $request->phone ? trim($request->phone) : null,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        // Clear verification session
        session()->forget(['reg_verified_email', 'reg_verified_at']);

        Auth::login($user);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully!',
                'redirect' => route('user.dashboard'),
            ]);
        }

        return redirect()->route('user.dashboard')->with('status', 'Account created successfully! Welcome to Raimart.');
    }
}
