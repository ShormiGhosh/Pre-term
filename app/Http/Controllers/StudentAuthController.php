<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * StudentAuthController
 * Handles student authentication: signup, login, logout, email verification, password reset
 */
class StudentAuthController extends Controller
{
    /**
     * Show student signup form
     */
    public function showSignupForm()
    {
        return view('student.signup');
    }

    /**
     * Handle student signup
     * Validates email format: surnameRoll@stud.kuet.ac.bd
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
                'unique:students',
                'regex:/^[a-zA-Z0-9]+@stud\.kuet\.ac\.bd$/' // Enforce KUET student email format
            ],
            'password' => 'required|string|min:6|confirmed', // confirmed checks for password_confirmation field
            'roll_number' => 'required|string|unique:students|max:20',
            'department' => 'required|string|max:100',
            'year' => 'required|integer|min:1|max:5',
            'semester' => 'required|string|max:10',
        ], [
            'email.regex' => 'Email must be in format: surnameRoll@stud.kuet.ac.bd'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create new student record (email not verified yet)
        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Automatically hashed by model
            'roll_number' => $request->roll_number,
            'department' => $request->department,
            'year' => $request->year,
            'semester' => $request->semester,
            'email_verified' => false, // Email not verified yet
        ]);

        // Generate verification code
        $code = VerificationCode::generateCode();
        
        // Store verification code in database
        VerificationCode::create([
            'email' => $student->email,
            'code' => $code,
            'type' => 'signup',
            'expires_at' => now()->addMinutes(15), // Code expires in 15 minutes
        ]);

        // Send verification email
        EmailHelper::sendVerificationEmail($student->email, $code, $student->name, 'signup');

        // Store temporary data in session for verification page
        session([
            'pending_verification_email' => $student->email,
            'pending_verification_name' => $student->name,
        ]);

        return redirect()->route('student.verify.show')->with('success', 'Account created! Please check your email for verification code.')
               ->with('verification_code', $code); // For development/testing
    }

    /**
     * Show student login form
     */
    public function showLoginForm()
    {
        return view('student.login');
    }

    /**
     * Handle student login
     */
    public function login(Request $request)
    {
        // Validate credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find student by email
        $student = Student::where('email', $credentials['email'])->first();

        // Check if student exists and password matches
        if ($student && Hash::check($credentials['password'], $student->password)) {
            // Store student info in session
            session([
                'user_id' => $student->id,
                'user_type' => 'student',
                'user_name' => $student->name,
                'user_email' => $student->email,
            ]);

            return redirect()->route('student.dashboard')->with('success', 'Login successful!');
        }

        // Authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Handle student logout
     */
    public function logout(Request $request)
    {
        // Clear all session data
        $request->session()->flush();

        return redirect()->route('student.login')->with('success', 'Logged out successfully!');
    }

    /**
     * Show student dashboard
     */
    public function dashboard()
    {
        return view('student.dashboard');
    }

    /**
     * Show email verification form
     */
    public function showVerifyForm()
    {
        if (!session()->has('pending_verification_email')) {
            return redirect()->route('student.login');
        }
        return view('student.verify');
    }

    /**
     * Verify email with code
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $email = session('pending_verification_email');
        
        if (!$email) {
            return redirect()->route('student.login')->with('error', 'Session expired. Please try again.');
        }

        // Find valid verification code
        $verification = VerificationCode::where('email', $email)
            ->where('code', $request->code)
            ->where('type', 'signup')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return back()->with('error', 'Invalid or expired verification code.');
        }

        // Mark code as used
        $verification->update(['is_used' => true]);

        // Update student email_verified status
        $student = Student::where('email', $email)->first();
        $student->update(['email_verified' => true]);

        // Clear pending verification session
        session()->forget(['pending_verification_email', 'pending_verification_name']);

        // Log in the student
        session([
            'user_id' => $student->id,
            'user_type' => 'student',
            'user_name' => $student->name,
            'user_email' => $student->email,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Email verified successfully! Welcome!');
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode()
    {
        $email = session('pending_verification_email');
        $name = session('pending_verification_name');
        
        if (!$email) {
            return redirect()->route('student.login')->with('error', 'Session expired.');
        }

        // Generate new code
        $code = VerificationCode::generateCode();
        
        VerificationCode::create([
            'email' => $email,
            'code' => $code,
            'type' => 'signup',
            'expires_at' => now()->addMinutes(15),
        ]);

        // Send email
        EmailHelper::sendVerificationEmail($email, $code, $name, 'signup');

        return back()->with('success', 'Verification code resent!')
               ->with('verification_code', $code);
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('student.forgot-password');
    }

    /**
     * Send password reset code
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:students,email',
        ]);

        $student = Student::where('email', $request->email)->first();

        // Generate reset code
        $code = VerificationCode::generateCode();
        
        VerificationCode::create([
            'email' => $student->email,
            'code' => $code,
            'type' => 'reset',
            'expires_at' => now()->addMinutes(15),
        ]);

        // Send email
        EmailHelper::sendVerificationEmail($student->email, $code, $student->name, 'reset');

        session(['reset_email' => $student->email]);

        return redirect()->route('student.reset.verify')->with('success', 'Reset code sent to your email!')
               ->with('verification_code', $code);
    }

    /**
     * Show reset code verification form
     */
    public function showResetVerifyForm()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('student.forgot-password');
        }
        return view('student.reset-verify');
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
            return redirect()->route('student.login')->with('error', 'Session expired.');
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

        return redirect()->route('student.reset.password')->with('success', 'Code verified! Set your new password.');
    }

    /**
     * Show new password form
     */
    public function showResetPasswordForm()
    {
        if (!session()->has('verified_reset_email')) {
            return redirect()->route('student.forgot-password');
        }
        return view('student.reset-password');
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
            return redirect()->route('student.login')->with('error', 'Session expired.');
        }

        // Update password and mark email as verified
        // (If they can reset password, they have access to their email, so it's verified)
        $student = Student::where('email', $email)->first();
        $student->update([
            'password' => $request->password,
            'email_verified' => true,
        ]);

        session()->forget('verified_reset_email');

        return redirect()->route('student.login')->with('success', 'Password reset successfully! Please login.');
    }
}
