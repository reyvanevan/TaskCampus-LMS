<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Management') }}: {{ $course->name }} <span class="text-sm text-gray-600">({{ $course->code }})</span>
            </h2>
            <div>
                <a href="{{ route('lecturer.courses.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to Dashboard') }}
                </a>
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
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Course Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-700 mb-1">Enrollment Code</h3>
                            <div class="flex items-center">
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
                            <p class="text-xs text-gray-500 mt-1">Share this code with students to allow them to enroll</p>
                        </div>

                        <div>
                            <h3 class="text-base font-semibold text-gray-700 mb-1">Enrollment Status</h3>
                            <div class="grid grid-cols-3 gap-3 mt-2">
                                <div class="bg-green-50 p-3 rounded-lg text-center">
                                    <p class="font-semibold text-xl text-green-700">{{ $activeCount }}</p>
                                    <p class="text-xs text-gray-600">Active</p>
                                </div>
                                <div class="bg-yellow-50 p-3 rounded-lg text-center">
                                    <p class="font-semibold text-xl text-yellow-700">{{ $inactiveCount }}</p>
                                    <p class="text-xs text-gray-600">Inactive</p>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg text-center">
                                    <p class="font-semibold text-xl text-blue-700">{{ $completedCount }}</p>
                                    <p class="text-xs text-gray-600">Completed</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-base font-semibold text-gray-700 mb-1">Course Status</h3>
                            <form method="POST" action="{{ route('courses.update', $course) }}" class="mt-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $course->name }}">
                                <input type="hidden" name="code" value="{{ $course->code }}">
                                <input type="hidden" name="description" value="{{ $course->description }}">
                                <input type="hidden" name="lecturer_id" value="{{ $course->lecturer_id }}">
                                <input type="hidden" name="semester_id" value="{{ $course->semester_id }}">
                                
                                <div class="flex items-center space-x-2">
                                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="active" {{ $course->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $course->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="archived" {{ $course->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                    <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        Update Status
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Enrolled Students</h3>
                        <div>
                            <form action="{{ route('lecturer.courses.students', $course) }}" method="GET" class="flex space-x-2">
                                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <input type="text" name="search" placeholder="Search by name or email" value="{{ request('search') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">Filter</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled On</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submissions</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($students as $student)
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
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $student->submission_count ?? 0 }} / {{ $assignmentCount }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <form method="POST" action="{{ route('enrollments.update', $student->pivot->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="text-green-600 hover:text-green-900 px-2 py-1 rounded-md border border-green-200 hover:bg-green-50">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('enrollments.update', $student->pivot->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="inactive">
                                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded-md border border-yellow-200 hover:bg-yellow-50">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('enrollments.update', $student->pivot->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded-md border border-blue-200 hover:bg-blue-50">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('enrollments.destroy', $student->pivot->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded-md border border-red-200 hover:bg-red-50" onclick="return confirm('Are you sure you want to remove this student from the course?')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No students enrolled in this course yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($students->count() > 0)
                        <div class="mt-4">
                            {{ $students->appends(request()->except('page'))->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
