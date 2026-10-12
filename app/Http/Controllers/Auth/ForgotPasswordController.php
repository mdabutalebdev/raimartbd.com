<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use App\Support\MailConfig;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    /** Step 1: Show Forgot Password Email Form */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /** Step 1 Action: Generate OTP and send email */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));
        $emailKey = md5($email);
        $lockoutKey = 'otp_lockout_' . $emailKey;

        // Check 30-minute lockout
        if ($lockout = cache()->get($lockoutKey)) {
            $diffSeconds = Carbon::now()->diffInSeconds($lockout, false);
            if ($diffSeconds > 0) {
                $minutes = (int) ceil($diffSeconds / 60);
                return back()->withErrors([
                    'email' => "Too many attempts. Please wait {$minutes} minute(s) before requesting an OTP code again.",
                ])->withInput();
            }
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'We could not find an account with that email address.',
            ])->withInput();
        }

        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        // Apply dynamic SMTP settings from Admin if configured
        MailConfig::apply();

        try {
            Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name));
        } catch (\Throwable $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());

            return back()->withErrors([
                'email' => 'Failed to send OTP email: ' . $e->getMessage() . '. Please verify your SMTP settings in Admin.',
            ])->withInput();
        }

        // Save email in session & clear previous verification
        session([
            'password_reset_email' => $user->email,
        ]);
        session()->forget('password_reset_verified');

        // Set initial resend count to 0 if not already tracking
        cache()->put('otp_resend_count_' . $emailKey, 0, now()->addMinutes(35));

        return redirect()->route('password.verify.show');
    }

    /** Step 2: Show Dedicated OTP Verification Form */
    public function showVerifyOtpForm(Request $request)
    {
        $email = session('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Please enter your email address first.',
            ]);
        }

        $emailKey = md5(strtolower(trim($email)));
        $lockoutKey = 'otp_lockout_' . $emailKey;
        $counterKey = 'otp_resend_count_' . $emailKey;

        $isLocked = false;
        $lockoutMinutes = 0;
        if ($lockout = cache()->get($lockoutKey)) {
            $diffSeconds = Carbon::now()->diffInSeconds($lockout, false);
            if ($diffSeconds > 0) {
                $isLocked = true;
                $lockoutMinutes = (int) ceil($diffSeconds / 60);
            }
        }

        $attempts = (int) cache()->get($counterKey, 0);
        $resendsLeft = max(0, 5 - $attempts);

        return view('auth.verify-otp', compact('email', 'isLocked', 'lockoutMinutes', 'resendsLeft'));
    }

    /** Step 2 Action: Verify 6-digit OTP */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record) {
            return back()->withErrors([
                'otp' => 'No OTP request found for this email. Please request a new code.',
            ]);
        }

        // Check if OTP is expired (15 minutes limit)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors([
                'otp' => 'This OTP code has expired. Please request a new code.',
            ]);
        }

        // Verify OTP match
        if (! Hash::check($request->otp, $record->token) && $record->token !== $request->otp) {
            return back()->withErrors([
                'otp' => 'The verification code you entered is incorrect.',
            ]);
        }

        // Mark as verified in session
        session([
            'password_reset_verified' => true,
            'password_reset_email' => $request->email,
        ]);

        return redirect()->route('password.reset');
    }

    /** Step 3: Show Dedicated New Password Form */
    public function showResetForm(Request $request)
    {
        $email = session('password_reset_email');
        $verified = session('password_reset_verified');

        if (! $email || ! $verified) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Please verify your OTP code first.',
            ]);
        }

        return view('auth.reset-password', compact('email'));
    }

    /** Step 3 Action: Set New Password */
    public function resetPassword(Request $request)
    {
        $email = session('password_reset_email');
        $verified = session('password_reset_verified');

        if (! $email || ! $verified) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Session expired. Please request a new OTP.',
            ]);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'User not found.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clean up reset token, session, and resend limits
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['password_reset_email', 'password_reset_verified']);

        $emailKey = md5(strtolower(trim($email)));
        cache()->forget('otp_resend_count_' . $emailKey);
        cache()->forget('otp_lockout_' . $emailKey);

        return redirect()->route('login')
            ->with('status', 'Password reset successfully!')
            ->with('success', 'Password reset successfully!');
    }

    /** Resend OTP with strict 5-attempt limit & 30-minute lockout */
    public function resendOtp(Request $request)
    {
        $email = session('password_reset_email', $request->email);

        if (! $email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Please enter your email address first.',
            ]);
        }

        $emailKey = md5(strtolower(trim($email)));
        $lockoutKey = 'otp_lockout_' . $emailKey;
        $counterKey = 'otp_resend_count_' . $emailKey;

        // Check if 30-minute lockout is active
        if ($lockout = cache()->get($lockoutKey)) {
            $diffSeconds = Carbon::now()->diffInSeconds($lockout, false);
            if ($diffSeconds > 0) {
                $minutes = (int) ceil($diffSeconds / 60);
                return back()->withErrors([
                    'otp' => "You have reached the maximum of 5 resend attempts. Please wait {$minutes} minute(s) before trying again.",
                ]);
            }
        }

        // Check attempts
        $attempts = (int) cache()->get($counterKey, 0) + 1;

        if ($attempts >= 5) {
            // Lock out for 30 minutes
            cache()->put($lockoutKey, Carbon::now()->addMinutes(30), Carbon::now()->addMinutes(30));
            cache()->forget($counterKey);

            return back()->withErrors([
                'otp' => 'You have reached the maximum limit of 5 resends. Please wait 30 minutes before trying again.',
            ]);
        }

        cache()->put($counterKey, $attempts, Carbon::now()->addMinutes(35));

        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'We could not find an account with that email address.',
            ]);
        }

        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        MailConfig::apply();

        try {
            Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name));
        } catch (\Throwable $e) {
            Log::error('Password reset email resend failed: ' . $e->getMessage());

            return back()->withErrors([
                'otp' => 'Failed to send email: ' . $e->getMessage(),
            ]);
        }

        return redirect()->route('password.verify.show');
    }
}
