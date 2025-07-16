<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Submission') }}: {{ $submission->assignment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Assignment Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="mb-2"><span class="font-medium">Course:</span> {{ $submission->assignment->course->code }} - {{ $submission->assignment->course->name }}</p>
                            <p class="mb-2"><span class="font-medium">Deadline:</span> {{ $submission->assignment->deadline->format('M d, Y, H:i') }}</p>
                            <p class="mb-2">
                                <span class="font-medium">Status:</span>
                                @if($submission->assignment->deadline->isPast())
                                    @if($submission->assignment->allow_late_submissions)
                                        <span class="text-yellow-600">Late Submission Allowed</span>
                                    @else
                                        <span class="text-red-600">Past Due</span>
                                    @endif
                                @else
                                    <span class="text-green-600">Open</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <p class="mb-2"><span class="font-medium">Maximum Score:</span> {{ $submission->assignment->max_score }} points</p>
                            <p class="mb-2">
                                <span class="font-medium">Time Remaining:</span>
                                @if($submission->assignment->deadline->isPast())
                                    <span class="text-red-600">Deadline has passed</span>
                                @else
                                    {{ $submission->assignment->deadline->diffForHumans(['parts' => 2]) }}
                                @endif
                            </p>
                            
                            @if($submission->assignment->file_path)
                                <p class="mt-4">
                                    <a href="{{ route('assignments.download', $submission->assignment) }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                        Download Assignment Instructions
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Edit Your Submission</h3>
                    
                    @if($submission->assignment->deadline->isPast() && !$submission->assignment->allow_late_submissions)
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Deadline has passed</p>
                            <p>The deadline for this assignment has passed and late submissions are not allowed.</p>
                        </div>
                    @elseif($submission->assignment->deadline->isPast())
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Late submission</p>
                            <p>The deadline for this assignment has passed. Your submission will be marked as late.</p>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('submissions.update', $submission) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="current_file" :value="__('Current Submission File')" />
                            <div class="mt-2 p-4 bg-gray-50 rounded-md flex items-center justify-between">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Submitted file</span>
                                </div>
                                <a href="{{ route('submissions.download', $submission) }}" class="text-indigo-600 hover:text-indigo-900">Download Current File</a>
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="submission_file" :value="__('Update Submission File (Optional)')" />
                            <input id="submission_file" type="file" name="submission_file" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <p class="text-sm text-gray-500 mt-1">Upload a new file to replace your current submission, or leave empty to keep your current file.</p>
                            <x-input-error :messages="$errors->get('submission_file')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="comment" :value="__('Comment (Optional)')" />
                            <textarea id="comment" name="comment" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('comment', $submission->comment) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Add any comments or notes about your submission.</p>
                            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('submissions.show', $submission) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Submission') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>