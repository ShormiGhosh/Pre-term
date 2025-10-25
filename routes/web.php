<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EmailHelper;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('home');
})->name('home');

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
    Route::get('/teacher/profile', [TeacherAuthController::class, 'showProfile'])->name('teacher.profile');
    Route::get('/teacher/profile/edit', [TeacherAuthController::class, 'showEditProfile'])->name('teacher.profile.edit');
    Route::post('/teacher/profile/update', [TeacherAuthController::class, 'updateProfile'])->name('teacher.profile.update');
    Route::post('/teacher/profile/delete', [TeacherAuthController::class, 'deleteAccount'])->name('teacher.profile.delete');
    
    // Course management routes for teachers
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/teacher/courses/{id}', [CourseController::class, 'show'])->name('teacher.courses.show');
    
    // CT Schedule routes for teachers
    Route::post('/courses/{courseId}/ct-schedules', [\App\Http\Controllers\CTScheduleController::class, 'store'])->name('ct-schedules.store');
    Route::delete('/ct-schedules/{id}', [\App\Http\Controllers\CTScheduleController::class, 'destroy'])->name('ct-schedules.destroy');
    Route::post('/ct-schedules/{id}/mark-past', [\App\Http\Controllers\CTScheduleController::class, 'markAsPast'])->name('ct-schedules.mark-past');
    
    // CT Marks routes for teachers
    Route::post('/courses/{courseId}/ct-marks/save', [\App\Http\Controllers\CTScheduleController::class, 'saveMarks'])->name('ct-marks.save');
    Route::get('/courses/{courseId}/ct-marks/download', [\App\Http\Controllers\CTScheduleController::class, 'downloadMarks'])->name('ct-marks.download');
    
    // Attendance routes for teachers
    Route::post('/courses/{courseId}/attendance/generate-qr', [\App\Http\Controllers\AttendanceController::class, 'generateQR'])->name('attendance.generate-qr');
    Route::get('/attendance/session/{sessionId}/status', [\App\Http\Controllers\AttendanceController::class, 'getSessionStatus'])->name('attendance.session.status');
    Route::post('/attendance/session/{sessionId}/close', [\App\Http\Controllers\AttendanceController::class, 'closeSession'])->name('attendance.session.close');
    Route::get('/courses/{courseId}/attendance/sheet', [\App\Http\Controllers\AttendanceController::class, 'getAttendanceSheet'])->name('attendance.sheet');
    Route::post('/courses/{courseId}/attendance/calculate-marks', [\App\Http\Controllers\AttendanceController::class, 'calculateAttendanceMarks'])->name('attendance.calculate-marks');
    
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
    Route::get('/student/profile', [StudentAuthController::class, 'showProfile'])->name('student.profile');
    Route::get('/student/profile/edit', [StudentAuthController::class, 'showEditProfile'])->name('student.profile.edit');
    Route::post('/student/profile/update', [StudentAuthController::class, 'updateProfile'])->name('student.profile.update');
    Route::post('/student/profile/delete', [StudentAuthController::class, 'deleteAccount'])->name('student.profile.delete');
    
    // Course enrollment routes for students
    Route::post('/courses/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/courses/{id}/unenroll', [CourseController::class, 'unenroll'])->name('courses.unenroll');
    Route::get('/student/courses/{id}', [CourseController::class, 'show'])->name('student.courses.show');
    
    // Attendance routes for students
    Route::post('/attendance/mark', [\App\Http\Controllers\AttendanceController::class, 'markAttendance'])->name('attendance.mark');
    Route::get('/courses/{courseId}/attendance/data', [\App\Http\Controllers\AttendanceController::class, 'getStudentAttendance'])->name('attendance.student.data');
    
    // AJAX: Mark CT as past (auto-update when countdown expires)
    Route::post('/ct-schedules/{id}/mark-past', [\App\Http\Controllers\CTScheduleController::class, 'markAsPast'])->name('ct-schedules.mark-past');
    
    // Notification routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'delete'])->name('notifications.delete');
    
    Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
});

// Shared routes accessible by both teachers and students
Route::get('/courses/{courseId}/attendance/active', [\App\Http\Controllers\AttendanceController::class, 'getActiveSession'])
    ->middleware(['auth:teacher,student'])
    ->name('attendance.active');
