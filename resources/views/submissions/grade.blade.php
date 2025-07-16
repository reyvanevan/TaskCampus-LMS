<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Grade Submission') }}
        </h2>
    </x-slot>
    
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Grade Submission</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $submission->assignment->title }} - {{ $submission->user->name }}
                        </p>
                    </div>
                    <a href="{{ route('submissions.show', $submission) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Back to Submission
                    </a>
                </div>

                <!-- Submission Info Card -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <div class="lg:col-span-2">
                        <!-- Student Info -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Student Information</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Name:</span> {{ $submission->user->name }}
                                </div>
                                <div>
                                    <span class="font-medium">Email:</span> {{ $submission->user->email }}
                                </div>
                                <div>
                                    <span class="font-medium">Submitted:</span> {{ $submission->created_at->format('M d, Y H:i') }}
                                </div>
                                <div>
                                    <span class="font-medium">Status:</span> 
                                    <span class="px-2 py-1 text-xs rounded-full {{ $submission->getStatusBadge() }}">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Submission File -->
                        @if($submission->file_path)
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Submission File</h3>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ $submission->original_filename ?: basename($submission->file_path) }}</span>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('submissions.download', $submission) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Submission Comment -->
                        @if($submission->comment)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Student Comment</h3>
                            <p class="text-sm text-gray-700">{{ $submission->comment }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Assignment Info -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Assignment Details</h3>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="font-medium">Course:</span>
                                    <br>{{ $submission->assignment->course->code }} - {{ $submission->assignment->course->name }}
                                </div>
                                <div>
                                    <span class="font-medium">Due Date:</span>
                                    <br>{{ $submission->assignment->deadline->format('M d, Y H:i') }}
                                </div>
                                <div>
                                    <span class="font-medium">Max Score:</span>
                                    <br>{{ $submission->assignment->max_score }} points
                                </div>
                                @if($submission->is_late)
                                <div class="text-red-600">
                                    <span class="font-medium">⚠️ Late Submission</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grading Form -->
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Grading</h3>
                        
                        <form method="POST" action="{{ route('submissions.grade', $submission) }}" x-data="gradingForm()">
                            @csrf
                            
                            @if($rubric && count($rubric->criteria) > 0)
                                <!-- Rubric-based Grading -->
                                <div class="mb-8">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-lg font-medium text-gray-900">{{ $rubric->name }}</h4>
                                        <div class="text-sm text-gray-600">
                                            Total: <span x-text="totalScore"></span> / {{ $rubric->getTotalPossibleScore() }} points
                                        </div>
                                    </div>
                                    
                                    @if($rubric->description)
                                        <p class="text-gray-600 mb-4">{{ $rubric->description }}</p>
                                    @endif
                                    
                                    <div class="space-y-6">
                                        @foreach ($rubric->criteria->sortBy('order') as $index => $criteria)
                                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                                <div class="flex justify-between items-start mb-3">
                                                    <div>
                                                        <h5 class="text-lg font-medium text-gray-900">{{ $criteria->title }}</h5>
                                                        @if($criteria->description)
                                                            <p class="text-gray-600 mt-1">{{ $criteria->description }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-sm font-medium text-gray-700">
                                                            <span x-text="criteriaScores[{{ $criteria->id }}] || 0"></span> / {{ $criteria->max_score }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">points</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="criteria_score_{{ $criteria->id }}" class="block text-sm font-medium text-gray-700 mb-1">Score</label>
                                                        <input 
                                                            type="number" 
                                                            id="criteria_score_{{ $criteria->id }}" 
                                                            name="criteria_score[{{ $criteria->id }}]" 
                                                            min="0" 
                                                            max="{{ $criteria->max_score }}" 
                                                            step="0.01" 
                                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                            x-model.number="criteriaScores[{{ $criteria->id }}]"
                                                            required>
                                                        <div class="mt-1 flex justify-between text-xs text-gray-500">
                                                            <span>Min: 0</span>
                                                            <span>Max: {{ $criteria->max_score }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div>
                                                        <label for="criteria_comment_{{ $criteria->id }}" class="block text-sm font-medium text-gray-700 mb-1">Feedback</label>
                                                        <textarea 
                                                            id="criteria_comment_{{ $criteria->id }}" 
                                                            name="criteria_comment[{{ $criteria->id }}]" 
                                                            rows="3" 
                                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                            placeholder="Optional feedback for this criteria..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Overall Score and Feedback -->
                            <div class="border-t pt-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <label for="score" class="block text-sm font-medium text-gray-700 mb-1">Overall Score</label>
                                        <div class="relative">
                                            <input 
                                                type="number" 
                                                id="score" 
                                                name="score" 
                                                min="0" 
                                                max="{{ $submission->assignment->max_score }}" 
                                                step="0.01" 
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-20"
                                                x-model.number="overallScore"
                                                required>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-sm text-gray-500">
                                                / {{ $submission->assignment->max_score }}
                                            </div>
                                        </div>
                                        @if($rubric && count($rubric->criteria) > 0)
                                            <button 
                                                type="button" 
                                                @click="overallScore = totalScore" 
                                                class="mt-2 text-xs text-indigo-600 hover:text-indigo-800 underline">
                                                Use rubric total (x-text="totalScore")
                                            </button>
                                        @endif
                                        <div class="mt-1 text-xs text-gray-500">
                                            Enter the final score out of {{ $submission->assignment->max_score }} points
                                        </div>
                                        @error('score')
                                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">Overall Feedback</label>
                                        <textarea 
                                            id="feedback" 
                                            name="feedback" 
                                            rows="4" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Provide overall feedback for the student...">{{ old('feedback') }}</textarea>
                                        <div class="mt-1 text-xs text-gray-500">
                                            This feedback will be visible to the student
                                        </div>
                                        @error('feedback')
                                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between mt-8 pt-6 border-t">
                                <div class="text-sm text-gray-600">
                                    <strong>Preview:</strong> Score <span x-text="overallScore || 0"></span>/{{ $submission->assignment->max_score }} 
                                    (<span x-text="Math.round(((overallScore || 0) / {{ $submission->assignment->max_score }}) * 100)"></span>%)
                                </div>
                                <div class="flex space-x-3">
                                    <button 
                                        type="button" 
                                        onclick="window.location.href='{{ route('submissions.show', $submission) }}'"
                                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                        Cancel
                                    </button>
                                    <button 
                                        type="submit"
                                        class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                        Submit Grade
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function gradingForm() {
    return {
        criteriaScores: {},
        overallScore: 0,
        
        get totalScore() {
            return Object.values(this.criteriaScores).reduce((sum, score) => sum + (parseFloat(score) || 0), 0);
        }
    }
}
</script>
</x-app-layout>
