<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\RubricController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect homepage to login page for guests, dashboard for authenticated users
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Explicitly prevent access to welcome page
Route::get('/welcome', function () {
    return redirect()->route('home');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    //OTORISASI/AUTHORIZATION
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('semesters', SemesterController::class);
    });
    
    // Admin and lecturer can manage courses
    Route::resource('courses', CourseController::class);
    Route::post('/courses/{course}/regenerate-code', [CourseController::class, 'regenerateCode'])
        ->name('courses.regenerateCode');
    
    // Student routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/enroll', [EnrollmentController::class, 'create'])->name('enroll.create');
        Route::post('/enroll', [EnrollmentController::class, 'store'])->name('enroll.store');
        Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    });
    
    // Routes for managing enrollments (accessible by different roles based on permissions in controller)
    Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollments.update');
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Student submissions route
    Route::get('/my-submissions', [SubmissionController::class, 'mySubmissions'])->name('submissions.my');

    // Assignment routes
    Route::resource('assignments', AssignmentController::class);
    Route::get('/assignments/{assignment}/download', [AssignmentController::class, 'downloadFile'])->name('assignments.download');
    
    // Submission routes
    Route::get('/assignments/{assignment}/submit', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/assignments/{assignment}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::get('/submissions/{submission}/download', [SubmissionController::class, 'downloadFile'])->name('submissions.download');
    Route::get('/submissions/{submission}/grade', [SubmissionController::class, 'gradeForm'])->name('submissions.grade-form');
    Route::post('/submissions/{submission}/grade', [SubmissionController::class, 'grade'])->name('submissions.grade');
    Route::post('/submissions/{submission}/return', [SubmissionController::class, 'returnToStudent'])->name('submissions.return');
    
    // Rubric routes
    Route::get('/assignments/{assignment}/rubric/create', [RubricController::class, 'create'])->name('rubrics.create');
    Route::post('/assignments/{assignment}/rubric', [RubricController::class, 'store'])->name('rubrics.store');
    Route::get('/rubrics/{rubric}', [RubricController::class, 'show'])->name('rubrics.show');
    Route::get('/rubrics/{rubric}/edit', [RubricController::class, 'edit'])->name('rubrics.edit');
    Route::put('/rubrics/{rubric}', [RubricController::class, 'update'])->name('rubrics.update');
    Route::delete('/rubrics/{rubric}', [RubricController::class, 'destroy'])->name('rubrics.destroy');
    
    //OTORISASI/AUTHORIZATION
    // Lecturer routes
    Route::middleware(['role:lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
        Route::get('/courses/dashboard', [CourseController::class, 'lecturerDashboard'])->name('courses.dashboard');
        Route::get('/courses/{course}/students', [CourseController::class, 'courseStudents'])->name('courses.students');
    });
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');
    
    // Export routes
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/course/{course}/grades', [App\Http\Controllers\ExportController::class, 'exportCourseGrades'])->name('course.grades');
        Route::get('/course/{course}/students', [App\Http\Controllers\ExportController::class, 'exportCourseStudents'])->name('course.students');
        Route::get('/assignment/{assignment}/submissions', [App\Http\Controllers\ExportController::class, 'exportAssignmentSubmissions'])->name('assignment.submissions');
        Route::get('/student/grades', [App\Http\Controllers\ExportController::class, 'exportStudentGrades'])->name('student.grades');
    });

    // Import routes (Admin only)
    Route::prefix('import')->name('import.')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [App\Http\Controllers\ImportController::class, 'index'])->name('index');
        Route::post('/students', [App\Http\Controllers\ImportController::class, 'importStudents'])->name('students');
        // Route::post('/courses', [App\Http\Controllers\ImportController::class, 'importCourses'])->name('courses'); // Disabled for now
        Route::get('/template/students', [App\Http\Controllers\ImportController::class, 'downloadStudentsTemplate'])->name('template.students');
        Route::get('/sample/students', [App\Http\Controllers\ImportController::class, 'downloadStudentsSample'])->name('sample.students');
        // Route::get('/template/courses', [App\Http\Controllers\ImportController::class, 'downloadCoursesTemplate'])->name('template.courses'); // Disabled for now
    });
    
    // Admin User Management routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::post('users/sample-data', [App\Http\Controllers\Admin\UserController::class, 'createSampleData'])->name('users.sample-data');
    });
});

require __DIR__.'/auth.php';