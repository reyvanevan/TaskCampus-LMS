<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Semester') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Update semester information for') }} <span class="font-medium">{{ $semester->name }}</span>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.semesters.show', $semester) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ __('View Semester') }}
                </a>
                <a href="{{ route('admin.semesters.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Semesters') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="px-6 py-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Semester Information</h3>
                        <p class="mt-1 text-sm text-gray-600">Update the details for this semester.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.semesters.update', $semester) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Semester Name')" />
                                <x-text-input 
                                    id="name" 
                                    class="block mt-1 w-full" 
                                    type="text" 
                                    name="name" 
                                    :value="old('name', $semester->name)" 
                                    required 
                                    autofocus 
                                    placeholder="e.g., 2025/2026 - Semester 1"
                                />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500">Enter a descriptive name for the semester including the academic year.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="start_date" :value="__('Start Date')" />
                                    <x-text-input 
                                        id="start_date" 
                                        class="block mt-1 w-full" 
                                        type="date" 
                                        name="start_date" 
                                        :value="old('start_date', $semester->start_date->format('Y-m-d'))" 
                                        required 
                                    />
                                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="end_date" :value="__('End Date')" />
                                    <x-text-input 
                                        id="end_date" 
                                        class="block mt-1 w-full" 
                                        type="date" 
                                        name="end_date" 
                                        :value="old('end_date', $semester->end_date->format('Y-m-d'))" 
                                        required 
                                    />
                                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                                name="is_active" 
                                                value="1" 
                                                id="is_active"
                                                {{ old('is_active', $semester->is_active) ? 'checked' : '' }}
                                            >
                                            <label for="is_active" class="ml-2 block text-sm font-medium text-blue-900">
                                                {{ __('Set as active semester') }}
                                                @if($semester->is_active)
                                                    <span class="text-green-600">(Currently Active)</span>
                                                @endif
                                            </label>
                                        </div>
                                        <p class="mt-1 text-sm text-blue-700">
                                            Only one semester can be active at a time. Setting this will deactivate the current active semester.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if($semester->courses->count() > 0)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.485 3.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 3.495zM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">Associated Courses</h3>
                                            <p class="mt-1 text-sm text-yellow-700">
                                                This semester has {{ $semester->courses->count() }} associated course(s). 
                                                Changing the semester dates may affect course schedules.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-200 space-x-3">
                            <a href="{{ route('admin.semesters.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                {{ __('Update Semester') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>