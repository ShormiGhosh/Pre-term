<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\EmailHelper;
use Illuminate\Support\Facades\Mail;

/**
 * Home Route
 * Landing page with options to login as teacher or student
 */
Route::get('/', function () {
    return view('home');
})->name('home');

/**
 * Test Email Route (Remove after testing)
 */
Route::get('/test-email', function () {
    try {
        $testEmail = 'test@stud.kuet.ac.bd'; // Change this to your actual email
        $code = '123456';
        $name = 'Test User';
        
        EmailHelper::sendVerificationEmail($testEmail, $code, $name, 'signup');
        
        return 'Email sent successfully! Check your inbox at: ' . $testEmail;
    } catch (\Exception $e) {
        return 'Email failed: ' . $e->getMessage();
    }
});

/**
 * Teacher Routes
 * Handles teacher authentication, password reset, and dashboard
 */
// Public routes (guest only)
Route::get('/teacher/signup', [TeacherAuthController::class, 'showSignupForm'])->name('teacher.signup');
Route::post('/teacher/signup', [TeacherAuthController::class, 'signup'])->name('teacher.signup.submit');
Route::get('/teacher/login', [TeacherAuthController::class, 'showLoginForm'])->name('teacher.login');
Route::post('/teacher/login', [TeacherAuthController::class, 'login'])->name('teacher.login.submit');

// Password reset routes
Route::get('/teacher/forgot-password', [TeacherAuthController::class, 'showForgotPasswordForm'])->name('teacher.forgot-password');
Route::post('/teacher/forgot-password', [TeacherAuthController::class, 'sendResetCode'])->name('teacher.reset.send');
Route::get('/teacher/reset-verify', [TeacherAuthController::class, 'showResetVerifyForm'])->name('teacher.reset.verify');
Route::post('/teacher/reset-verify', [TeacherAuthController::class, 'verifyResetCode'])->name('teacher.reset.verify.submit');
Route::get('/teacher/reset-password', [TeacherAuthController::class, 'showResetPasswordForm'])->name('teacher.reset.password');
Route::post('/teacher/reset-password', [TeacherAuthController::class, 'resetPassword'])->name('teacher.reset.password.submit');

// Protected routes (teacher auth required)
Route::middleware(['teacher.auth'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherAuthController::class, 'dashboard'])->name('teacher.dashboard');
    Route::post('/teacher/logout', [TeacherAuthController::class, 'logout'])->name('teacher.logout');
});

/**
 * Student Routes
 * Handles student authentication, email verification, password reset, and dashboard
 */
// Public routes (guest only)
Route::get('/student/signup', [StudentAuthController::class, 'showSignupForm'])->name('student.signup');
Route::post('/student/signup', [StudentAuthController::class, 'signup'])->name('student.signup.submit');
Route::get('/student/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentAuthController::class, 'login'])->name('student.login.submit');

// Email verification routes
Route::get('/student/verify', [StudentAuthController::class, 'showVerifyForm'])->name('student.verify.show');
Route::post('/student/verify', [StudentAuthController::class, 'verifyEmail'])->name('student.verify.submit');
Route::post('/student/verify/resend', [StudentAuthController::class, 'resendVerificationCode'])->name('student.verify.resend');

// Password reset routes
Route::get('/student/forgot-password', [StudentAuthController::class, 'showForgotPasswordForm'])->name('student.forgot-password');
Route::post('/student/forgot-password', [StudentAuthController::class, 'sendResetCode'])->name('student.reset.send');
Route::get('/student/reset-verify', [StudentAuthController::class, 'showResetVerifyForm'])->name('student.reset.verify');
Route::post('/student/reset-verify', [StudentAuthController::class, 'verifyResetCode'])->name('student.reset.verify.submit');
Route::get('/student/reset-password', [StudentAuthController::class, 'showResetPasswordForm'])->name('student.reset.password');
Route::post('/student/reset-password', [StudentAuthController::class, 'resetPassword'])->name('student.reset.password.submit');

// Protected routes (student auth required)
Route::middleware(['student.auth'])->group(function () {
    Route::get('/student/dashboard', [StudentAuthController::class, 'dashboard'])->name('student.dashboard');
    Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
});
