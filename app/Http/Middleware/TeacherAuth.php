<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * TeacherAuth Middleware
 * Protects routes that should only be accessible by authenticated teachers
 * Checks if user is logged in as a teacher via session
 */
class TeacherAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in and is a teacher
        if (!session()->has('user_id') || session('user_type') !== 'teacher') {
            // Not authenticated as teacher, redirect to teacher login
            return redirect()->route('teacher.login')->with('error', 'Please login as teacher to access this page.');
        }

        // User is authenticated as teacher, allow request to proceed
        return $next($request);
    }
}
