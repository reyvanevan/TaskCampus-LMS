<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $data = [];
        
        // Check if user is admin
        if ($user->isAdmin()) {
            // For admin, show system stats
            $data['courseCount'] = Course::count();
            
            // Only try to count assignments if table exists
            if (Schema::hasTable('assignments')) {
                try {
                    $data['activeAssignments'] = Assignment::where('status', 'published')->count();
                } catch (QueryException $e) {
                    $data['activeAssignments'] = 0;
                }
            } else {
                $data['activeAssignments'] = 0;
            }
            
            // Only try to count submissions if table exists
            if (Schema::hasTable('submissions')) {
                try {
                    $data['pendingSubmissions'] = Submission::whereIn('status', ['submitted', 'late'])->count();
                } catch (QueryException $e) {
                    $data['pendingSubmissions'] = 0;
                }
            } else {
                $data['pendingSubmissions'] = 0;
            }
            
            // Recent assignments
            if (Schema::hasTable('assignments')) {
                try {
                    $data['recentAssignments'] = Assignment::with(['course', 'course.lecturer'])
                                                    ->orderBy('created_at', 'desc')
                                                    ->limit(5)
                                                    ->get();
                } catch (QueryException $e) {
                    $data['recentAssignments'] = collect([]);
                }
            } else {
                $data['recentAssignments'] = collect([]);
            }
        } 
        // Check if user is lecturer
        elseif ($user->isLecturer()) {
            // For lecturer, show their courses and assignments
            $data['courseCount'] = $user->teachingCourses()->count();
            
            // Only try to count assignments if table exists
            if (Schema::hasTable('assignments')) {
                try {
                    $data['activeAssignments'] = Assignment::whereHas('course', function($q) use ($user) {
                                                    $q->where('lecturer_id', $user->id);
                                                })
                                                ->where('status', 'published')
                                                ->count();
                } catch (QueryException $e) {
                    $data['activeAssignments'] = 0;
                }
            } else {
                $data['activeAssignments'] = 0;
            }
            
            // Only try to count submissions if table exists
            if (Schema::hasTable('submissions') && Schema::hasTable('assignments')) {
                try {
                    $data['pendingSubmissions'] = Submission::whereHas('assignment.course', function($q) use ($user) {
                                                    $q->where('lecturer_id', $user->id);
                                                })
                                                ->whereIn('status', ['submitted', 'late'])
                                                ->count();
                } catch (QueryException $e) {
                    $data['pendingSubmissions'] = 0;
                }
            } else {
                $data['pendingSubmissions'] = 0;
            }
            
            // Upcoming assignments deadlines
            if (Schema::hasTable('assignments')) {
                try {
                    $data['upcomingDeadlines'] = Assignment::whereHas('course', function($q) use ($user) {
                                                    $q->where('lecturer_id', $user->id);
                                                })
                                                ->where('status', 'published')
                                                ->where('deadline', '>', now())
                                                ->orderBy('deadline', 'asc')
                                                ->limit(5)
                                                ->with('course')
                                                ->get();
                } catch (QueryException $e) {
                    $data['upcomingDeadlines'] = collect([]);
                }
            } else {
                $data['upcomingDeadlines'] = collect([]);
            }
            
            // Recent submissions
            if (Schema::hasTable('submissions') && Schema::hasTable('assignments')) {
                try {
                    $data['recentSubmissions'] = Submission::whereHas('assignment.course', function($q) use ($user) {
                                                    $q->where('lecturer_id', $user->id);
                                                })
                                                ->orderBy('created_at', 'desc')
                                                ->limit(5)
                                                ->with(['user', 'assignment'])
                                                ->get();
                } catch (QueryException $e) {
                    $data['recentSubmissions'] = collect([]);
                }
            } else {
                $data['recentSubmissions'] = collect([]);
            }
        } 
        // If user is student
        else {
            // For student, show enrolled courses and assignments
            $data['enrolledCourses'] = $user->enrolledCourses()
                                        ->where('courses.status', 'active')
                                        ->withPivot('status')
                                        ->wherePivot('status', 'active')
                                        ->get();
            
            // Upcoming assignments
            if (Schema::hasTable('assignments')) {
                try {
                    $data['upcomingAssignments'] = Assignment::whereHas('course.students', function($q) use ($user) {
                                                    $q->where('users.id', $user->id);
                                                })
                                                ->where('status', 'published')
                                                ->where('deadline', '>', now())
                                                ->orderBy('deadline', 'asc')
                                                ->limit(5)
                                                ->with(['course', 'submissions' => function($q) use ($user) {
                                                    $q->where('user_id', $user->id);
                                                }])
                                                ->get();
                } catch (QueryException $e) {
                    $data['upcomingAssignments'] = collect([]);
                }
            } else {
                $data['upcomingAssignments'] = collect([]);
            }
            
            // Recent submissions
            if (Schema::hasTable('submissions')) {
                try {
                    $data['recentSubmissions'] = $user->submissions()
                                                ->orderBy('created_at', 'desc')
                                                ->limit(5)
                                                ->with(['assignment', 'assignment.course'])
                                                ->get();
                } catch (QueryException $e) {
                    $data['recentSubmissions'] = collect([]);
                }
            } else {
                $data['recentSubmissions'] = collect([]);
            }
            
            // Graded assignments
            if (Schema::hasTable('submissions')) {
                try {
                    $data['gradedSubmissions'] = $user->submissions()
                                                ->whereIn('status', ['graded', 'returned'])
                                                ->orderBy('updated_at', 'desc')
                                                ->limit(5)
                                                ->with(['assignment', 'assignment.course'])
                                                ->get();
                } catch (QueryException $e) {
                    $data['gradedSubmissions'] = collect([]);
                }
            } else {
                $data['gradedSubmissions'] = collect([]);
            }
        }
        
        return view('dashboard', $data);
    }
}