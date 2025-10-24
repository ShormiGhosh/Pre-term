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

        // Attempt to log in using the teacher guard
        if (Auth::guard('teacher')->attempt($credentials)) {
            $request->session()->regenerate();
            
            $teacher = Auth::guard('teacher')->user();
            
            // Store additional info in session for backward compatibility
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
        ])->withInput($request->only('email'));
    }

    /**
     * Handle teacher logout
     */
    public function logout(Request $request)
    {
        Auth::guard('teacher')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

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

    /**
     * Show teacher profile page
     */
    public function showProfile()
    {
        $teacher = Teacher::find(session('user_id'));
        return view('teacher.profile', compact('teacher'));
    }

    /**
     * Show edit profile form
     */
    public function showEditProfile()
    {
        $teacher = Teacher::find(session('user_id'));
        return view('teacher.edit-profile', compact('teacher'));
    }

    /**
     * Update teacher profile
     */
    public function updateProfile(Request $request)
    {
        $teacher = Teacher::find(session('user_id'));

        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($teacher->profile_image && file_exists(public_path('uploads/profiles/' . $teacher->profile_image))) {
                unlink(public_path('uploads/profiles/' . $teacher->profile_image));
            }

            $image = $request->file('profile_image');
            $imageName = time() . '_' . $teacher->id . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/profiles'), $imageName);
            $teacher->profile_image = $imageName;
        }

        $teacher->update([
            'name' => $request->name,
            'department' => $request->department,
            'designation' => $request->designation,
        ]);

        // Update session data
        session(['user_name' => $teacher->name]);

        return redirect()->route('teacher.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete teacher account
     */
    public function deleteAccount()
    {
        $teacher = Teacher::find(session('user_id'));

        // Delete profile image if exists
        if ($teacher->profile_image && file_exists(public_path('uploads/profiles/' . $teacher->profile_image))) {
            unlink(public_path('uploads/profiles/' . $teacher->profile_image));
        }

        $teacher->delete();
        session()->flush();

        return redirect()->route('home')->with('success', 'Your account has been deleted successfully.');
    }
}
