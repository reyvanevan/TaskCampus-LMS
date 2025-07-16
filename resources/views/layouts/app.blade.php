<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TaskCampus') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-gray-800">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <script>
        // Initialize SweetAlert if available
        if (typeof Swal !== 'undefined') {
            // Global SweetAlert configuration can be placed here
            @if (session('swal_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('swal_success') }}',
                });
            @endif

            @if (session('swal_error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('swal_error') }}',
                });
            @endif
        }
    </script>

    <!-- Notification System Script -->
    <script>
        function notificationDropdown() {
            return {
                isOpen: false,
                notifications: [],
                unreadCount: 0,

                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.loadNotifications();
                    }
                },

                async loadNotifications() {
                    try {
                        const response = await fetch('/notifications/recent');
                        const data = await response.json();
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                    } catch (error) {
                        console.error('Failed to load notifications:', error);
                    }
                },

                async markAsRead(notificationId) {
                    try {
                        const response = await fetch(`/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                        });

                        if (response.ok) {
                            // Update notification as read with smooth animation
                            const notification = this.notifications.find(n => n.id === notificationId);
                            if (notification) {
                                notification.read_at = new Date().toISOString();
                                this.unreadCount = Math.max(0, this.unreadCount - 1);
                                
                                // Show success feedback
                                this.showToast('Notification marked as read', 'success');
                            }

                            // Navigate to relevant page if notification has data
                            if (notification && notification.data) {
                                setTimeout(() => {
                                    if (notification.data.assignment_id) {
                                        window.location.href = `/assignments/${notification.data.assignment_id}`;
                                    } else if (notification.data.submission_id) {
                                        window.location.href = `/submissions/${notification.data.submission_id}`;
                                    }
                                }, 500);
                            }
                        }
                    } catch (error) {
                        console.error('Failed to mark notification as read:', error);
                        this.showToast('Failed to mark notification as read', 'error');
                    }
                },

                async markAllAsRead() {
                    try {
                        const response = await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                        });

                        if (response.ok) {
                            // Mark all notifications as read
                            this.notifications.forEach(notification => {
                                notification.read_at = new Date().toISOString();
                            });
                            this.unreadCount = 0;
                            this.showToast('All notifications marked as read', 'success');
                        }
                    } catch (error) {
                        console.error('Failed to mark all notifications as read:', error);
                        this.showToast('Failed to mark all notifications as read', 'error');
                    }
                },

                getIcon(type) {
                    const icons = {
                        'assignment_created': '📝',
                        'assignment_graded': '✅',
                        'deadline_reminder': '⏰',
                        'course_enrolled': '🎓',
                        'submission_received': '📄',
                        'grade_updated': '📊'
                    };
                    return icons[type] || '🔔';
                },

                getIconBgClass(type) {
                    const classes = {
                        'assignment_created': 'bg-blue-100 text-blue-600',
                        'assignment_graded': 'bg-green-100 text-green-600',
                        'deadline_reminder': 'bg-yellow-100 text-yellow-600',
                        'course_enrolled': 'bg-purple-100 text-purple-600',
                        'submission_received': 'bg-indigo-100 text-indigo-600',
                        'grade_updated': 'bg-pink-100 text-pink-600'
                    };
                    return classes[type] || 'bg-gray-100 text-gray-600';
                },

                showToast(message, type = 'info') {
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
                            document.body.removeChild(toast);
                        }, 300);
                    }, 3000);
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    const now = new Date();
                    const diffMs = now - date;
                    const diffMins = Math.floor(diffMs / 60000);
                    const diffHours = Math.floor(diffMs / 3600000);
                    const diffDays = Math.floor(diffMs / 86400000);

                    if (diffMins < 1) return 'Just now';
                    if (diffMins < 60) return `${diffMins}m ago`;
                    if (diffHours < 24) return `${diffHours}h ago`;
                    if (diffDays < 7) return `${diffDays}d ago`;
                    return date.toLocaleDateString();
                },

                init() {
                    this.loadNotifications();
                    // Check for new notifications every 30 seconds
                    setInterval(() => {
                        this.loadNotifications();
                    }, 30000);
                    
                    // Show welcome message for new notifications
                    this.$watch('unreadCount', (newCount, oldCount) => {
                        if (newCount > oldCount && oldCount !== undefined) {
                            this.showToast(`You have ${newCount - oldCount} new notification${newCount - oldCount > 1 ? 's' : ''}!`, 'info');
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>