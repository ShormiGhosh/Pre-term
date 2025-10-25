<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user instanceof \App\Models\Teacher) {
            // Get courses created by this teacher
            $courses = Course::where('teacher_id', $user->id)->get();
        } else {
            // Get courses enrolled by this student
            $courses = $user->courses;
        }
        
        return view('courses.index', compact('courses'));
    }

    /**
     * Show course details page
     */
    public function show($id)
    {
        $course = Course::with(['teacher', 'students', 'ctSchedules' => function($query) {
            $query->orderBy('ct_datetime', 'asc');
        }])->findOrFail($id);
        
        // Check if user has access to this course
        $user = Auth::guard('teacher')->check() ? Auth::guard('teacher')->user() : Auth::guard('student')->user();
        
        // Teachers can only view their own courses
        if ($user instanceof \App\Models\Teacher && $course->teacher_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        // Students can only view enrolled courses
        if ($user instanceof \App\Models\Student) {
            $isEnrolled = CourseEnrollment::where('student_id', $user->id)
                                          ->where('course_id', $course->id)
                                          ->exists();
            if (!$isEnrolled) {
                return redirect()->back()->with('error', 'You are not enrolled in this course.');
            }
        }
        
        return view('courses.show', compact('course', 'user'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string|max:20',
            'course_title' => 'required|string|max:255',
            'course_credit' => 'required|numeric|min:0|max:10',
            'department' => 'nullable|string|max:10',
        ]);

        $user = Auth::guard('teacher')->user();

        if (!$user) {
            return back()->with('error', 'Only teachers can create courses.');
        }

        Course::create([
            'course_code' => $request->course_code,
            'course_title' => $request->course_title,
            'course_credit' => $request->course_credit,
            'department' => $request->department ?? substr($request->course_code, 0, strpos($request->course_code, ' ')),
            'teacher_id' => $user->id,
        ]);

        return back()->with('success', 'Course created successfully!');
    }

    /**
     * Remove the specified course
     */
    public function destroy($id)
    {
        $user = Auth::guard('teacher')->user();
        $course = Course::findOrFail($id);

        if (!$user || $course->teacher_id !== $user->id) {
            return back()->with('error', 'You do not have permission to delete this course.');
        }

        $course->delete();

        return back()->with('success', 'Course deleted successfully!');
    }

    /**
     * Enroll student in a course
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $user = Auth::guard('student')->user();

        if (!$user) {
            return back()->with('error', 'Only students can enroll in courses.');
        }

        $enrolledCount = 0;
        $alreadyEnrolledCount = 0;

        foreach ($request->course_ids as $courseId) {
            // Check if already enrolled
            if (CourseEnrollment::where('student_id', $user->id)
                ->where('course_id', $courseId)
                ->exists()) {
                $alreadyEnrolledCount++;
                continue;
            }

            CourseEnrollment::create([
                'student_id' => $user->id,
                'course_id' => $courseId,
            ]);
            $enrolledCount++;
        }

        if ($enrolledCount > 0 && $alreadyEnrolledCount > 0) {
            return back()->with('success', "Successfully enrolled in {$enrolledCount} course(s). {$alreadyEnrolledCount} course(s) were already enrolled.");
        } elseif ($enrolledCount > 0) {
            return back()->with('success', "Successfully enrolled in {$enrolledCount} course(s)!");
        } else {
            return back()->with('error', 'You are already enrolled in the selected course(s).');
        }
    }

    /**
     * Unenroll student from a course
     */
    public function unenroll($id)
    {
        $user = Auth::guard('student')->user();
        $course = Course::findOrFail($id);

        if (!$user) {
            return back()->with('error', 'Only students can unenroll from courses.');
        }

        $enrollment = CourseEnrollment::where('student_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'You are not enrolled in this course.');
        }

        $enrollment->delete();

        return back()->with('success', 'Successfully unenrolled from course!');
    }
}
