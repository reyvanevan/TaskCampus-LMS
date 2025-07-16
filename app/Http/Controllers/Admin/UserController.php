<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filter by role if specified
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }
    
    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }
    
    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,lecturer,student',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(),
        ]);

        // Send notifications
        $this->notificationService->createWelcomeNotification($user);
        $this->notificationService->notifyUserCreated($user, auth()->user());
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully and notifications sent.');
    }
    
    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }
    
    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,lecturer,student',
        ]);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete yourself.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
    
    /**
     * Create sample data for demo
     */
    public function createSampleData()
    {
        try {
            // Create sample lecturers
            $lecturers = [
                ['name' => 'Dr. John Anderson', 'email' => 'john.anderson@taskcampus.edu', 'role' => 'lecturer'],
                ['name' => 'Prof. Sarah Thompson', 'email' => 'sarah.thompson@taskcampus.edu', 'role' => 'lecturer'],
                ['name' => 'Dr. Michael Chen', 'email' => 'michael.chen@taskcampus.edu', 'role' => 'lecturer'],
                ['name' => 'Prof. Lisa Rodriguez', 'email' => 'lisa.rodriguez@taskcampus.edu', 'role' => 'lecturer'],
                ['name' => 'Dr. David Wilson', 'email' => 'david.wilson@taskcampus.edu', 'role' => 'lecturer'],
            ];
            
            // Create sample students
            $students = [
                ['name' => 'Alice Johnson', 'email' => 'alice.johnson@student.taskcampus.edu', 'role' => 'student'],
                ['name' => 'Bob Smith', 'email' => 'bob.smith@student.taskcampus.edu', 'role' => 'student'],
                ['name' => 'Carol Davis', 'email' => 'carol.davis@student.taskcampus.edu', 'role' => 'student'],
                ['name' => 'Daniel Brown', 'email' => 'daniel.brown@student.taskcampus.edu', 'role' => 'student'],
                ['name' => 'Emma Wilson', 'email' => 'emma.wilson@student.taskcampus.edu', 'role' => 'student'],
            ];
            
            $created = 0;
            $errors = [];
            
            foreach (array_merge($lecturers, $students) as $userData) {
                // Check if email already exists
                if (User::where('email', $userData['email'])->exists()) {
                    $errors[] = "User with email {$userData['email']} already exists";
                    continue;
                }
                
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password123'),
                    'role' => $userData['role'],
                    'email_verified_at' => now(),
                ]);

                // Send welcome notification to new user
                $this->notificationService->createWelcomeNotification($user);
                
                $created++;
            }
            
            $message = "Successfully created {$created} sample users.";
            if (!empty($errors)) {
                $message .= " Skipped: " . implode(', ', $errors);
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Error creating sample data: ' . $e->getMessage());
        }
    }
}
