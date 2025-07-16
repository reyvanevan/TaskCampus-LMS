<?php

require_once 'vendor/autoload.php';

use App\Models\Assignment;
use App\Models\Submission;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Test assignment with submissions
$assignment = Assignment::first();
if ($assignment) {
    echo "Assignment: " . $assignment->title . "\n";
    echo "Submissions count: " . $assignment->submissions()->count() . "\n";
    
    if ($assignment->submissions()->count() > 0) {
        echo "This assignment has submissions - testing force delete scenario\n";
        echo "Force delete URL would be: " . route('assignments.destroy', $assignment) . "?force=1\n";
    } else {
        echo "This assignment has no submissions - regular delete would work\n";
    }
} else {
    echo "No assignments found\n";
}
