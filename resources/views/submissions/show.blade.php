<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Submission Details') }}: {{ $submission->assignment->title }}
            </h2>
            <div class="flex">
                @if(Auth::user()->isStudent() && Auth::id() === $submission->user_id && !in_array($submission->status, ['graded', 'returned']) && !$submission->assignment->isPastDue())
                    <a href="{{ route('submissions.edit', $submission) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                        {{ __('Edit Submission') }}
                    </a>
                @endif
                
                <a href="{{ route('assignments.show', $submission->assignment) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to Assignment') }}
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

            <!-- Submission Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Submission Overview</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="mb-2"><span class="font-medium">Assignment:</span> {{ $submission->assignment->title }}</p>
                                    <p class="mb-2"><span class="font-medium">Course:</span> {{ $submission->assignment->course->code }} - {{ $submission->assignment->course->name }}</p>
                                    <p class="mb-2"><span class="font-medium">Student:</span> {{ $submission->user->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="mb-2"><span class="font-medium">Submitted:</span> {{ $submission->created_at->format('M d, Y, H:i') }}</p>
                                    <p class="mb-2"><span class="font-medium">Deadline:</span> {{ $submission->assignment->deadline->format('M d, Y, H:i') }}</p>
                                    <p class="mb-2">
                                        <span class="font-medium">Submission Status:</span>
                                        @if($submission->status === 'submitted')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Submitted
                                            </span>
                                        @elseif($submission->status === 'late')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Late
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
                                        
                                        @if($submission->is_late)
                                            <span class="ml-2 text-yellow-600 text-xs">(Submitted after deadline)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <h4 class="font-medium mb-2">Submission File</h4>
                                <div class="p-4 bg-gray-50 rounded-md flex items-center justify-between">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>{{ $submission->original_filename ?: basename($submission->file_path) }}</span>
                                    </div>
                                    <a href="{{ route('submissions.download', $submission) }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                            
                            @if($submission->comment)
                                <div class="mt-6">
                                    <h4 class="font-medium mb-2">Student Comment</h4>
                                    <div class="p-4 bg-gray-50 rounded-md">
                                        {!! nl2br(e($submission->comment)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Grading</h3>
                            
                            @if(in_array($submission->status, ['graded', 'returned']))
                                <div class="mb-4">
                                    <p class="text-2xl font-bold text-center mb-2">
                                        {{ $submission->score }} / {{ $submission->assignment->max_score }}
                                    </p>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($submission->score / $submission->assignment->max_score) * 100 }}%"></div>
                                    </div>
                                    <p class="text-center text-gray-500 text-sm">
                                        {{ round(($submission->score / $submission->assignment->max_score) * 100) }}%
                                    </p>
                                </div>
                                
                                @if($submission->feedback)
                                    <div class="mt-6">
                                        <h4 class="font-medium mb-2">Feedback</h4>
                                        <div class="p-4 bg-white rounded-md border border-gray-200">
                                            {!! nl2br(e($submission->feedback)) !!}
                                        </div>
                                    </div>
                                @endif
                            @else
                                @if(Auth::user()->isAdmin() || (Auth::user()->isLecturer() && $submission->assignment->course->lecturer_id === Auth::id()))
                                    <div class="text-center mb-4 p-2 bg-yellow-50 rounded-md">
                                        <p class="text-yellow-700">This submission has not been graded yet.</p>
                                    </div>
                                @else
                                    <div class="text-center p-4 bg-blue-50 rounded-md">
                                        <p class="text-blue-700">Your submission is awaiting grading.</p>
                                    </div>
                                @endif
                            @endif
                            
                            <!-- Grading actions for lecturer/admin -->
                            @if(Auth::user()->isAdmin() || (Auth::user()->isLecturer() && $submission->assignment->course->lecturer_id === Auth::id()))
                                @if($submission->status === 'graded')
                                    <div class="mt-4">
                                        <form method="POST" action="{{ route('submissions.return', $submission) }}">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Return to Student
                                            </button>
                                        </form>
                                    </div>
                                @elseif(!in_array($submission->status, ['returned']))
                                    <div class="mt-4">
                                        <a href="{{ route('submissions.grade-form', $submission) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Grade Submission
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Rubric Scores (if graded with rubric) -->
            @if(in_array($submission->status, ['graded', 'returned']) && $rubric && count($criteriaScores) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Rubric Assessment</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criteria</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comment</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($rubric->criteria as $criteria)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium">
                                                {{ $criteria->title }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $criteria->description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if(isset($criteriaScores[$criteria->id]))
                                                    {{ $criteriaScores[$criteria->id]->score }} / {{ $criteria->max_score }}
                                                @else
                                                    <span class="text-gray-400">Not scored</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if(isset($criteriaScores[$criteria->id]) && $criteriaScores[$criteria->id]->comment)
                                                    {{ $criteriaScores[$criteria->id]->comment }}
                                                @else
                                                    <span class="text-gray-400">No comment</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="2" class="px-6 py-3 text-right font-medium text-gray-700">Total</td>
                                        <td class="px-6 py-3 text-center font-medium text-gray-700">
                                            {{ $submission->score }} / {{ $rubric->getTotalPossibleScore() }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Grading Form for lecturer/admin -->
            @if((Auth::user()->isAdmin() || (Auth::user()->isLecturer() && $submission->assignment->course->lecturer_id === Auth::id())) && !in_array($submission->status, ['graded', 'returned']))
                <div id="grade-submission" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Grade Submission</h3>
                        
                        <form method="POST" action="{{ route('submissions.grade', $submission) }}">
                            @csrf
                            
                            @if($rubric)
                                <div class="mb-6">
                                    <h4 class="font-medium text-gray-700 mb-2">Rubric: {{ $rubric->name }}</h4>
                                    
                                    <div class="space-y-4">
                                        @foreach ($rubric->criteria as $index => $criteria)
                                            <div class="p-4 border border-gray-200 rounded-md">
                                                <div class="flex justify-between items-center mb-2">
                                                    <h5 class="font-medium">{{ $criteria->title }}</h5>
                                                    <span class="text-gray-500 text-sm">Max: {{ $criteria->max_score }} points</span>
                                                </div>
                                                
                                                @if($criteria->description)
                                                    <p class="text-gray-600 mb-3">{{ $criteria->description }}</p>
                                                @endif
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="criteria_score_{{ $criteria->id }}" class="block text-sm font-medium text-gray-700">Score</label>
                                                        <input type="number" id="criteria_score_{{ $criteria->id }}" name="criteria_score[{{ $criteria->id }}]" min="0" max="{{ $criteria->max_score }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    </div>
                                                    
                                                    <div>
                                                        <label for="criteria_comment_{{ $criteria->id }}" class="block text-sm font-medium text-gray-700">Comment (Optional)</label>
                                                        <textarea id="criteria_comment_{{ $criteria->id }}" name="criteria_comment[{{ $criteria->id }}]" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mb-4">
                                <x-input-label for="score" :value="__('Overall Score')" />
                                <x-text-input id="score" class="block mt-1 w-full" type="number" name="score" :value="old('score')" min="0" max="{{ $submission->assignment->max_score }}" step="0.01" required />
                                <p class="text-sm text-gray-500 mt-1">Enter the overall score out of {{ $submission->assignment->max_score }}.</p>
                                <x-input-error :messages="$errors->get('score')" class="mt-2" />
                            </div>
                            
                            <div class="mb-4">
                                <x-input-label for="feedback" :value="__('Feedback')" />
                                <textarea id="feedback" name="feedback" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('feedback') }}</textarea>
                                <p class="text-sm text-gray-500 mt-1">Provide feedback for the student regarding their submission.</p>
                                <x-input-error :messages="$errors->get('feedback')" class="mt-2" />
                            </div>
                            
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>
                                    {{ __('Grade Submission') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>