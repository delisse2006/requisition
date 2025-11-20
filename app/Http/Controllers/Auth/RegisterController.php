<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration form submission.
     */
    public function register(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:employee,accountant,admin',
        ]);

        // Create user (catch DB errors and show a friendly message)
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);
        } catch (QueryException $ex) {
            // Log detailed DB error for debugging
            Log::error('Registration DB error: ' . $ex->getMessage(), ['exception' => $ex]);

            // Return back with a user-friendly message and the DB error short text
            return back()->withInput()->withErrors(['database' => 'Unable to create account: ' . $ex->getMessage()]);
        }

        // Auto-login user
        auth()->login($user);

        // Redirect to dashboard
        return redirect()->route('dashboard')->with('success', 'Registration successful!');
    }
}
