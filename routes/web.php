<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\StudentAuthController;

/**
 * Home Route
 * Landing page with options to login as teacher or student
 */
Route::get('/', function () {
    return view('home');
})->name('home');

/**
 * Teacher Routes
 * Handles teacher authentication and dashboard
 */
// Public routes (guest only)
Route::get('/teacher/signup', [TeacherAuthController::class, 'showSignupForm'])->name('teacher.signup');
Route::post('/teacher/signup', [TeacherAuthController::class, 'signup'])->name('teacher.signup.submit');
Route::get('/teacher/login', [TeacherAuthController::class, 'showLoginForm'])->name('teacher.login');
Route::post('/teacher/login', [TeacherAuthController::class, 'login'])->name('teacher.login.submit');

// Protected routes (teacher auth required)
Route::middleware(['teacher.auth'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherAuthController::class, 'dashboard'])->name('teacher.dashboard');
    Route::post('/teacher/logout', [TeacherAuthController::class, 'logout'])->name('teacher.logout');
});

/**
 * Student Routes
 * Handles student authentication and dashboard
 */
// Public routes (guest only)
Route::get('/student/signup', [StudentAuthController::class, 'showSignupForm'])->name('student.signup');
Route::post('/student/signup', [StudentAuthController::class, 'signup'])->name('student.signup.submit');
Route::get('/student/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentAuthController::class, 'login'])->name('student.login.submit');

// Protected routes (student auth required)
Route::middleware(['student.auth'])->group(function () {
    Route::get('/student/dashboard', [StudentAuthController::class, 'dashboard'])->name('student.dashboard');
    Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
});
