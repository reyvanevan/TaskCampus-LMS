<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Data from CSV') }}
        </h2>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Import Students -->                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Import Students</h3>
                            <p class="text-sm text-gray-600 mb-4">Upload CSV file to bulk import student accounts.</p>
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <p class="text-sm text-blue-700">
                                    <strong>Note:</strong> Please use CSV format. If you have Excel file, save it as CSV first.
                                </p>
                            </div>
                            
                            <!-- Download Template -->
                            <div class="mb-4 space-y-2">
                                <a href="{{ route('import.template.students') }}" 
                                   class="inline-flex items-center px-3 py-2 border border-blue-300 rounded-md text-sm text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Empty Template
                                </a>
                                <a href="{{ route('import.sample.students') }}" 
                                   class="inline-flex items-center px-3 py-2 border border-green-300 rounded-md text-sm text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Sample Data (10 Students)
                                </a>
                            </div>

                        <!-- Upload Form -->
                        <form action="{{ route('import.students') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label for="students_file" class="block text-sm font-medium text-gray-700 mb-2">CSV File</label>
                                <input type="file" 
                                       name="file" 
                                       id="students_file"
                                       accept=".csv,.txt"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">Supported formats: .csv, .txt (Max: 2MB)</p>
                            </div>
                            
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Import Students
                            </button>
                        </form>

                        <!-- Format Info -->
                        <div class="mt-4 p-3 bg-gray-50 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">CSV Format:</h4>
                            <ul class="text-xs text-gray-600 space-y-1">
                                <li>• Column A: <strong>name</strong> - Student full name</li>
                                <li>• Column B: <strong>email</strong> - Student email (must be unique)</li>
                                <li>• Column C: <strong>password</strong> - Password (will be hashed)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Import Courses - Coming Soon -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-500 mb-4">Import Courses</h3>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-md text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            <p class="text-sm text-gray-500 mb-2"><strong>Coming Soon</strong></p>
                            <p class="text-xs text-gray-400">Import courses feature will be available in future updates</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Import Instructions</h3>
                    <div class="prose max-w-none text-sm text-gray-600">
                        <ol class="list-decimal list-inside space-y-2">
                            <li><strong>Download Template:</strong> Use the CSV template files to ensure correct format</li>
                            <li><strong>Prepare Data:</strong> Fill in the CSV file with your data</li>
                            <li><strong>Check Requirements:</strong>
                                <ul class="list-disc list-inside ml-4 mt-1 space-y-1">
                                    <li>Emails must be unique and valid</li>
                                    <li>Course titles must be unique</li>
                                    <li>Use CSV format (comma-separated values)</li>
                                    <li>First row should contain column headers</li>
                                </ul>
                            </li>
                            <li><strong>Upload File:</strong> Select and upload your CSV file</li>
                            <li><strong>Review Results:</strong> Check success/error messages after import</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
