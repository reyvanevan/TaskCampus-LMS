<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit Assignment') }}: {{ $assignment->title }}
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
                            <p class="mb-2"><span class="font-medium">Course:</span> {{ $assignment->course->code }} - {{ $assignment->course->name }}</p>
                            <p class="mb-2"><span class="font-medium">Deadline:</span> {{ $assignment->deadline->format('M d, Y, H:i') }}</p>
                            <p class="mb-2">
                                <span class="font-medium">Status:</span>
                                @if($assignment->deadline->isPast())
                                    @if($assignment->allow_late_submissions)
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
                            <p class="mb-2"><span class="font-medium">Maximum Score:</span> {{ $assignment->max_score }} points</p>
                            <p class="mb-2">
                                <span class="font-medium">Time Remaining:</span>
                                @if($assignment->deadline->isPast())
                                    <span class="text-red-600">Deadline has passed</span>
                                @else
                                    {{ $assignment->deadline->diffForHumans(['parts' => 2]) }}
                                @endif
                            </p>
                            
                            @if($assignment->file_path)
                                <p class="mt-4">
                                    <a href="{{ route('assignments.download', $assignment) }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
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
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Your Submission</h3>
                    
                    @if($assignment->deadline->isPast() && !$assignment->allow_late_submissions)
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Deadline has passed</p>
                            <p>The deadline for this assignment has passed and late submissions are not allowed.</p>
                        </div>
                    @elseif($assignment->deadline->isPast())
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Late submission</p>
                            <p>The deadline for this assignment has passed. Your submission will be marked as late.</p>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('submissions.store', $assignment) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="submission_file" :value="__('Submission File')" />
                            <div x-data="{ fileName: null, uploading: false }" 
                                 class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                                 x-on:dragover.prevent="$el.classList.add('border-indigo-500'); $el.classList.add('ring-2'); $el.classList.add('ring-indigo-500')"
                                 x-on:dragleave.prevent="$el.classList.remove('border-indigo-500'); $el.classList.remove('ring-2'); $el.classList.remove('ring-indigo-500')"
                                 x-on:drop.prevent="
                                    $el.classList.remove('border-indigo-500');
                                    $el.classList.remove('ring-2');
                                    $el.classList.remove('ring-indigo-500');
                                    uploading = true;
                                    setTimeout(() => {
                                        fileName = $event.dataTransfer.files[0].name;
                                        document.getElementById('submission_file_input').files = $event.dataTransfer.files;
                                        uploading = false;
                                    }, 500)
                                 ">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div x-show="!fileName" class="flex text-sm text-gray-600">
                                        <label for="submission_file_input" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="submission_file_input" name="submission_file" type="file" class="sr-only" 
                                                required x-on:change="fileName = $event.target.files[0].name">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <div x-show="fileName" class="text-sm text-gray-800">
                                        <span class="font-medium" x-text="fileName"></span>
                                        <button type="button" x-on:click="fileName = null; document.getElementById('submission_file_input').value = ''" 
                                            class="ml-2 text-indigo-600 hover:text-indigo-500">
                                            Change
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PDF, DOC, DOCX, PPT, XLS, ZIP, RAR, TXT, JPG, PNG (Max 10MB)
                                    </p>
                                    <div x-show="uploading" class="w-full mt-2">
                                        <div class="h-1 bg-indigo-500 rounded-full animate-pulse"></div>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('submission_file')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="comment" :value="__('Comment (Optional)')" />
                            <textarea id="comment" name="comment" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('comment') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Add any comments or notes about your submission.</p>
                            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('assignments.show', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Submit Assignment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>