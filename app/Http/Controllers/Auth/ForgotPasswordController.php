<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset link email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'We cannot find a user with that email address.'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->security_question) {
            return back()->withErrors(['email' => 'Security question not set up for this account. Please contact administrator.']);
        }

        // Store email in session for security question step
        session(['password_reset_email' => $request->email]);

        return redirect()->route('password.verify.form', ['email' => $request->email]);
    }

    /**
     * Show security question verification form.
     */
    public function showSecurityQuestionForm($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !$user->security_question) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Security question not set up for this account. Please contact administrator.']);
        }

        return view('auth.verify-security-question', [
            'securityQuestion' => $user->security_question,
            'email' => $email
        ]);
    }

    /**
     * Verify security answer.
     */
    public function verifySecurityAnswer(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'security_answer' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if (!$user->security_question || !$user->security_answer) {
            return back()->withErrors(['security_answer' => 'Security question not set up for this account.']);
        }

        // Verify security answer using the method in User model
        if (!$user->verifySecurityAnswer($request->security_answer)) {
            return back()->withErrors(['security_answer' => 'Incorrect answer. Please try again.']);
        }

        // Generate password reset token
        $token = Str::random(64);
        // Store token in session
        session([
            'password_reset_token' => $token, 
            'password_reset_email' => $request->email
        ]);

        return redirect()->route('password.reset.form', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Show reset password form.
     */
    public function showResetForm($token, $email)
    {
        // Verify token from session
        if (session('password_reset_token') !== $token) {
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Invalid or expired token.']);
        }

        if (session('password_reset_email') !== $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Invalid email address.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verify token from session
        if (session('password_reset_token') !== $request->token) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        if (session('password_reset_email') !== $request->email) {
            return back()->withErrors(['email' => 'Invalid email address.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Update password
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        // Clear session
        session()->forget(['password_reset_token', 'password_reset_email']);

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }
}
    