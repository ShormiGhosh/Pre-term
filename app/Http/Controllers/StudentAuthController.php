<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * StudentAuthController
 * Handles student authentication: signup, login, logout
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

        // Create new student record
        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Automatically hashed by model
            'roll_number' => $request->roll_number,
            'department' => $request->department,
            'year' => $request->year,
            'semester' => $request->semester,
        ]);

        // Store student info in session
        session([
            'user_id' => $student->id,
            'user_type' => 'student',
            'user_name' => $student->name,
            'user_email' => $student->email,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Student account created successfully!');
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
}
