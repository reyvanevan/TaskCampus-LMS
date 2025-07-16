<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Show the enrollment form.
     */
    public function create(): View
    {
        // Only students can enroll in courses
        if (!Auth::user()->isStudent()) {
            abort(403, 'Only students can enroll in courses.');
        }
        
        return view('enrollments.create');
    }

    /**
     * Enroll student in a course using enrollment code.
     */
    public function store(Request $request): RedirectResponse
    {
        // Only students can enroll in courses
        if (!Auth::user()->isStudent()) {
            abort(403, 'Only students can enroll in courses.');
        }
        
        $validatedData = $request->validate([
            'enrollment_code' => 'required|string',
        ]);
        
        // Find course with the enrollment code
        $course = Course::where('enrollment_code', $validatedData['enrollment_code'])->first();
        
        // Check if course exists
        if (!$course) {
            return back()->with('error', 'Invalid enrollment code. Please check and try again.')->withInput();
        }
        
        // Check if course is active
        if ($course->status !== 'active') {
            return back()->with('error', 'This course is not currently accepting enrollments.')->withInput();
        }
        
        // Check if student is already enrolled
        if (Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.enrollments.index')
                ->with('info', 'You are already enrolled in this course: ' . $course->name);
        }
        
        // Create enrollment
        Enrollment::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        
        return redirect()->route('student.enrollments.index')
            ->with('success', 'You have successfully enrolled in the course: ' . $course->name);
    }
    
    /**
     * Display the student's enrolled courses.
     */
    public function index(): View
    {
        // Only students have enrollments to view
        if (!Auth::user()->isStudent()) {
            abort(403, 'Only students can view their enrollments.');
        }
        
        $enrollments = Auth::user()->enrollments()
                            ->with(['course', 'course.lecturer', 'course.semester'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(9);
                            
        return view('courses.student-courses', compact('enrollments'));
    }
    
    /**
     * Update enrollment status.
     */
    public function update(Request $request, Enrollment $enrollment): RedirectResponse
    {
        // Check permissions: only the enrolled student, course lecturer, or admin can update
        if (Auth::user()->isStudent() && $enrollment->user_id !== Auth::id()) {
            abort(403, 'You are not enrolled in this course.');
        }
        
        if (Auth::user()->isLecturer() && $enrollment->course->lecturer_id !== Auth::id()) {
            abort(403, 'You are not the lecturer for this course.');
        }
        
        $validatedData = $request->validate([
            'status' => 'required|in:active,inactive,completed',
        ]);
        
        $enrollment->update($validatedData);
        
        return redirect()->back()
            ->with('success', 'Enrollment status updated successfully.');
    }
    
    /**
     * Remove student from a course.
     */
    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        // Check permissions: only the enrolled student, course lecturer, or admin can unenroll
        if (Auth::user()->isStudent() && $enrollment->user_id !== Auth::id()) {
            abort(403, 'You are not enrolled in this course.');
        }
        
        if (Auth::user()->isLecturer() && $enrollment->course->lecturer_id !== Auth::id()) {
            abort(403, 'You are not the lecturer for this course.');
        }
        
        $enrollment->delete();
        
        return redirect()->back()
            ->with('success', 'Enrollment has been removed successfully.');
    }
}