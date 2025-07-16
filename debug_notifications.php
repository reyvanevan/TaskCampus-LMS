<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

// Test dengan user yang berbeda
$admin = User::where('role', 'admin')->first();
$lecturer = User::where('role', 'lecturer')->first(); 
$student = User::where('role', 'student')->first();

$notificationService = new NotificationService();

echo "=== DEBUGGING NOTIFICATION PAGINATION ===\n\n";

if ($admin) {
    echo "ADMIN USER (ID: {$admin->id}):\n";
    $adminNotifications = $notificationService->getUserNotifications($admin, 15);
    echo "Type: " . get_class($adminNotifications) . "\n";
    echo "Count: " . $adminNotifications->count() . "\n";
    echo "Has paginate methods: " . (method_exists($adminNotifications, 'simplePaginate') ? 'YES' : 'NO') . "\n\n";
}

if ($lecturer) {
    echo "LECTURER USER (ID: {$lecturer->id}):\n";
    $lecturerNotifications = $notificationService->getUserNotifications($lecturer, 15);
    echo "Type: " . get_class($lecturerNotifications) . "\n";
    echo "Count: " . $lecturerNotifications->count() . "\n";
    echo "Has paginate methods: " . (method_exists($lecturerNotifications, 'simplePaginate') ? 'YES' : 'NO') . "\n\n";
}

if ($student) {
    echo "STUDENT USER (ID: {$student->id}):\n";
    $studentNotifications = $notificationService->getUserNotifications($student, 15);
    echo "Type: " . get_class($studentNotifications) . "\n";
    echo "Count: " . $studentNotifications->count() . "\n";
    echo "Has paginate methods: " . (method_exists($studentNotifications, 'simplePaginate') ? 'YES' : 'NO') . "\n\n";
}
