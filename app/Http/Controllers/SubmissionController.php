<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\RubricCriteria;
use App\Models\SubmissionScore;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;

class SubmissionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show the form for creating a new submission.
     */
    public function create(Assignment $assignment)
    {
        // Only students can submit assignments
        if (!Auth::user()->isStudent()) {
            abort(403, 'Only students can submit assignments.');
        }
        
        // Check if the student is enrolled in the course
        if (!$assignment->course->students()->where('users.id', Auth::id())->wherePivot('status', 'active')->exists()) {
            abort(403, 'You are not enrolled in this course.');
        }
        
        // Check if the assignment is published
        if ($assignment->status !== 'published') {
            abort(403, 'This assignment is not available for submission.');
        }
        
        // Check if the deadline has passed and late submissions are not allowed
        if ($assignment->isPastDue()) {
            abort(403, 'The deadline for this assignment has passed.');
        }
        
        // Check if student has already submitted
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
                                      ->where('user_id', Auth::id())
                                      ->first();
        
        if ($existingSubmission) {
            return redirect()->route('submissions.edit', $existingSubmission)
                    ->with('info', 'You have already submitted this assignment. You can edit your submission here.');
        }
        
        return view('submissions.create', compact('assignment'));
    }

    /**
     * Store a newly created submission.
     */
    public function store(Request $request, Assignment $assignment): RedirectResponse
    {
        // Only students can submit assignments
        if (!Auth::user()->isStudent()) {
            abort(403, 'Only students can submit assignments.');
        }
        
        // Check if the student is enrolled in the course
        if (!$assignment->course->students()->where('users.id', Auth::id())->wherePivot('status', 'active')->exists()) {
            abort(403, 'You are not enrolled in this course.');
        }
        
        // Check if the assignment is published
        if ($assignment->status !== 'published') {
            abort(403, 'This assignment is not available for submission.');
        }
        
        // Check if the deadline has passed and late submissions are not allowed
        $isLate = false;
        if (now() > $assignment->deadline) {
            if (!$assignment->allow_late_submissions) {
                abort(403, 'The deadline for this assignment has passed.');
            }
            $isLate = true;
        }
        
        // Check if student has already submitted
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
                                      ->where('user_id', Auth::id())
                                      ->first();
        
        if ($existingSubmission) {
            return redirect()->route('submissions.edit', $existingSubmission)
                    ->with('info', 'You have already submitted this assignment. You can edit your submission here.');
        }
        
        $validatedData = $request->validate([
            'comment' => 'nullable|string',
            'submission_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,jpg,jpeg,png,gif|max:10240', // 10MB max with specific allowed types
        ]);
        
        // Handle file upload with original filename
        $originalName = $request->file('submission_file')->getClientOriginalName();
        $safeFilename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.' . $request->file('submission_file')->getClientOriginalExtension();
        $path = $request->file('submission_file')->storeAs('submissions/' . $assignment->id, $safeFilename, 'public');
        
        // Create submission
        $submission = Submission::create([
            'user_id' => Auth::id(),
            'assignment_id' => $assignment->id,
            'file_path' => $path,
            'original_filename' => $originalName,
            'comment' => $validatedData['comment'],
            'status' => $isLate ? 'late' : 'submitted',
            'is_late' => $isLate,
        ]);

        // Notify lecturer about new submission
        $this->notificationService->notifySubmissionReceived($submission);
        
        return redirect()->route('submissions.show', $submission)
                ->with('success', 'Assignment submitted successfully.');
    }

    /**
     * Display the specified submission.
     */
    public function show(Submission $submission): View
    {
        // Load related data
        $submission->load(['assignment', 'assignment.course', 'user', 'criteriaScores']);
        
        // Check if the user can view this submission
        $this->authorizeAccess($submission);
        
        $rubric = $submission->assignment->rubric;
        $criteriaScores = [];
        
        // If the submission has been graded using a rubric, prepare the criteria scores
        if ($rubric) {
            foreach ($rubric->criteria as $criteria) {
                $criteriaScore = $submission->criteriaScores()
                                        ->where('rubric_criteria_id', $criteria->id)
                                        ->first();
                
                $criteriaScores[$criteria->id] = $criteriaScore;
            }
        }
        
        return view('submissions.show', compact('submission', 'rubric', 'criteriaScores'));
    }

    /**
     * Show the form for editing the submission.
     */
    public function edit(Submission $submission)
    {
        // Only the student who made the submission can edit it
        if (Auth::id() !== $submission->user_id) {
            abort(403, 'You are not authorized to edit this submission.');
        }
        
        // Check if the submission can still be edited (not graded or returned)
        if (in_array($submission->status, ['graded', 'returned'])) {
            return redirect()->route('submissions.show', $submission)
                    ->with('error', 'This submission has already been graded and cannot be edited.');
        }
        
        // Check if the deadline has passed and late submissions are not allowed
        if ($submission->assignment->isPastDue()) {
            return redirect()->route('submissions.show', $submission)
                    ->with('error', 'The deadline for this assignment has passed.');
        }
        
        return view('submissions.edit', compact('submission'));
    }

    /**
     * Update the submission.
     */
    public function update(Request $request, Submission $submission): RedirectResponse
    {
        // Only the student who made the submission can update it
        if (Auth::id() !== $submission->user_id) {
            abort(403, 'You are not authorized to update this submission.');
        }
        
        // Check if the submission can still be updated (not graded or returned)
        if (in_array($submission->status, ['graded', 'returned'])) {
            return redirect()->route('submissions.show', $submission)
                    ->with('error', 'This submission has already been graded and cannot be updated.');
        }
        
        // Check if the deadline has passed and late submissions are not allowed
        if ($submission->assignment->isPastDue()) {
            return redirect()->route('submissions.show', $submission)
                    ->with('error', 'The deadline for this assignment has passed.');
        }
        
        $validatedData = $request->validate([
            'comment' => 'nullable|string',
            'submission_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,jpg,jpeg,png,gif|max:10240', // 10MB max with specific allowed types
        ]);
        
        // Handle file upload if provided
        if ($request->hasFile('submission_file')) {
            // Delete old file if exists
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
            
            // Store with original filename + timestamp
            $originalName = $request->file('submission_file')->getClientOriginalName();
            $safeFilename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.' . $request->file('submission_file')->getClientOriginalExtension();
            $path = $request->file('submission_file')->storeAs(
                'submissions/' . $submission->assignment_id, 
                $safeFilename, 
                'public'
            );
            $submission->file_path = $path;
            $submission->original_filename = $originalName;
        }
        
        $submission->comment = $validatedData['comment'];
        
        // Check if submission is late
        $isLate = now() > $submission->assignment->deadline;
        if ($isLate && $submission->assignment->allow_late_submissions) {
            $submission->is_late = true;
            $submission->status = 'late';
        }
        
        $submission->save();
        
        return redirect()->route('submissions.show', $submission)
                ->with('success', 'Submission updated successfully.');
    }

    /**
     * Grade the submission.
     */
    public function grade(Request $request, Submission $submission): RedirectResponse
    {
        // Only admin and the course lecturer can grade submissions
        if (Auth::user()->isStudent()) {
            abort(403, 'Students cannot grade submissions.');
        }
        
        // If lecturer, check if they own the course
        if (Auth::user()->isLecturer() && $submission->assignment->course->lecturer_id != Auth::id()) {
            abort(403, 'You are not authorized to grade submissions for this course.');
        }
        
        $validatedData = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $submission->assignment->max_score,
            'feedback' => 'nullable|string',
            'criteria_score' => 'nullable|array',
            'criteria_comment' => 'nullable|array',
        ]);
        
        $submission->score = $validatedData['score'];
        $submission->feedback = $validatedData['feedback'];
        $submission->status = 'graded';
        $submission->save();
        
        // Process rubric criteria scores if provided
        if ($request->has('criteria_score') && $submission->assignment->rubric) {
            foreach ($validatedData['criteria_score'] as $criteriaId => $score) {
                $criteria = RubricCriteria::find($criteriaId);
                
                if ($criteria && $criteria->rubric_id == $submission->assignment->rubric->id) {
                    SubmissionScore::updateOrCreate(
                        [
                            'submission_id' => $submission->id,
                            'rubric_criteria_id' => $criteriaId,
                        ],
                        [
                            'score' => min($score, $criteria->max_score),
                            'comment' => $validatedData['criteria_comment'][$criteriaId] ?? null,
                        ]
                    );
                }
            }
        }
        
        // Send notification to student
        $this->notificationService->notifySubmissionGraded($submission);
        
        return redirect()->route('submissions.show', $submission)
                ->with('success', 'Submission graded successfully.');
    }

    /**
     * Return the graded submission to the student.
     */
    public function returnToStudent(Submission $submission): RedirectResponse
    {
        // Only admin and the course lecturer can return submissions
        if (Auth::user()->isStudent()) {
            abort(403, 'Students cannot return submissions.');
        }
        
        // If lecturer, check if they own the course
        if (Auth::user()->isLecturer() && $submission->assignment->course->lecturer_id != Auth::id()) {
            abort(403, 'You are not authorized to return submissions for this course.');
        }
        
        // Check if submission is already graded
        if ($submission->status !== 'graded') {
            return redirect()->back()
                    ->with('error', 'Only graded submissions can be returned to students.');
        }
        
        $submission->status = 'returned';
        $submission->save();
        
        return redirect()->route('submissions.show', $submission)
                ->with('success', 'Submission returned to student successfully.');
    }

    /**
     * Authorize access to the submission.
     */
    protected function authorizeAccess(Submission $submission): void
    {
        // Admin has access to everything
        if (Auth::user()->isAdmin()) {
            return;
        }
        
        // Lecturer must own the course
        if (Auth::user()->isLecturer() && $submission->assignment->course->lecturer_id != Auth::id()) {
            abort(403, 'You do not have access to this submission.');
        }
        
        // Student can only view their own submissions
        if (Auth::user()->isStudent() && $submission->user_id != Auth::id()) {
            abort(403, 'You do not have access to this submission.');
        }
    }
    
    /**
     * List all submissions by the logged-in student
     */
    public function mySubmissions(Request $request): View
    {
        // Only students can view their submissions
        if (!Auth::user()->isStudent()) {
            abort(403, 'Only students can view their submissions.');
        }
        
        $query = Submission::where('user_id', Auth::id())
                  ->with(['assignment', 'assignment.course']);
        
        // Filter by course if provided
        if ($request->filled('course_id')) {
            $courseId = $request->course_id;
            $query->whereHas('assignment', function($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }
        
        $submissions = $query->orderBy('created_at', 'desc')
                      ->paginate(10);
        
        // Get the student's enrolled courses for the filter dropdown
        $courses = Auth::user()->enrolledCourses()->get();
                      
        return view('submissions.my-submissions', compact('submissions', 'courses'));
    }
    
    /**
     * Download submission file
     */
    public function downloadFile(Submission $submission)
    {
        // Check authorization
        if (Auth::id() !== $submission->user_id && 
            !Auth::user()->isAdmin() && 
            !(Auth::user()->isLecturer() && Auth::id() === $submission->assignment->course->lecturer_id)) {
            abort(403, 'You are not authorized to download this file.');
        }
        
        if (!$submission->file_path || !Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found.');
        }
        
        return Storage::disk('public')->download(
            $submission->file_path, 
            $submission->original_filename ?: pathinfo($submission->file_path, PATHINFO_BASENAME)
        );
    }
    
    /**
     * Show the grading form for the submission.
     */
    public function gradeForm(Submission $submission)
    {
        // Only admin and the course lecturer can grade submissions
        if (Auth::user()->isStudent()) {
            abort(403, 'Students cannot grade submissions.');
        }
        
        if (Auth::user()->isLecturer() && $submission->assignment->course->lecturer_id !== Auth::id()) {
            abort(403, 'You are not authorized to grade submissions for this course.');
        }
        
        // Check if submission is already graded
        if (in_array($submission->status, ['graded', 'returned'])) {
            return redirect()->route('submissions.show', $submission)
                    ->with('info', 'This submission has already been graded.');
        }
        
        // Load rubric and criteria if exists
        $rubric = $submission->assignment->rubric;
        $criteriaScores = [];
        
        if ($rubric) {
            $criteriaScores = $submission->scores()
                                       ->with('rubricCriteria')
                                       ->get()
                                       ->keyBy('rubric_criteria_id');
        }
        
        return view('submissions.grade', compact('submission', 'rubric', 'criteriaScores'));
    }
}