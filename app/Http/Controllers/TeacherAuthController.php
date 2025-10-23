<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * TeacherAuthController
 * Handles teacher authentication: signup, login, logout, password reset
 */
class TeacherAuthController extends Controller
{
    /**
     * Show teacher signup form
     */
    public function showSignupForm()
    {
        return view('teacher.signup');
    }

    /**
     * Handle teacher signup
     * Validates email format: teacher_first_name@dept.kuet.ac.bd
     */
    public function signup(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'unique:teachers',
                'regex:/^[a-zA-Z]+@[a-zA-Z]+\.kuet\.ac\.bd$/' // Enforce KUET teacher email format (no underscore)
            ],
            'password' => 'required|string|min:6|confirmed', // confirmed checks for password_confirmation field
            'department' => 'required|string|max:100',
            'designation' => 'nullable|string|max:100',
        ], [
            'email.regex' => 'Email must be in format: teachername@dept.kuet.ac.bd'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create new teacher record
        $teacher = Teacher::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Automatically hashed by model
            'department' => $request->department,
            'designation' => $request->designation,
        ]);

        // Store teacher info in session
        session([
            'user_id' => $teacher->id,
            'user_type' => 'teacher',
            'user_name' => $teacher->name,
            'user_email' => $teacher->email,
        ]);

        return redirect()->route('teacher.dashboard')->with('success', 'Teacher account created successfully!');
    }

    /**
     * Show teacher login form
     */
    public function showLoginForm()
    {
        return view('teacher.login');
    }

    /**
     * Handle teacher login
     */
    public function login(Request $request)
    {
        // Validate credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find teacher by email
        $teacher = Teacher::where('email', $credentials['email'])->first();

        // Check if teacher exists and password matches
        if ($teacher && Hash::check($credentials['password'], $teacher->password)) {
            // Store teacher info in session
            session([
                'user_id' => $teacher->id,
                'user_type' => 'teacher',
                'user_name' => $teacher->name,
                'user_email' => $teacher->email,
            ]);

            return redirect()->route('teacher.dashboard')->with('success', 'Login successful!');
        }

        // Authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Handle teacher logout
     */
    public function logout(Request $request)
    {
        // Clear all session data
        $request->session()->flush();

        return redirect()->route('teacher.login')->with('success', 'Logged out successfully!');
    }

    /**
     * Show teacher dashboard
     */
    public function dashboard()
    {
        return view('teacher.dashboard');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('teacher.forgot-password');
    }

    /**
     * Send password reset code
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:teachers,email',
        ]);

        $teacher = Teacher::where('email', $request->email)->first();

        // Generate reset code
        $code = VerificationCode::generateCode();
        
        VerificationCode::create([
            'email' => $teacher->email,
            'code' => $code,
            'type' => 'reset',
            'expires_at' => now()->addMinutes(15),
        ]);

        // Send email
        EmailHelper::sendVerificationEmail($teacher->email, $code, $teacher->name, 'reset');

        session(['reset_email' => $teacher->email]);

        return redirect()->route('teacher.reset.verify')->with('success', 'Reset code sent to your email!')
               ->with('verification_code', $code);
    }

    /**
     * Show reset code verification form
     */
    public function showResetVerifyForm()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('teacher.forgot-password');
        }
        return view('teacher.reset-verify');
    }

    /**
     * Verify reset code and show new password form
     */
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $email = session('reset_email');
        
        if (!$email) {
            return redirect()->route('teacher.login')->with('error', 'Session expired.');
        }

        // Find valid verification code
        $verification = VerificationCode::where('email', $email)
            ->where('code', $request->code)
            ->where('type', 'reset')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return back()->with('error', 'Invalid or expired code.');
        }

        // Mark as used
        $verification->update(['is_used' => true]);

        session(['verified_reset_email' => $email]);
        session()->forget('reset_email');

        return redirect()->route('teacher.reset.password')->with('success', 'Code verified! Set your new password.');
    }

    /**
     * Show new password form
     */
    public function showResetPasswordForm()
    {
        if (!session()->has('verified_reset_email')) {
            return redirect()->route('teacher.forgot-password');
        }
        return view('teacher.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = session('verified_reset_email');
        
        if (!$email) {
            return redirect()->route('teacher.login')->with('error', 'Session expired.');
        }

        // Update password
        $teacher = Teacher::where('email', $email)->first();
        $teacher->update(['password' => $request->password]);

        session()->forget('verified_reset_email');

        return redirect()->route('teacher.login')->with('success', 'Password reset successfully! Please login.');
    }
}
