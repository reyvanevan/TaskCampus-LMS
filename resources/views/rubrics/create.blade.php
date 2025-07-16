<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Rubric') }} for {{ $assignment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('rubrics.store', $assignment) }}" id="rubric-form">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Rubric Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="2" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="total_points" :value="__('Total Points')" />
                            <x-text-input id="total_points" class="block mt-1 w-full" type="number" name="total_points" :value="old('total_points', 100)" min="1" step="0.01" required />
                            <x-input-error :messages="$errors->get('total_points')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-lg font-semibold text-gray-700">Criteria</h3>
                                <button type="button" id="add-criteria" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Add Criteria
                                </button>
                            </div>
                            
                            <div id="criteria-container" class="space-y-4">
                                <!-- Criteria will be added here dynamically -->
                                <div class="criteria-item p-4 border border-gray-200 rounded-md">
                                    <div class="flex justify-between">
                                        <h4 class="font-medium text-gray-700 mb-2">Criteria 1</h4>
                                        <button type="button" class="remove-criteria text-red-600 hover:text-red-900">Remove</button>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                        <input type="text" name="criteria[0][title]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea name="criteria[0][description]" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Points</label>
                                        <input type="number" name="criteria[0][max_score]" min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-red-500 text-sm mt-2" id="criteria-error" style="display: none;">
                                At least one criteria is required.
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('assignments.show', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Create Rubric') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let criteriaCount = 1;
            
            // Add criteria button
            document.getElementById('add-criteria').addEventListener('click', function() {
                criteriaCount++;
                const container = document.getElementById('criteria-container');
                const criteriaItem = document.createElement('div');
                criteriaItem.className = 'criteria-item p-4 border border-gray-200 rounded-md mb-4';
                criteriaItem.innerHTML = `
                    <div class="flex justify-between">
                        <h4 class="font-medium text-gray-700 mb-2">Criteria ${criteriaCount}</h4>
                        <button type="button" class="remove-criteria text-red-600 hover:text-red-900">Remove</button>
                    </div>
                    
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="criteria[${criteriaCount - 1}][title]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="criteria[${criteriaCount - 1}][description]" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Points</label>
                        <input type="number" name="criteria[${criteriaCount - 1}][max_score]" min="0" step="0.01" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 criteria-points" required>
                        <span class="text-red-500 text-sm hidden error-message">Please enter a valid number</span>
                    </div>
                `;
                container.appendChild(criteriaItem);
                
                // Update remove handlers
                updateRemoveHandlers();
            });
            
            // Form submit validation
            document.getElementById('rubric-form').addEventListener('submit', function(e) {
                const criteriaItems = document.querySelectorAll('.criteria-item');
                
                // Check if there are criteria items
                if (criteriaItems.length === 0) {
                    e.preventDefault();
                    document.getElementById('criteria-error').style.display = 'block';
                    return;
                } else {
                    document.getElementById('criteria-error').style.display = 'none';
                }
                
                // Validate all points inputs before submitting
                let hasErrors = false;
                document.querySelectorAll('.criteria-points').forEach(input => {
                    const value = parseFloat(input.value);
                    if (isNaN(value) || value < 0) {
                        e.preventDefault();
                        const errorMsg = input.parentNode.querySelector('.error-message');
                        errorMsg.classList.remove('hidden');
                        input.classList.add('border-red-500');
                        hasErrors = true;
                    }
                });
                
                if (hasErrors) {
                    e.preventDefault();
                    alert('Please fix errors in the criteria points before submitting.');
                }
            });
            
            // Initial remove handlers
            updateRemoveHandlers();
            
            // Function to update remove handlers
            function updateRemoveHandlers() {
                document.querySelectorAll('.remove-criteria').forEach(button => {
                    button.addEventListener('click', function() {
                        const criteriaItem = this.closest('.criteria-item');
                        criteriaItem.remove();
                        
                        // Reindex the remaining criteria
                        const items = document.querySelectorAll('.criteria-item');
                        items.forEach((item, index) => {
                            item.querySelector('h4').textContent = `Criteria ${index + 1}`;
                            item.querySelectorAll('input, textarea').forEach(input => {
                                const name = input.getAttribute('name');
                                if (name) {
                                    input.setAttribute('name', name.replace(/criteria\[\d+\]/, `criteria[${index}]`));
                                }
                            });
                        });
                        
                        // Update the criteria count
                        criteriaCount = items.length;
                    });
                });
            }
            
            // Validate number inputs
            function validateNumber(input) {
                const value = parseFloat(input.value);
                const errorMsg = input.parentNode.querySelector('.error-message');
                
                if (isNaN(value) || value < 0) {
                    errorMsg.classList.remove('hidden');
                    input.classList.add('border-red-500');
                    return false;
                } else {
                    errorMsg.classList.add('hidden');
                    input.classList.remove('border-red-500');
                    return true;
                }
            }
            
            // Add global validation for number inputs
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('input', function() {
                    validateNumber(this);
                });
            });
        });
    </script>
</x-app-layout>