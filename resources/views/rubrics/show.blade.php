<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rubric: {{ $rubric->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Rubric Info -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">Assignment Details</h3>
                        <p class="text-blue-700"><strong>Course:</strong> {{ $rubric->assignment->course->name }}</p>
                        <p class="text-blue-700"><strong>Assignment:</strong> {{ $rubric->assignment->title }}</p>
                        <p class="text-blue-700"><strong>Due Date:</strong> {{ $rubric->assignment->due_date ? $rubric->assignment->due_date->format('M d, Y g:i A') : 'Not set' }}</p>
                        <p class="text-blue-700"><strong>Total Points:</strong> {{ $rubric->total_points }}</p>
                        
                        @if($rubric->description)
                            <div class="mt-3">
                                <p class="text-blue-700"><strong>Description:</strong></p>
                                <p class="text-blue-600 text-sm">{{ $rubric->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Criteria -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Grading Criteria</h3>
                        
                        <div class="space-y-4">
                            @foreach($rubric->criteria as $index => $criteria)
                                <div class="bg-gray-50 p-4 rounded-lg border">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-800">{{ $criteria->title }}</h4>
                                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2 py-1 rounded">
                                            {{ $criteria->max_score }} points
                                        </span>
                                    </div>
                                    
                                    @if($criteria->description)
                                        <p class="text-gray-600 text-sm">{{ $criteria->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-700 font-medium">
                                Total: {{ $rubric->criteria->sum('max_score') }} / {{ $rubric->total_points }} points
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between">
                        <a href="{{ route('assignments.show', $rubric->assignment) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold rounded transition duration-200">
                            ← Back to Assignment
                        </a>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('rubrics.edit', $rubric) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded transition duration-200">
                                Edit Rubric
                            </a>
                            
                            <form method="POST" action="{{ route('rubrics.destroy', $rubric) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this rubric? This action cannot be undone.')" 
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded transition duration-200">
                                    Delete Rubric
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
