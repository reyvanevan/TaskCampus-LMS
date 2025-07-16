<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Notifications') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Stay updated with your latest activity
                </p>
            </div>
            <div class="flex space-x-2">
                @if($notifications->where('read_at', null)->count() > 0)
                    <button onclick="markAllAsRead()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm text-white font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Mark All as Read ({{ $notifications->where('read_at', null)->count() }})
                    </button>
                @endif
                <button onclick="refreshNotifications()" 
                        class="inline-flex items-center px-3 py-2 bg-gray-100 border border-transparent rounded-lg text-sm text-gray-700 font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-blue-100 truncate">Total Notifications</dt>
                                <dd class="text-lg font-semibold">{{ $notifications->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-sm p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-green-100 truncate">Read</dt>
                                <dd class="text-lg font-semibold">{{ $notifications->where('read_at', '!=', null)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl shadow-sm p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-yellow-100 truncate">Unread</dt>
                                <dd class="text-lg font-semibold">{{ $notifications->where('read_at', null)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Enhanced Notifications List -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                <div class="p-6 text-gray-900">
                    @if($notifications->count() > 0)
                        <div class="space-y-3">
                            @foreach($notifications as $notification)
                                <div class="group relative overflow-hidden rounded-xl border transition-all duration-200 hover:shadow-md {{ $notification->isUnread() ? 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200 shadow-sm' : 'bg-white border-gray-200 hover:bg-gray-50' }}" 
                                     data-notification-id="{{ $notification->id }}">
                                    
                                    <!-- Notification content -->
                                    <div class="flex items-start space-x-4 p-5">
                                        <!-- Enhanced Icon -->
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 rounded-full {{ $notification->getColorClass() }} flex items-center justify-center text-lg font-semibold shadow-sm">
                                                {{ $notification->getIcon() }}
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h3 class="text-sm font-medium text-gray-900 {{ $notification->isUnread() ? 'font-semibold' : '' }} leading-tight">
                                                        {{ $notification->title }}
                                                    </h3>
                                                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                                                        {{ $notification->message }}
                                                    </p>

                                                    <!-- Enhanced metadata -->
                                                    <div class="flex items-center mt-3 space-x-4 text-xs text-gray-500">
                                                        <div class="flex items-center space-x-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span class="capitalize">{{ str_replace('_', ' ', $notification->type) }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- Enhanced Action buttons -->
                                                    @if($notification->data)
                                                        <div class="mt-4 flex flex-wrap gap-2">
                                                            @if(isset($notification->data['assignment_id']))
                                                                <a href="{{ route('assignments.show', $notification->data['assignment_id']) }}" 
                                                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-lg transition-colors duration-200"
                                                                   onclick="markAsRead({{ $notification->id }})">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    View Assignment
                                                                </a>
                                                            @endif
                                                            @if(isset($notification->data['submission_id']))
                                                                <a href="{{ route('submissions.show', $notification->data['submission_id']) }}" 
                                                                   class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-lg transition-colors duration-200"
                                                                   onclick="markAsRead({{ $notification->id }})">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    View Submission
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Status and actions -->
                                                <div class="flex items-start space-x-2 ml-4">
                                                    @if($notification->isUnread())
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                            <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5 animate-pulse"></div>
                                                            New
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Read
                                                        </span>
                                                    @endif

                                                    @if($notification->isUnread())
                                                        <button onclick="markAsRead({{ $notification->id }})"
                                                                class="inline-flex items-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                                                title="Mark as read">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Unread indicator line -->
                                    @if($notification->isUnread())
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-600"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Enhanced Pagination -->
                        <div class="mt-8 flex items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b-xl">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <!-- Mobile pagination -->
                                {{ $notifications->links() }}
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">{{ $notifications->firstItem() ?? 0 }}</span>
                                        to
                                        <span class="font-medium">{{ $notifications->lastItem() ?? 0 }}</span>
                                        of
                                        <span class="font-medium">{{ $notifications->total() }}</span>
                                        notifications
                                    </p>
                                </div>
                                <div>
                                    {{ $notifications->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Enhanced Empty State -->
                        <div class="text-center py-16">
                            <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">All caught up! 🎉</h3>
                            <p class="text-gray-600 mb-4 max-w-md mx-auto">
                                You have no notifications at the moment. When you receive new assignments, grades, or important updates, they'll appear here.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <a href="{{ route('dashboard') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h0a2 2 0 012 2v0H8v0z"></path>
                                    </svg>
                                    Go to Dashboard
                                </a>
                                <button onclick="refreshNotifications()" 
                                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Refresh
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced notification interaction with better feedback
        function markAsRead(notificationId) {
            const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
            
            // Add loading state
            if (notification) {
                notification.style.opacity = '0.7';
                notification.style.pointerEvents = 'none';
            }

            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && notification) {
                    // Smooth transition to read state
                    notification.style.opacity = '1';
                    notification.style.pointerEvents = 'auto';
                    
                    // Update visual state
                    notification.classList.remove('bg-gradient-to-r', 'from-blue-50', 'to-indigo-50', 'border-blue-200', 'shadow-sm');
                    notification.classList.add('bg-white', 'border-gray-200');
                    
                    // Remove unread indicator
                    const indicator = notification.querySelector('.absolute.left-0');
                    if (indicator) indicator.remove();
                    
                    // Update badge
                    const newBadge = notification.querySelector('.bg-blue-100');
                    if (newBadge) {
                        newBadge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
                        newBadge.innerHTML = `
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Read
                        `;
                    }
                    
                    // Remove mark as read button
                    const readButton = notification.querySelector('button[onclick*="markAsRead"]');
                    if (readButton) {
                        readButton.remove();
                    }
                    
                    // Show success toast
                    showToast('Notification marked as read', 'success');
                    updateNotificationCount();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (notification) {
                    notification.style.opacity = '1';
                    notification.style.pointerEvents = 'auto';
                }
                showToast('Failed to mark notification as read', 'error');
            });
        }

        function markAllAsRead() {
            // Show confirmation
            if (!confirm('Mark all notifications as read?')) return;
            
            // Add loading state to button
            const button = event.target;
            const originalText = button.textContent;
            button.disabled = true;
            button.textContent = 'Marking...';

            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('All notifications marked as read', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to mark all notifications as read', 'error');
                button.disabled = false;
                button.textContent = originalText;
            });
        }

        function refreshNotifications() {
            showToast('Refreshing notifications...', 'info');
            setTimeout(() => location.reload(), 500);
        }

        function updateNotificationCount() {
            fetch('/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    const countElement = document.querySelector('#notification-count');
                    if (countElement) {
                        if (data.count > 0) {
                            countElement.textContent = data.count > 99 ? '99+' : data.count;
                            countElement.style.display = 'flex';
                        } else {
                            countElement.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Failed to update notification count:', error));
        }

        function showToast(message, type = 'info') {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            // Remove toast after 3 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        // Auto-refresh notifications every 2 minutes
        setInterval(updateNotificationCount, 120000);
    </script>
</x-app-layout>
