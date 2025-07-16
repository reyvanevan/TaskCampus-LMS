<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lecturer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome to Lecturer Dashboard</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-100 p-4 rounded-lg shadow">
                            <h4 class="font-bold">My Courses</h4>
                            <p class="text-sm">Manage your courses and class materials</p>
                            <a href="#" class="text-blue-600 hover:underline text-sm">View Courses →</a>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg shadow">
                            <h4 class="font-bold">Assignments</h4>
                            <p class="text-sm">Create and grade assignments</p>
                            <a href="#" class="text-blue-600 hover:underline text-sm">Manage Assignments →</a>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg shadow">
                            <h4 class="font-bold">Students</h4>
                            <p class="text-sm">View enrolled students and their progress</p>
                            <a href="#" class="text-blue-600 hover:underline text-sm">View Students →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>