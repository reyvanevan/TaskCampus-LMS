<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Semester;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Course::with(['lecturer', 'semester']);
        
        // Apply filters if provided
        if ($request->filled('semester')) {
            $query->where('semester_id', $request->semester);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Only show courses taught by the current lecturer if not admin
        if (Auth::user()->isLecturer()) {
            $query->where('lecturer_id', Auth::id());
        }
        
        $courses = $query->orderBy('created_at', 'desc')->paginate(10);
        $semesters = Semester::all();
        
        return view('courses.index', compact('courses', 'semesters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Only admin can assign lecturers to courses
        if (Auth::user()->isAdmin()) {
            $lecturers = User::where('role', 'lecturer')->get();
        } else {
            $lecturers = collect([Auth::user()]);
        }
        
        $semesters = Semester::all();
        
        return view('courses.create', compact('lecturers', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses',
            'description' => 'nullable|string',
            'lecturer_id' => 'required|exists:users,id',
            'semester_id' => 'required|exists:semesters,id',
            'status' => 'required|in:active,inactive,archived',
        ]);
        
        // Generate enrollment code
        $validatedData['enrollment_code'] = Str::upper(Str::random(8));
        
        // Check if the user is a lecturer and trying to assign someone else
        if (Auth::user()->isLecturer() && $validatedData['lecturer_id'] != Auth::id()) {
            return back()
                ->withInput()
                ->with('error', 'You can only create courses for yourself as a lecturer.');
        }
        
        Course::create($validatedData);
        
        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): View
    {
        // Check permission: only course lecturer, admin, or enrolled students can view
        if (Auth::user()->isStudent() && !$course->students()->where('users.id', Auth::id())->wherePivot('status', 'active')->exists()) {
            abort(403, 'You are not enrolled in this course.');
        }
        
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            abort(403, 'You are not the lecturer for this course.');
        }
        
        $course->load(['lecturer', 'semester']);
        
        // For students, we need to load their enrollment status
        $enrollment = null;
        if (Auth::user()->isStudent()) {
            $enrollment = $course->enrollments()
                              ->where('user_id', Auth::id())
                              ->first();
        }
        
        // Load enrolled students (for lecturers and admins)
        $enrolledStudents = [];
        if (Auth::user()->isLecturer() || Auth::user()->isAdmin()) {
            $enrolledStudents = $course->students()->paginate(20);
        }
        
        return view('courses.show', compact('course', 'enrollment', 'enrolledStudents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): View
    {
        // Check permission: only course lecturer or admin can edit
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            abort(403, 'You are not the lecturer for this course.');
        }
        
        // Only admin can reassign lecturers
        if (Auth::user()->isAdmin()) {
            $lecturers = User::where('role', 'lecturer')->get();
        } else {
            $lecturers = collect([Auth::user()]);
        }
        
        $semesters = Semester::all();
        
        return view('courses.edit', compact('course', 'lecturers', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        // Check permission: only course lecturer or admin can update
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            abort(403, 'You are not the lecturer for this course.');
        }
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'lecturer_id' => 'required|exists:users,id',
            'semester_id' => 'required|exists:semesters,id',
            'status' => 'required|in:active,inactive,archived',
        ]);
        
        // Check if the user is a lecturer and trying to reassign the course
        if (Auth::user()->isLecturer() && $validatedData['lecturer_id'] != Auth::id()) {
            return back()
                ->withInput()
                ->with('error', 'You cannot reassign the course to another lecturer.');
        }
        
        // Regenerate enrollment code if requested
        if ($request->has('regenerate_code')) {
            $validatedData['enrollment_code'] = Str::upper(Str::random(8));
        }
        
        $course->update($validatedData);
        
        return redirect()->route('courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Request $request): RedirectResponse
    {
        // Check permission: only course lecturer or admin can delete
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            abort(403, 'You are not the lecturer for this course.');
        }
        
        try {
            $courseName = $course->name;
            $enrollmentCount = $course->enrollments()->count();
            $assignmentCount = $course->assignments()->count();
            $totalIssues = $enrollmentCount + $assignmentCount;
            
            // Check if force delete is requested
            $forceDelete = $request->has('force') && $request->boolean('force');
            
            // If has data and not force delete, offer force delete option
            if ($totalIssues > 0 && !$forceDelete) {
                $forceDeleteUrl = route('courses.destroy', $course) . '?force=1';
                
                return back()
                    ->with('error', "Error! Cannot delete course '{$courseName}'. It has {$enrollmentCount} enrollments and {$assignmentCount} assignments.")
                    ->with('show_force_delete', true)
                    ->with('force_delete_data', [
                        'course_id' => $course->id,
                        'course_name' => $courseName,
                        'enrollment_count' => $enrollmentCount,
                        'assignment_count' => $assignmentCount,
                        'force_delete_url' => $forceDeleteUrl,
                    ]);
            }
            
            // Proceed with force deletion
            if ($forceDelete && $totalIssues > 0) {
                // Delete all assignments and their submissions
                foreach ($course->assignments as $assignment) {
                    // Delete all submissions for this assignment
                    foreach ($assignment->submissions as $submission) {
                        if ($submission->file_path) {
                            Storage::disk('public')->delete($submission->file_path);
                        }
                        $submission->criteriaScores()->delete();
                        $submission->delete();
                    }
                    
                    // Delete assignment file
                    if ($assignment->file_path) {
                        Storage::disk('public')->delete($assignment->file_path);
                    }
                    
                    // Delete rubric
                    if ($assignment->rubric) {
                        $assignment->rubric->delete();
                    }
                    
                    $assignment->delete();
                }
                
                // Delete all enrollments
                $course->enrollments()->delete();
            }
            
            $course->delete();
            
            $successMessage = $forceDelete 
                ? "Course '{$courseName}' and all related data ({$enrollmentCount} enrollments, {$assignmentCount} assignments) have been FORCE DELETED successfully!"
                : "Course '{$courseName}' deleted successfully.";
            
            return redirect()->route('courses.index')
                ->with('success', $successMessage);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting course: ' . $e->getMessage());
        }
    }
    
    /**
     * Regenerate enrollment code for the course.
     */
    public function regenerateCode(Course $course): RedirectResponse
    {
        // Check permission: only course lecturer or admin can regenerate code
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            abort(403, 'You are not the lecturer for this course.');
        }
        
        $course->enrollment_code = Str::upper(Str::random(8));
        $course->save();
        
        return redirect()->route('courses.show', $course)
            ->with('success', 'Enrollment code regenerated successfully.');
    }
    
    /**
     * Display the lecturer's course management dashboard.
     */
    public function lecturerDashboard(Request $request): View
    {
        // Only lecturers should access this dashboard
        if (!Auth::user()->isLecturer()) {
            abort(403, 'Only lecturers can access this dashboard.');
        }
        
        // Apply filters if provided
        $query = Course::where('lecturer_id', Auth::id());
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('semester')) {
            $query->where('semester_id', $request->semester);
        }
        
        // Get courses with student count
        $courses = $query->withCount('students')
                        ->with('semester')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        // Get all semesters for filter
        $semesters = Semester::all();
        
        // Get course count
        $courseCount = Course::where('lecturer_id', Auth::id())->count();
        
        // Count active assignments
        $activeAssignments = 0;
        try {
            $activeAssignments = Assignment::whereHas('course', function($q) {
                                $q->where('lecturer_id', Auth::id());
                            })
                            ->where('status', 'published')
                            ->count();
        } catch (QueryException $e) {
            $activeAssignments = 0;
        }
        
        // Count pending submissions
        $pendingSubmissions = 0;
        try {
            $pendingSubmissions = Submission::whereHas('assignment.course', function($q) {
                                $q->where('lecturer_id', Auth::id());
                            })
                            ->whereIn('status', ['submitted', 'late'])
                            ->count();
        } catch (QueryException $e) {
            $pendingSubmissions = 0;
        }
        
        // Get upcoming deadlines
        $upcomingDeadlines = collect([]);
        try {
            $upcomingDeadlines = Assignment::whereHas('course', function($q) {
                                $q->where('lecturer_id', Auth::id());
                            })
                            ->where('status', 'published')
                            ->where('deadline', '>', now())
                            ->orderBy('deadline', 'asc')
                            ->limit(5)
                            ->with('course')
                            ->get();
        } catch (QueryException $e) {
            // Keep empty collection if error
        }
        
        // Get recent submissions
        $recentSubmissions = collect([]);
        try {
            $recentSubmissions = Submission::whereHas('assignment.course', function($q) {
                                $q->where('lecturer_id', Auth::id());
                            })
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->with(['user', 'assignment'])
                            ->get();
        } catch (QueryException $e) {
            // Keep empty collection if error
        }
        
        return view('courses.lecturer-dashboard', compact(
            'courses', 'semesters', 'courseCount', 'activeAssignments',
            'pendingSubmissions', 'upcomingDeadlines', 'recentSubmissions'
        ));
    }
    
    /**
     * Display students enrolled in a specific course.
     */
    public function courseStudents(Request $request, Course $course): View
    {
        // Check permission: only course lecturer or admin can view
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            abort(403, 'You are not the lecturer for this course.');
        }
        
        // Query students
        $query = $course->students();
        
        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter if provided
        if ($request->filled('status')) {
            $query->wherePivot('status', $request->status);
        }
        
        // Get student submission counts
        $students = $query->withCount(['submissions' => function($query) use ($course) {
                        $query->whereHas('assignment', function($q) use ($course) {
                            $q->where('course_id', $course->id);
                        });
                    }])
                    ->paginate(15);
        
        // Count assignments for this course
        $assignmentCount = 0;
        try {
            $assignmentCount = $course->assignments()->count();
        } catch (QueryException $e) {
            $assignmentCount = 0;
        }
        
        // Count students by status
        $activeCount = $course->students()->wherePivot('status', 'active')->count();
        $inactiveCount = $course->students()->wherePivot('status', 'inactive')->count();
        $completedCount = $course->students()->wherePivot('status', 'completed')->count();
        
        return view('courses.course-students', compact(
            'course', 'students', 'assignmentCount', 
            'activeCount', 'inactiveCount', 'completedCount'
        ));
    }
}