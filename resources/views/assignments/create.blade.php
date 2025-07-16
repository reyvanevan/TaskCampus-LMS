<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Assignment') }}
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
                    <form method="POST" action="{{ route('assignments.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Assignment Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="course_id" :value="__('Course')" />
                            <select id="course_id" name="course_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select a course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ (old('course_id') == $course->id || (isset($selectedCourse) && $selectedCourse->id == $course->id)) ? 'selected' : '' }}>
                                        {{ $course->code }} - {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="5" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="deadline" :value="__('Deadline')" />
                                <x-text-input id="deadline" class="block mt-1 w-full" type="datetime-local" name="deadline" :value="old('deadline')" required />
                                <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="max_score" :value="__('Maximum Score')" />
                                <x-text-input id="max_score" class="block mt-1 w-full" type="number" name="max_score" :value="old('max_score', 100)" required min="1" />
                                <x-input-error :messages="$errors->get('max_score')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="assignment_file" :value="__('Assignment File (Optional)')" />
                            <input id="assignment_file" type="file" name="assignment_file" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <p class="text-sm text-gray-500 mt-1">Upload instructions or additional materials for students (PDF, Word, etc.).</p>
                            <x-input-error :messages="$errors->get('assignment_file')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (not visible to students)</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (visible to students)</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived (inactive)</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="allow_late_submissions" value="1" {{ old('allow_late_submissions') ? 'checked' : '' }}>
                                <span class="ml-2">{{ __('Allow late submissions') }}</span>
                            </label>
                            <p class="text-sm text-gray-500 mt-1">Students will be allowed to submit after the deadline, but their submission will be marked as late.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('assignments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Create Assignment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>