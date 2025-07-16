<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->name }} <span class="text-sm text-gray-600">({{ $course->code }})</span>
            </h2>
            <div class="flex space-x-2">
                @if (Auth::user()->isAdmin() || (Auth::user()->isLecturer() && $course->lecturer_id === Auth::id()))
                    <a href="{{ route('courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Course
                    </a>
                    
                    <form method="POST" action="{{ route('courses.destroy', $course) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this course? This action cannot be undone.')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Course
                        </button>
                    </form>
                @endif
                @if(Auth::user()->isStudent())
                    <a href="{{ route('student.enrollments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-3">
                        {{ __('Back to My Courses') }}
                    </a>
                @elseif(Auth::user()->isLecturer())
                    <a href="{{ route('lecturer.courses.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-3">
                        {{ __('Back to Course Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-3">
                        {{ __('Back to Courses') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <div class="block sm:inline">{{ session('error') }}</div>
                    <x-force-delete-option />
                </div>
            @endif

            @if (session('info'))
                <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Course Details</h3>
                            <p class="mb-1"><span class="font-medium">Course Code:</span> {{ $course->code }}</p>
                            <p class="mb-1"><span class="font-medium">Lecturer:</span> {{ $course->lecturer->name }}</p>
                            <p class="mb-1"><span class="font-medium">Semester:</span> {{ $course->semester->name }}</p>
                            <p class="mb-1">
                                <span class="font-medium">Status:</span>
                                @if ($course->status === 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @elseif ($course->status === 'inactive')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Inactive
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Archived
                                    </span>
                                @endif
                            </p>
                        </div>
                        
                        @if (Auth::user()->isAdmin() || (Auth::user()->isLecturer() && $course->lecturer_id === Auth::id()))
                            <div class="border-t md:border-t-0 md:border-l border-gray-200 pt-4 md:pt-0 md:pl-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Enrollment Code</h3>
                                <div class="flex items-center mb-4">
                                    <div class="border rounded-md px-4 py-2 bg-gray-50 font-mono tracking-wider">
                                        {{ $course->enrollment_code }}
                                    </div>
                                    <form method="POST" action="{{ route('courses.regenerateCode', $course) }}" class="ml-3">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 border border-indigo-500 rounded-md text-xs text-indigo-700 hover:bg-indigo-50" onclick="return confirm('Are you sure? This will invalidate the current enrollment code.')">
                                            Regenerate
                                        </button>
                                    </form>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Share this code with students to allow them to enroll in this course.</p>
                                
                                <!-- Export Options -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h4 class="text-md font-semibold text-gray-700 mb-3">Export Data</h4>
                                    
                                    <!-- Export Grades -->
                                    <div class="mb-3">
                                        <h5 class="text-sm font-medium text-gray-600 mb-2">Export Grades</h5>
                                        <div class="flex gap-2">
                                            <a href="{{ route('export.course.grades', $course) }}?format=csv" 
                                               class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                CSV
                                            </a>
                                            <a href="{{ route('export.course.grades', $course) }}?format=excel" 
                                               class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Excel
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Export Students -->
                                    <div class="mb-3">
                                        <h5 class="text-sm font-medium text-gray-600 mb-2">Export Students</h5>
                                        <div class="flex gap-2">
                                            <a href="{{ route('export.course.students', $course) }}?format=csv" 
                                               class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                CSV
                                            </a>
                                            <a href="{{ route('export.course.students', $course) }}?format=excel" 
                                               class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Excel
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <p class="text-xs text-gray-500 mt-2">
                                        <strong>Note:</strong> Both CSV and Excel formats contain the same data. 
                                        CSV format is recommended for best compatibility with Excel and other spreadsheet applications.
                                        Excel format is optimized for direct opening in Microsoft Excel.
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        @if (Auth::user()->isStudent() && isset($enrollment))
                            <div class="border-t md:border-t-0 md:border-l border-gray-200 pt-4 md:pt-0 md:pl-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Your Enrollment</h3>
                                <p class="mb-1">
                                    <span class="font-medium">Status:</span>
                                    @if ($enrollment->status === 'active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @elseif ($enrollment->status === 'inactive')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Inactive
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Completed
                                        </span>
                                    @endif
                                </p>
                                <p class="mb-1"><span class="font-medium">Enrolled on:</span> {{ $enrollment->created_at->format('M d, Y') }}</p>
                                
                                @if ($enrollment->status !== 'completed')
                                    <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}" class="mt-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to unenroll from this course?')">
                                            Unenroll from this course
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    @if ($course->description)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Course Description</h3>
                            <div class="prose max-w-none">
                                {{ $course->description }}
                            </div>
                        </div>
                    @endif

                    @if (Auth::user()->isStudent())
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h3>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    View Assignments
                                </a>
                                <a href="{{ route('submissions.my', ['course_id' => $course->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    My Submissions
                                </a>
                                <a href="{{ route('export.student.grades') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Export My Grades
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if (Auth::user()->isAdmin() || (Auth::user()->isLecturer() && $course->lecturer_id === Auth::id()))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Enrolled Students</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled On</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($enrolledStudents as $student)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $student->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $student->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $student->pivot->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($student->pivot->status === 'active')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @elseif ($student->pivot->status === 'inactive')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Inactive
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Completed
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($student->pivot && $student->pivot->id)
                                                    <!-- Activate Button -->
                                                    <form method="POST" action="{{ route('enrollments.update', $student->pivot->id) }}" class="inline-block mr-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 text-green-800 text-sm rounded-md transition-colors duration-200"
                                                                title="Activate Student">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Deactivate Button -->
                                                    <form method="POST" action="{{ route('enrollments.update', $student->pivot->id) }}" class="inline-block mr-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="inactive">
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-sm rounded-md transition-colors duration-200"
                                                                title="Deactivate Student">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 4h4.018a2 2 0 01.485.06l3.76.94m2.28 5.52a2 2 0 01-.921 3.74H16M10 14v6m0-6l2-2m-2 2l-2-2" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Complete Button -->
                                                    <form method="POST" action="{{ route('enrollments.update', $student->pivot->id) }}" class="inline-block mr-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-sm rounded-md transition-colors duration-200"
                                                                title="Mark as Completed">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Remove Button -->
                                                    <form method="POST" action="{{ route('enrollments.destroy', $student->pivot->id) }}" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-800 text-sm rounded-md transition-colors duration-200"
                                                                title="Remove Student"
                                                                onclick="return confirm('Are you sure you want to remove this student from the course?')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-500 text-sm">No actions available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                                No students enrolled in this course yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if ($enrolledStudents->count() > 0)
                            <div class="mt-4">
                                {{ $enrolledStudents->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>