<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $assignment->title }}
            </h2>
            <div class="flex space-x-2">
                @if(Auth::user()->isAdmin() || (Auth::user()->isLecturer() && $assignment->course->lecturer_id === Auth::id()))
                    <a href="{{ route('assignments.edit', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Assignment
                    </a>
                    
                    <form method="POST" action="{{ route('assignments.destroy', $assignment) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this assignment? This action cannot be undone and will delete all submissions.')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Assignment
                        </button>
                    </form>
                @endif
                
                @if(Auth::user()->isStudent() && $assignment->status === 'published' && !$assignment->isPastDue())
                    <a href="{{ route('submissions.create', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                        {{ __('Submit Assignment') }}
                    </a>
                @endif
                
                <a href="{{ route('assignments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to Assignments') }}
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
                    <div class="block sm:inline">{{ session('error') }}</div>
                    <x-force-delete-option />
                </div>
            @endif

            <!-- Assignment Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Assignment Details</h3>
                            
                            <div class="prose max-w-none">
                                <p class="mb-4">{!! nl2br(e($assignment->description)) !!}</p>
                            </div>
                            
                            @if($assignment->file_path)
                                <div class="mt-4 p-4 bg-gray-50 rounded-md">
                                    <h4 class="font-medium mb-2">Assignment Materials</h4>
                                    <a href="{{ route('assignments.download', $assignment) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                        Download Assignment File
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Overview</h3>
                            
                            <div class="space-y-2">
                                <p><span class="font-medium">Course:</span> 
                                    <a href="{{ route('courses.show', $assignment->course) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $assignment->course->code }} - {{ $assignment->course->name }}
                                    </a>
                                </p>
                                <p><span class="font-medium">Lecturer:</span> {{ $assignment->course->lecturer->name }}</p>
                                <p><span class="font-medium">Deadline:</span> {{ $assignment->deadline->format('M d, Y, H:i') }}</p>
                                <p><span class="font-medium">Maximum Score:</span> {{ $assignment->max_score }} points</p>
                                <p>
                                    <span class="font-medium">Status:</span>
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
                                </p>
                                <p>
                                    <span class="font-medium">Late Submissions:</span>
                                    @if($assignment->allow_late_submissions)
                                        <span class="text-green-600">Allowed</span>
                                    @else
                                        <span class="text-red-600">Not Allowed</span>
                                    @endif
                                </p>
                                <p>
                                    <span class="font-medium">Time Remaining:</span>
                                    @if($assignment->deadline->isPast())
                                        <span class="text-red-600">Deadline has passed</span>
                                    @else
                                        {{ $assignment->deadline->diffForHumans(['parts' => 2]) }}
                                    @endif
                                </p>
                            </div>
                            
                            <!-- Student Submission Status -->
                            @if(Auth::user()->isStudent())
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <h4 class="font-medium mb-2">Your Submission</h4>
                                    @if($submission)
                                        <p>
                                            <span class="font-medium">Status:</span>
                                            @if($submission->status === 'submitted')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Submitted
                                                </span>
                                            @elseif($submission->status === 'late')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Submitted Late
                                                </span>
                                            @elseif($submission->status === 'graded')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Graded
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                    Returned
                                                </span>
                                            @endif
                                        </p>
                                        @if($submission->score !== null)
                                            <p class="mt-2"><span class="font-medium">Score:</span> {{ $submission->score }} / {{ $assignment->max_score }}</p>
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('submissions.show', $submission) }}" class="text-indigo-600 hover:text-indigo-900">
                                                View your submission
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-gray-500">You haven't submitted this assignment yet.</p>
                                        @if(!$assignment->isPastDue())
                                            <div class="mt-3">
                                                <a href="{{ route('submissions.create', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    {{ __('Submit Assignment') }}
                                                </a>
                                            </div>
                                        @elseif($assignment->allow_late_submissions)
                                            <div class="mt-3">
                                                <a href="{{ route('submissions.create', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    {{ __('Submit Late') }}
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Rubric Section -->
            @if(Auth::user()->isAdmin() || (Auth::user()->isLecturer() && $assignment->course->lecturer_id === Auth::id()))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Grading Rubric</h3>
                            
                            @if(!$assignment->rubric)
                                <a href="{{ route('rubrics.create', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Create Rubric') }}
                                </a>
                            @else
                                <div>
                                    <a href="{{ route('rubrics.edit', $assignment->rubric) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                        {{ __('Edit Rubric') }}
                                    </a>
                                    <form method="POST" action="{{ route('rubrics.destroy', $assignment->rubric) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this rubric?')">
                                            {{ __('Delete Rubric') }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        
                        @if($assignment->rubric)
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-700 mb-1">{{ $assignment->rubric->name }}</h4>
                                @if($assignment->rubric->description)
                                    <p class="text-gray-600 mb-4">{{ $assignment->rubric->description }}</p>
                                @endif
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criteria</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($assignment->rubric->criteria as $criteria)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $criteria->title }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $criteria->description }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    {{ $criteria->max_score }} points
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                                    No criteria added to this rubric yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="2" class="px-6 py-3 text-right text-sm font-medium text-gray-700">Total</td>
                                            <td class="px-6 py-3 text-right text-sm font-medium text-gray-700">
                                                {{ $assignment->rubric->getTotalPossibleScore() }} points
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">No rubric has been created for this assignment yet.</p>
                            <p class="text-sm text-gray-500 mt-1">Create a rubric to help standardize grading and provide clear expectations for students.</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Submissions Section (for lecturer/admin) -->
            @if(Auth::user()->isAdmin() || (Auth::user()->isLecturer() && $assignment->course->lecturer_id === Auth::id()))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Student Submissions</h3>
                            
                            <!-- Export Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('export.assignment.submissions', $assignment) }}?format=csv" 
                                   class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    CSV
                                </a>
                                <a href="{{ route('export.assignment.submissions', $assignment) }}?format=excel" 
                                   class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Excel
                                </a>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($submissions as $sub)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $sub->user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $sub->created_at->format('M d, Y, H:i') }}
                                                @if($sub->is_late)
                                                    <span class="text-red-500 text-xs">(Late)</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($sub->status === 'submitted')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Submitted
                                                    </span>
                                                @elseif($sub->status === 'late')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Late
                                                    </span>
                                                @elseif($sub->status === 'graded')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Graded
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                        Returned
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($sub->score !== null)
                                                    {{ $sub->score }} / {{ $assignment->max_score }}
                                                @else
                                                    <span class="text-gray-500">Not graded</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('submissions.show', $sub) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                                
                                                @if($sub->status === 'graded')
                                                    <form method="POST" action="{{ route('submissions.return', $sub) }}" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900">Return to Student</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                                No submissions yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $submissions->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>