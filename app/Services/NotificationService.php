<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Notification;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create notification for new assignment.
     */
    public function notifyAssignmentCreated(Assignment $assignment): void
    {
        $students = $assignment->course->students;
        
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'type' => 'assignment_created',
                'title' => 'New Assignment: ' . $assignment->title,
                'message' => "A new assignment has been posted in {$assignment->course->name}. Due: {$assignment->deadline->format('M d, Y H:i')}",
                'data' => [
                    'assignment_id' => $assignment->id,
                    'course_id' => $assignment->course_id,
                    'deadline' => $assignment->deadline->toISOString(),
                ],
            ]);
        }
    }

    /**
     * Create notification for graded submission.
     */
    public function notifySubmissionGraded(Submission $submission): void
    {
        Notification::create([
            'user_id' => $submission->user_id,
            'type' => 'assignment_graded',
            'title' => 'Assignment Graded: ' . $submission->assignment->title,
            'message' => "Your submission for '{$submission->assignment->title}' has been graded. Score: {$submission->score}/{$submission->assignment->max_score}",
            'data' => [
                'submission_id' => $submission->id,
                'assignment_id' => $submission->assignment_id,
                'course_id' => $submission->assignment->course_id,
                'score' => $submission->score,
                'max_score' => $submission->assignment->max_score,
            ],
        ]);
    }

    /**
     * Create deadline reminder notifications.
     */
    public function createDeadlineReminders(Assignment $assignment): void
    {
        $students = $assignment->course->students;
        $reminderTimes = [
            $assignment->deadline->subDays(3), // 3 days before
            $assignment->deadline->subDay(),    // 1 day before
            $assignment->deadline->subHours(2), // 2 hours before
        ];

        foreach ($students as $student) {
            foreach ($reminderTimes as $reminderTime) {
                if ($reminderTime->isFuture()) {
                    Notification::create([
                        'user_id' => $student->id,
                        'type' => 'deadline_reminder',
                        'title' => 'Assignment Due Soon: ' . $assignment->title,
                        'message' => "Reminder: '{$assignment->title}' is due on {$assignment->deadline->format('M d, Y H:i')}",
                        'data' => [
                            'assignment_id' => $assignment->id,
                            'course_id' => $assignment->course_id,
                            'deadline' => $assignment->deadline->toISOString(),
                        ],
                        'scheduled_for' => $reminderTime,
                    ]);
                }
            }
        }
    }

    /**
     * Get notifications for a user with pagination.
     */
    public function getUserNotifications(User $user, int $perPage = 10)
    {
        return $user->notifications()
                   ->orderBy('created_at', 'desc')
                   ->paginate($perPage);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): void
    {
        $user->notifications()->unread()->update(['read_at' => now()]);
    }

    /**
     * Get unread notifications count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->unread()->count();
    }

    /**
     * Send pending deadline reminders.
     */
    public function sendPendingDeadlineReminders(): void
    {
        $pendingReminders = Notification::where('type', 'deadline_reminder')
                                      ->where('scheduled_for', '<=', now())
                                      ->whereNull('read_at')
                                      ->where('sent_email', false)
                                      ->get();

        foreach ($pendingReminders as $notification) {
            // Mark as sent (in a real app, you'd also send an email here)
            $notification->update(['sent_email' => true]);
            
            // Optionally send email notification here
            // Mail::to($notification->user)->send(new DeadlineReminderMail($notification));
        }
    }

    /**
     * Clean up old notifications (older than 30 days).
     */
    public function cleanupOldNotifications(): void
    {
        Notification::where('created_at', '<', now()->subDays(30))
                   ->whereNotNull('read_at')
                   ->delete();
    }

    /**
     * Notify lecturer about new submission.
     */
    public function notifySubmissionReceived(Submission $submission): void
    {
        $lecturer = $submission->assignment->course->lecturer;
        
        Notification::create([
            'user_id' => $lecturer->id,
            'type' => 'submission_received',
            'title' => 'New Submission: ' . $submission->assignment->title,
            'message' => "Student {$submission->user->name} has submitted their work for '{$submission->assignment->title}'",
            'data' => [
                'submission_id' => $submission->id,
                'assignment_id' => $submission->assignment_id,
                'course_id' => $submission->assignment->course_id,
                'student_id' => $submission->user_id,
                'student_name' => $submission->user->name,
            ],
        ]);
    }

    /**
     * Notify admin about new user registration.
     */
    public function notifyUserCreated(User $user, User $createdBy): void
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            if ($admin->id !== $createdBy->id) { // Don't notify the admin who created the user
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'user_created',
                    'title' => 'New User Created: ' . $user->name,
                    'message' => "A new {$user->role} account has been created for {$user->name} ({$user->email}) by {$createdBy->name}",
                    'data' => [
                        'user_id' => $user->id,
                        'user_role' => $user->role,
                        'created_by' => $createdBy->id,
                        'created_by_name' => $createdBy->name,
                    ],
                ]);
            }
        }
    }

    /**
     * Notify students about course enrollment.
     */
    public function notifyCourseEnrollment(User $student, Course $course): void
    {
        Notification::create([
            'user_id' => $student->id,
            'type' => 'course_enrolled',
            'title' => 'Enrolled in Course: ' . $course->name,
            'message' => "You have been successfully enrolled in '{$course->name}'. You can now access assignments and course materials.",
            'data' => [
                'course_id' => $course->id,
                'course_code' => $course->code,
                'lecturer_id' => $course->lecturer_id,
                'lecturer_name' => $course->lecturer->name,
            ],
        ]);
    }

    /**
     * Notify lecturer about new course creation.
     */
    public function notifyCourseCreated(Course $course): void
    {
        $lecturer = $course->lecturer;
        
        Notification::create([
            'user_id' => $lecturer->id,
            'type' => 'course_created',
            'title' => 'Course Created: ' . $course->name,
            'message' => "Your course '{$course->name} ({$course->code})' has been successfully created. You can now add assignments and manage enrollments.",
            'data' => [
                'course_id' => $course->id,
                'course_code' => $course->code,
                'semester_id' => $course->semester_id,
            ],
        ]);
    }

    /**
     * Notify student about grade update.
     */
    public function notifyGradeUpdated(Submission $submission, $oldScore = null): void
    {
        $message = $oldScore !== null 
            ? "Your grade for '{$submission->assignment->title}' has been updated from {$oldScore} to {$submission->score} out of {$submission->assignment->max_score}"
            : "Your submission for '{$submission->assignment->title}' has been graded. Score: {$submission->score}/{$submission->assignment->max_score}";

        Notification::create([
            'user_id' => $submission->user_id,
            'type' => 'grade_updated',
            'title' => 'Grade Updated: ' . $submission->assignment->title,
            'message' => $message,
            'data' => [
                'submission_id' => $submission->id,
                'assignment_id' => $submission->assignment_id,
                'course_id' => $submission->assignment->course_id,
                'old_score' => $oldScore,
                'new_score' => $submission->score,
                'max_score' => $submission->assignment->max_score,
            ],
        ]);
    }

    /**
     * Create welcome notification for new users.
     */
    public function createWelcomeNotification(User $user): void
    {
        $message = match($user->role) {
            'admin' => 'Welcome to TaskCampus! As an admin, you can manage users, courses, and oversee the entire system.',
            'lecturer' => 'Welcome to TaskCampus! You can now create courses, assignments, and manage your students.',
            'student' => 'Welcome to TaskCampus! You can now enroll in courses, submit assignments, and track your progress.',
            default => 'Welcome to TaskCampus! Explore the platform and discover all the features available to you.',
        };

        Notification::create([
            'user_id' => $user->id,
            'type' => 'user_created',
            'title' => 'Welcome to TaskCampus! 🎉',
            'message' => $message,
            'data' => [
                'user_role' => $user->role,
                'welcome_message' => true,
            ],
        ]);
    }

    /**
     * Create notification for assignment deletion.
     */
    public function notifyAssignmentDeleted(Assignment $assignment, bool $forceDelete = false, int $submissionCount = 0): void
    {
        $students = $assignment->course->students;
        $lecturer = $assignment->course->lecturer;
        
        $title = $forceDelete 
            ? 'Assignment Force Deleted: ' . $assignment->title
            : 'Assignment Deleted: ' . $assignment->title;
            
        $message = $forceDelete 
            ? "The assignment '{$assignment->title}' in {$assignment->course->name} has been FORCE DELETED along with all {$submissionCount} submission(s)."
            : "The assignment '{$assignment->title}' in {$assignment->course->name} has been deleted.";
        
        // Notify students
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'type' => 'assignment_deleted',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'assignment_title' => $assignment->title,
                    'course_id' => $assignment->course_id,
                    'course_name' => $assignment->course->name,
                    'force_delete' => $forceDelete,
                    'submission_count' => $submissionCount,
                ],
            ]);
        }
        
        // Notify lecturer if force delete by admin
        if ($forceDelete && $lecturer && $lecturer->id !== auth()->id()) {
            Notification::create([
                'user_id' => $lecturer->id,
                'type' => 'assignment_deleted',
                'title' => 'Your Assignment Was Force Deleted: ' . $assignment->title,
                'message' => "Your assignment '{$assignment->title}' in {$assignment->course->name} has been FORCE DELETED by an admin along with all {$submissionCount} submission(s).",
                'data' => [
                    'assignment_title' => $assignment->title,
                    'course_id' => $assignment->course_id,
                    'course_name' => $assignment->course->name,
                    'force_delete' => $forceDelete,
                    'submission_count' => $submissionCount,
                    'deleted_by_admin' => true,
                ],
            ]);
        }
    }
}
