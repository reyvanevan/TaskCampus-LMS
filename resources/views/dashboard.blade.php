<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Auth::user()->isAdmin())
                <!-- Admin Dashboard -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-6">System Overview</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-gray-500 text-sm font-medium">Total Courses</h4>
                                        <h3 class="text-3xl font-bold text-gray-700">{{ $courseCount }}</h3>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-gray-500 text-sm font-medium">Active Assignments</h4>
                                        <h3 class="text-3xl font-bold text-gray-700">{{ $activeAssignments }}</h3>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-yellow-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-yellow-500 bg-opacity-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-gray-500 text-sm font-medium">Pending Submissions</h4>
                                        <h3 class="text-3xl font-bold text-gray-700">{{ $pendingSubmissions }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Recent Assignments</h3>
                        
                        @if(count($recentAssignments) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lecturer</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentAssignments as $assignment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $assignment->title }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $assignment->course->code }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $assignment->course->lecturer->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $assignment->deadline->format('M d, Y, H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($assignment->status === 'published')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Published
                                                        </span>
                                                    @elseif($assignment->status === 'draft')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            Draft
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Archived
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('assignments.show', $assignment) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">No recent assignments.</p>
                        @endif
                        
                        <div class="mt-4 text-right">
                            <a href="{{ route('assignments.index') }}" class="text-indigo-600 hover:text-indigo-900">View all assignments →</a>
                        </div>
                    </div>
                </div>
            @elseif(Auth::user()->isLecturer())
                <!-- Lecturer Dashboard -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-6">Teaching Overview</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-gray-500 text-sm font-medium">My Courses</h4>
                                        <h3 class="text-3xl font-bold text-gray-700">{{ $courseCount }}</h3>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-gray-500 text-sm font-medium">Active Assignments</h4>
                                        <h3 class="text-3xl font-bold text-gray-700">{{ $activeAssignments }}</h3>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-yellow-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-yellow-500 bg-opacity-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-gray-500 text-sm font-medium">Pending Submissions</h4>
                                        <h3 class="text-3xl font-bold text-gray-700">{{ $pendingSubmissions }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold mb-4">Upcoming Deadlines</h3>
                            
                            @if(count($upcomingDeadlines) > 0)
                                <div class="space-y-4">
                                    @foreach($upcomingDeadlines as $assignment)
                                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                            <div>
                                                <h4 class="font-medium text-indigo-600">{{ $assignment->title }}</h4>
                                                <p class="text-sm text-gray-600">{{ $assignment->course->code }} - {{ $assignment->course->name }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Due: {{ $assignment->deadline->format('M d, Y, H:i') }}</p>
                                            </div>
                                            <a href="{{ route('assignments.show', $assignment) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No upcoming deadlines.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold mb-4">Recent Submissions</h3>
                            
                            @if(count($recentSubmissions) > 0)
                                <div class="space-y-4">
                                    @foreach($recentSubmissions as $submission)
                                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                            <div>
                                                <h4 class="font-medium">{{ $submission->user->name }}</h4>
                                                <p class="text-sm text-indigo-600">{{ $submission->assignment->title }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Submitted: {{ $submission->created_at->format('M d, Y, H:i') }}</p>
                                            </div>
                                            <div>
                                                @if($submission->status === 'submitted')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">New</span>
                                                @elseif($submission->status === 'late')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-2">Late</span>
                                                @endif
                                                <a href="{{ route('submissions.grade-form', $submission) }}" class="text-indigo-600 hover:text-indigo-900">Grade</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No recent submissions.</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">My Courses</h3>
                            <a href="{{ route('courses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Create Course') }}
                            </a>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach(Auth::user()->teachingCourses as $course)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $course->code }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $course->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $course->semester->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($course->status === 'active')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @elseif($course->status === 'inactive')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Inactive
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Archived
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                                <a href="{{ route('assignments.create') }}?course_id={{ $course->id }}" class="text-green-600 hover:text-green-900">Add Assignment</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <!-- Student Dashboard -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">My Learning</h3>
                            
                            <!-- Export My Grades Button -->
                            <div class="relative inline-block text-left">
                                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" onclick="toggleMyGradesExportDropdown()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Export My Grades
                                    <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                
                                <div id="my-grades-export-dropdown" class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="py-1">
                                        <a href="{{ route('export.student.grades') }}" 
                                           class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4z"/>
                                            </svg>
                                            Export My Grades (CSV)
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-gray-500 text-sm font-medium">Enrolled Courses</h4>
                                        <h3 class="text-3xl font-bold text-gray-700">{{ count($enrolledCourses) }}</h3>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-gray-500 text-sm font-medium">Upcoming Assignments</h4>
                                        <h3 class="text-3xl font-bold text-gray-700">{{ count($upcomingAssignments) }}</h3>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-purple-50 p-6 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-purple-500 bg-opacity-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-gray-500 text-sm font-medium">My Submissions</h4>
                                        <h3 class="text-3xl font-bold text-gray-700">{{ count($recentSubmissions) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold mb-4">Upcoming Assignments</h3>
                            
                            @if(count($upcomingAssignments) > 0)
                                <div class="space-y-4">
                                    @foreach($upcomingAssignments as $assignment)
                                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                            <div>
                                                <h4 class="font-medium text-indigo-600">{{ $assignment->title }}</h4>
                                                <p class="text-sm text-gray-600">{{ $assignment->course->code }} - {{ $assignment->course->name }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Due: {{ $assignment->deadline->format('M d, Y, H:i') }}</p>
                                            </div>
                                            <div>
                                                @php
                                                    $submission = $assignment->submissions->first();
                                                @endphp
                                                @if($submission)
                                                    <div class="flex flex-col items-end space-y-1">
                                                        <div class="flex items-center space-x-2">
                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            <span class="text-xs text-green-600 font-medium">Submitted</span>
                                                        </div>
                                                        <a href="{{ route('submissions.show', $submission) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">View</a>
                                                    </div>
                                                @else
                                                    <div class="flex flex-col items-end space-y-1">
                                                        <span class="text-xs text-orange-600 font-medium">Not Submitted</span>
                                                        @if(!$assignment->isPastDue())
                                                            <a href="{{ route('submissions.create', $assignment) }}" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Submit</a>
                                                        @elseif($assignment->allow_late_submissions)
                                                            <a href="{{ route('submissions.create', $assignment) }}" class="inline-flex items-center px-3 py-1 bg-yellow-600 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">Submit Late</a>
                                                        @else
                                                            <span class="text-xs text-red-600 font-medium">Deadline Passed</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No upcoming assignments.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold mb-4">Graded Assignments</h3>
                            
                            @if(count($gradedSubmissions) > 0)
                                <div class="space-y-4">
                                    @foreach($gradedSubmissions as $submission)
                                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                            <div>
                                                <h4 class="font-medium">{{ $submission->assignment->title }}</h4>
                                                <p class="text-sm text-indigo-600">{{ $submission->assignment->course->code }}</p>
                                                <div class="flex items-center mt-1">
                                                    <span class="text-xs text-gray-500">Score:</span>
                                                    <span class="ml-1 text-sm font-medium">{{ $submission->score }}/{{ $submission->assignment->max_score }}</span>
                                                    <div class="ml-2 w-24 bg-gray-200 rounded-full h-1.5">
                                                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ ($submission->score / $submission->assignment->max_score) * 100 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('submissions.show', $submission) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No graded assignments yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">My Courses</h3>
                            <a href="{{ route('student.enroll.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Enroll in Course') }}
                            </a>
                        </div>
                        
                        @if(count($enrolledCourses) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($enrolledCourses as $course)
                                    <div class="bg-white border border-gray-200 rounded-lg shadow overflow-hidden">
                                        <div class="bg-gray-50 py-3 px-4 border-b border-gray-200">
                                            <div class="flex justify-between items-center">
                                                <h4 class="font-medium">{{ $course->code }}</h4>
                                                @if($course->pivot->status === 'active')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @elseif($course->pivot->status === 'inactive')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Inactive
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Completed
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <h3 class="font-medium text-gray-900 mb-1">{{ $course->name }}</h3>
                                            <p class="text-sm text-gray-500 mb-3">{{ $course->lecturer->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $course->semester->name }}</p>
                                        </div>
                                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-right">
                                            <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                View Course →
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 mb-4">You are not enrolled in any courses.</p>
                                <a href="{{ route('student.enroll.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Enroll Now
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
function toggleMyGradesExportDropdown() {
    const dropdown = document.getElementById('my-grades-export-dropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const isExportButton = event.target.closest('[onclick="toggleMyGradesExportDropdown()"]');
    const isDropdownContent = event.target.closest('#my-grades-export-dropdown');
    
    if (!isExportButton && !isDropdownContent) {
        const dropdown = document.getElementById('my-grades-export-dropdown');
        if (dropdown) {
            dropdown.classList.add('hidden');
        }
    }
});
</script>