@props(['course', 'role' => 'student', 'enrollment' => null, 'showActions' => true])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
        <!-- Header dengan nama course dan status -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ $course->name }}</h3>
            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                @if($course->status === 'active') bg-green-100 text-green-800
                @elseif($course->status === 'inactive') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst($course->status) }}
            </span>
        </div>
        
        <!-- Course Information -->
        <div class="space-y-2 mb-4">
            <p class="text-sm text-gray-600">
                <span class="font-medium">Code:</span> {{ $course->code }}
            </p>
            
            @if($role !== 'lecturer')
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Lecturer:</span> {{ $course->lecturer->name }}
                </p>
            @endif
            
            <p class="text-sm text-gray-600">
                <span class="font-medium">Semester:</span> {{ $course->semester->name }}
            </p>
            
            @if($role === 'student' && $enrollment)
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Enrolled:</span> {{ $enrollment->created_at->format('M d, Y') }}
                </p>
            @elseif($role === 'lecturer' || $role === 'admin')
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Students:</span> {{ $course->students_count ?? $course->students()->count() }}
                </p>
            @endif
        </div>
        
        @if($showActions)
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <div class="flex flex-wrap gap-2">
                    <!-- View Course Button -->
                    <a href="{{ route('courses.show', $course) }}" 
                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View
                    </a>
                    
                    <!-- Assignments Button -->
                    <a href="{{ route('assignments.index', ['course_id' => $course->id]) }}" 
                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Assignments
                    </a>
                    
                    @if($role === 'lecturer' || $role === 'admin')
                        <!-- Edit Course Button (Lecturer/Admin only) -->
                        <a href="{{ route('courses.edit', $course) }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                        
                        @if($role === 'lecturer')
                            <!-- View Students Button (Lecturer only) -->
                            <a href="{{ route('lecturer.courses.students', $course) }}" 
                               class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                Students
                            </a>
                        @endif
                        
                        <!-- Delete Course Button (Admin/Lecturer only) -->
                        <form method="POST" action="{{ route('courses.destroy', $course) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this course? This will permanently delete all assignments and data.');"
                              class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
                
                @if($role === 'student' && $enrollment)
                    <!-- Unenroll Button (Student only) -->
                    <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}" 
                          onsubmit="return confirm('Are you sure you want to unenroll from this course? This action cannot be undone.');"
                          class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Unenroll
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</div>
