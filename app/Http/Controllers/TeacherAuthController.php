<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * TeacherAuthController
 * Handles teacher authentication: signup, login, logout
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
}
