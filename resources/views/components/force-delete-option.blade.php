@if (session('show_force_delete') && session('force_delete_data'))
    @php $forceData = session('force_delete_data'); @endphp
    <div class="mt-4 pt-4 border-t border-red-300">
        <div class="flex items-center mb-3">
            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <strong class="text-red-800">⚠️ Force Delete Option:</strong>
        </div>
        <p class="text-sm text-red-700 mb-3">
            If you want to permanently delete this {{ isset($forceData['course_name']) ? 'course' : 'assignment' }} along with all related data, click the button below.
            <strong>This action cannot be undone!</strong>
        </p>
        <form method="POST" action="{{ $forceData['force_delete_url'] }}" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    onclick="return confirm('🚨 FORCE DELETE CONFIRMATION\n\nThis will PERMANENTLY DELETE:\n{{ isset($forceData['course_name']) ? '- Course: ' . $forceData['course_name'] . '\n- All ' . $forceData['enrollment_count'] . ' enrollment(s)\n- All ' . $forceData['assignment_count'] . ' assignment(s) and their submissions\n- All related files and data' : '- Assignment: ' . $forceData['assignment_title'] . '\n- All ' . $forceData['submission_count'] . ' submission(s)\n- All related files and grades' }}\n\nThis action CANNOT be undone!\n\nAre you absolutely sure?')"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                FORCE DELETE (Danger!)
            </button>
        </form>
    </div>
@endif
