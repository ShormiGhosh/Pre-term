<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via student guard
        if (!Auth::guard('student')->check()) {
            return redirect()->route('student.login')->with('error', 'Please login as student to access this page.');
        }

        // User is authenticated as student, allow request to proceed
        return $next($request);
    }
}
