<?php

require_once 'vendor/autoload.php';

// Load Laravel Application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;

echo "=== Debugging Enrollment Issue ===\n";

// Check basic data
echo "Total courses: " . Course::count() . "\n";
echo "Total students: " . User::where('role', 'student')->count() . "\n";
echo "Total enrollments: " . Enrollment::count() . "\n\n";

// Get first course
$course = Course::first();
if ($course) {
    echo "Course found: ID={$course->id}, Name={$course->name}\n";
    echo "Course students count: " . $course->students()->count() . "\n\n";
}

// Get first student
$student = User::where('role', 'student')->first();
if ($student) {
    echo "Student found: ID={$student->id}, Name={$student->name}\n\n";
}

// Check enrollments for this course
if ($course) {
    $enrollments = Enrollment::where('course_id', $course->id)->get();
    echo "Enrollments for course {$course->id}:\n";
    foreach ($enrollments as $enrollment) {
        echo "- User ID: {$enrollment->user_id}, Status: {$enrollment->status}\n";
    }
}

// Create test enrollment if needed
if ($course && $student && Enrollment::where('course_id', $course->id)->where('user_id', $student->id)->count() == 0) {
    echo "\nCreating test enrollment...\n";
    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active'
    ]);
    echo "Enrollment created: ID={$enrollment->id}\n";
}

echo "\n=== After potential enrollment creation ===\n";
echo "Total enrollments: " . Enrollment::count() . "\n";
if ($course) {
    echo "Course students count: " . $course->students()->count() . "\n";
}
