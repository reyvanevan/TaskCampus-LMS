<x-app-layout>
    <x-slot name="header">
        <h2 class="fo                                    <a href="{{ route('assignments.download', $assignment) }}" target="_blank" class="ml-2 text-indigo-600 hover:text-indigo-900">
                                        View Current File
                                    </a>semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Assignment') }}: {{ $assignment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('assignments.update', $assignment) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Assignment Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $assignment->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="course_id" :value="__('Course')" />
                            <select id="course_id" name="course_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select a course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ (old('course_id', $assignment->course_id) == $course->id) ? 'selected' : '' }}>
                                        {{ $course->code }} - {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="5" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $assignment->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="deadline" :value="__('Deadline')" />
                                <x-text-input id="deadline" class="block mt-1 w-full" type="datetime-local" name="deadline" :value="old('deadline', $assignment->deadline->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="max_score" :value="__('Maximum Score')" />
                                <x-text-input id="max_score" class="block mt-1 w-full" type="number" name="max_score" :value="old('max_score', $assignment->max_score)" required min="1" />
                                <x-input-error :messages="$errors->get('max_score')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="assignment_file" :value="__('Assignment File (Optional)')" />
                            @if($assignment->file_path)
                                <div class="mb-2">
                                    <span class="text-gray-700">Current file:</span>
                                    <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="ml-2 text-indigo-600 hover:text-indigo-900">
                                        View current file
                                    </a>
                                </div>
                            @endif
                            <input id="assignment_file" type="file" name="assignment_file" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <p class="text-sm text-gray-500 mt-1">Upload a new file to replace the current one, or leave empty to keep the current file.</p>
                            <x-input-error :messages="$errors->get('assignment_file')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="draft" {{ old('status', $assignment->status) == 'draft' ? 'selected' : '' }}>Draft (not visible to students)</option>
                                <option value="published" {{ old('status', $assignment->status) == 'published' ? 'selected' : '' }}>Published (visible to students)</option>
                                <option value="archived" {{ old('status', $assignment->status) == 'archived' ? 'selected' : '' }}>Archived (inactive)</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="allow_late_submissions" value="1" {{ old('allow_late_submissions', $assignment->allow_late_submissions) ? 'checked' : '' }}>
                                <span class="ml-2">{{ __('Allow late submissions') }}</span>
                            </label>
                            <p class="text-sm text-gray-500 mt-1">Students will be allowed to submit after the deadline, but their submission will be marked as late.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('assignments.show', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Assignment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Assignment -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-red-600 mb-4">Danger Zone</h3>
                    
                    <form method="POST" action="{{ route('assignments.destroy', $assignment) }}" onsubmit="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        
                        <p class="mb-4 text-gray-700">Once you delete an assignment, there is no going back. Please be certain.</p>
                        
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Delete Assignment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>