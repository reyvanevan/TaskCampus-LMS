<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Rubric;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class AssignmentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Check if a specific course is requested
        $courseId = $request->query('course_id');
        
        $query = Assignment::with(['course', 'course.lecturer']);
        
        // Filter by course if provided
        if ($courseId) {
            $query->where('course_id', $courseId);
            $course = Course::findOrFail($courseId);
            
            // Check if user has access to this course
            $this->authorizeAccess($course);
        }
        
        // If lecturer, only show assignments from their courses
        if (Auth::user()->isLecturer()) {
            $query->whereHas('course', function($q) {
                $q->where('lecturer_id', Auth::id());
            });
        }
        
        // If student, only show published assignments from enrolled courses
        if (Auth::user()->isStudent()) {
            $query->where('status', 'published')
                ->whereHas('course.students', function($q) {
                    $q->where('users.id', Auth::id());
                });
        }
        
        $assignments = $query->orderBy('deadline', 'asc')
                            ->paginate(10);
        
        $courses = [];
        if (Auth::user()->isAdmin()) {
            $courses = Course::all();
        } elseif (Auth::user()->isLecturer()) {
            $courses = Auth::user()->teachingCourses;
        } elseif (Auth::user()->isStudent()) {
            $courses = Auth::user()->enrolledCourses;
        }
        
        return view('assignments.index', compact('assignments', 'courses', 'courseId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        // Only admin and lecturers can create assignments
        if (Auth::user()->isStudent()) {
            abort(403, 'You are not authorized to create assignments.');
        }
        
        // Get available courses based on user role
        if (Auth::user()->isAdmin()) {
            $courses = Course::where('status', 'active')->get();
        } else {
            $courses = Course::where('lecturer_id', Auth::id())
                            ->where('status', 'active')
                            ->get();
        }
        
        // Pre-select course if provided in query string
        $selectedCourse = null;
        if ($request->has('course_id')) {
            $courseId = $request->query('course_id');
            $selectedCourse = Course::find($courseId);
            
            // Verify that the lecturer has access to this course
            if (Auth::user()->isLecturer() && $selectedCourse && $selectedCourse->lecturer_id != Auth::id()) {
                $selectedCourse = null;
            }
        }
        
        return view('assignments.create', compact('courses', 'selectedCourse'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Only admin and lecturers can create assignments
        if (Auth::user()->isStudent()) {
            abort(403, 'You are not authorized to create assignments.');
        }
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'deadline' => 'required|date|after:now',
            'max_score' => 'required|integer|min:1',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'allow_late_submissions' => 'sometimes|boolean',
            'assignment_file' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        // Ensure the lecturer has access to the course
        $course = Course::findOrFail($validatedData['course_id']);
        if (Auth::user()->isLecturer() && $course->lecturer_id != Auth::id()) {
            return redirect()->back()
                    ->withInput()
                    ->with('error', 'You do not have permission to create assignments for this course.');
        }
        
        // Handle file upload if present
        if ($request->hasFile('assignment_file')) {
            $originalName = $request->file('assignment_file')->getClientOriginalName();
            $path = $request->file('assignment_file')->store('assignments', 'public');
            $validatedData['file_path'] = $path;
            $validatedData['original_filename'] = $originalName;
        }
        
        $assignment = Assignment::create($validatedData);
        
        // Send notifications if assignment is published
        if ($assignment->status === 'published') {
            $this->notificationService->notifyAssignmentCreated($assignment);
            $this->notificationService->createDeadlineReminders($assignment);
        }
        
        return redirect()->route('assignments.show', $assignment)
                ->with('success', 'Assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment): View
    {
        // Load related data
        $assignment->load(['course', 'course.lecturer', 'rubric', 'rubric.criteria']);
        
        // Check if the user has access to this assignment
        $this->authorizeAccess($assignment->course);
        
        // For students, check if they have a submission for this assignment
        $submission = null;
        if (Auth::user()->isStudent()) {
            $submission = $assignment->submissions()
                                    ->where('user_id', Auth::id())
                                    ->first();
        }
        
        // For lecturers, get the submissions for this assignment
        $submissions = [];
        if (Auth::user()->isLecturer() || Auth::user()->isAdmin()) {
            $submissions = $assignment->submissions()
                                    ->with('user')
                                    ->paginate(15);
        }
        
        return view('assignments.show', compact('assignment', 'submission', 'submissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment): View
    {
        // Only admin and assignment owner (lecturer) can edit assignments
        if (Auth::user()->isStudent()) {
            abort(403, 'You are not authorized to edit assignments.');
        }
        
        // If lecturer, check if they own the course
        if (Auth::user()->isLecturer() && $assignment->course->lecturer_id != Auth::id()) {
            abort(403, 'You are not authorized to edit this assignment.');
        }
        
        // Load related data
        $assignment->load('course');
        
        // Get available courses based on user role
        if (Auth::user()->isAdmin()) {
            $courses = Course::where('status', 'active')->get();
        } else {
            $courses = Course::where('lecturer_id', Auth::id())
                            ->where('status', 'active')
                            ->get();
        }
        
        return view('assignments.edit', compact('assignment', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        // Only admin and assignment owner (lecturer) can update assignments
        if (Auth::user()->isStudent()) {
            abort(403, 'You are not authorized to update assignments.');
        }
        
        // If lecturer, check if they own the course
        if (Auth::user()->isLecturer() && $assignment->course->lecturer_id != Auth::id()) {
            abort(403, 'You are not authorized to update this assignment.');
        }
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'deadline' => 'required|date',
            'max_score' => 'required|integer|min:1',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'allow_late_submissions' => 'sometimes|boolean',
            'assignment_file' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        // Ensure the lecturer has access to the course
        $course = Course::findOrFail($validatedData['course_id']);
        if (Auth::user()->isLecturer() && $course->lecturer_id != Auth::id()) {
            return redirect()->back()
                    ->withInput()
                    ->with('error', 'You do not have permission to assign to this course.');
        }
        
        // Handle file upload if present
        if ($request->hasFile('assignment_file')) {
            // Delete old file if exists
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            
            $originalName = $request->file('assignment_file')->getClientOriginalName();
            $path = $request->file('assignment_file')->store('assignments', 'public');
            $validatedData['file_path'] = $path;
            $validatedData['original_filename'] = $originalName;
        }
        
        // Set allow_late_submissions to false if not provided
        $validatedData['allow_late_submissions'] = $request->has('allow_late_submissions');
        
        $assignment->update($validatedData);
        
        return redirect()->route('assignments.show', $assignment)
                ->with('success', 'Assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment, Request $request): RedirectResponse
    {
        // Only admin and assignment owner (lecturer) can delete assignments
        if (Auth::user()->isStudent()) {
            abort(403, 'You are not authorized to delete assignments.');
        }
        
        // If lecturer, check if they own the course
        if (Auth::user()->isLecturer() && $assignment->course->lecturer_id != Auth::id()) {
            abort(403, 'You are not authorized to delete this assignment.');
        }
        
        try {
            $assignmentTitle = $assignment->title;
            $submissionCount = $assignment->submissions()->count();
            
            // Check if force delete is requested
            $forceDelete = $request->has('force') && $request->boolean('force');
            
            // If has submissions and not force delete, offer force delete option
            if ($submissionCount > 0 && !$forceDelete) {
                $forceDeleteUrl = route('assignments.destroy', $assignment) . '?force=1';
                
                return redirect()->back()
                    ->with('error', "Error! Cannot delete assignment '{$assignmentTitle}'. It has {$submissionCount} submission(s). Please remove all submissions first or contact admin.")
                    ->with('show_force_delete', true)
                    ->with('force_delete_data', [
                        'assignment_id' => $assignment->id,
                        'assignment_title' => $assignmentTitle,
                        'submission_count' => $submissionCount,
                        'force_delete_url' => $forceDeleteUrl,
                    ]);
            }
            
            // Proceed with deletion (normal or force)
            if ($forceDelete && $submissionCount > 0) {
                // Force delete: remove all submissions first
                foreach ($assignment->submissions as $submission) {
                    // Delete submission files
                    if ($submission->file_path) {
                        Storage::disk('public')->delete($submission->file_path);
                    }
                    
                    // Delete criteria scores
                    $submission->criteriaScores()->delete();
                    
                    // Delete submission
                    $submission->delete();
                }
            }
            
            // Delete assignment file if exists
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            
            // Delete the rubric (will cascade to criteria)
            if ($assignment->rubric) {
                $assignment->rubric->delete();
            }

            // Send notifications before deleting assignment
            $this->notificationService->notifyAssignmentDeleted($assignment, $forceDelete, $submissionCount);
            
            $assignment->delete();
            
            $successMessage = $forceDelete 
                ? "Assignment '{$assignmentTitle}' and all {$submissionCount} submission(s) have been FORCE DELETED successfully!"
                : "Assignment '{$assignmentTitle}' deleted successfully.";
            
            return redirect()->route('assignments.index')
                    ->with('success', $successMessage);
                    
        } catch (\Exception $e) {
            return redirect()->back()
                    ->with('error', 'Error deleting assignment: ' . $e->getMessage());
        }
    }

    /**
     * Authorize access to the course/assignment.
     */
    protected function authorizeAccess(Course $course): void
    {
        // Admin has access to everything
        if (Auth::user()->isAdmin()) {
            return;
        }
        
        // Lecturer must own the course
        if (Auth::user()->isLecturer() && $course->lecturer_id != Auth::id()) {
            abort(403, 'You do not have access to this course.');
        }
        
        // Student must be enrolled in the course with active status
        if (Auth::user()->isStudent() && !$course->students()->where('users.id', Auth::id())->wherePivot('status', 'active')->exists()) {
            abort(403, 'You are not enrolled in this course.');
        }
    }

    /**
     * Regenerate enrollment code for the course.
     */
    public function regenerateCode(Course $course): RedirectResponse
    {
        // Only admin and assignment owner (lecturer) can regenerate code
        if (Auth::user()->isStudent()) {
            abort(403, 'You are not authorized to regenerate codes.');
        }
        
        // If lecturer, check if they own the course
        if (Auth::user()->isLecturer() && $course->lecturer_id != Auth::id()) {
            abort(403, 'You are not authorized to regenerate this code.');
        }
        
        $course->enrollment_code = Str::upper(Str::random(8));
        $course->save();
        
        return redirect()->route('courses.show', $course)
                ->with('success', 'Enrollment code regenerated successfully.');
    }

    /**
     * Download assignment file
     */
    public function downloadFile(Assignment $assignment)
    {
        // Check if the user has access to this assignment
        $this->authorizeAccess($assignment->course);
        
        if (!$assignment->file_path || !Storage::disk('public')->exists($assignment->file_path)) {
            abort(404, 'File not found.');
        }
        
        return Storage::disk('public')->download(
            $assignment->file_path, 
            $assignment->original_filename ?: pathinfo($assignment->file_path, PATHINFO_BASENAME)
        );
    }
}