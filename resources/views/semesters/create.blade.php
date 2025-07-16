<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Create New Semester') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Add a new academic semester to the system') }}
                </p>
            </div>
            <a href="{{ route('admin.semesters.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back to Semesters') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="px-6 py-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Semester Information</h3>
                        <p class="mt-1 text-sm text-gray-600">Please provide the details for the new semester.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.semesters.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Semester Name')" />
                                <x-text-input 
                                    id="name" 
                                    class="block mt-1 w-full" 
                                    type="text" 
                                    name="name" 
                                    :value="old('name')" 
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
                                        :value="old('start_date')" 
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
                                        :value="old('end_date')" 
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
                                                {{ old('is_active') ? 'checked' : '' }}
                                            >
                                            <label for="is_active" class="ml-2 block text-sm font-medium text-blue-900">
                                                {{ __('Set as active semester') }}
                                            </label>
                                        </div>
                                        <p class="mt-1 text-sm text-blue-700">
                                            Only one semester can be active at a time. Setting this will deactivate the current active semester.
                                        </p>
                                    </div>
                                </div>
                            </div>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('Create Semester') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>