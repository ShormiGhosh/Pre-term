<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * StudentAuth Middleware
 * Protects routes that should only be accessible by authenticated students
 * Checks if user is logged in as a student via session
 */
class StudentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in and is a student
        if (!session()->has('user_id') || session('user_type') !== 'student') {
            // Not authenticated as student, redirect to student login
            return redirect()->route('student.login')->with('error', 'Please login as student to access this page.');
        }

        // User is authenticated as student, allow request to proceed
        return $next($request);
    }
}
