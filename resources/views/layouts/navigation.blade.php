<nav class="bg-white border-b border-gray-100" x-data="{ open: false }">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-bold text-xl">
                        TaskCampus
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @if (Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('User Management') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.semesters.index')" :active="request()->routeIs('admin.semesters.*')">
                            {{ __('Semesters') }}
                        </x-nav-link>
                        <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*') && !request()->routeIs('courses.show')">
                            {{ __('Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                            {{ __('Assignments') }}
                        </x-nav-link>
                        <x-nav-link :href="route('import.index')" :active="request()->routeIs('import.*')">
                            {{ __('Import Excel') }}
                        </x-nav-link>
                    @elseif (Auth::user()->isLecturer())
                        <x-nav-link :href="route('lecturer.courses.dashboard')" :active="request()->routeIs('lecturer.courses.*')">
                            {{ __('My Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*') || request()->routeIs('submissions.*') || request()->routeIs('rubrics.*')">
                            {{ __('Assignments') }}
                        </x-nav-link>
                    @elseif (Auth::user()->isStudent())
                        <x-nav-link :href="route('student.enrollments.index')" :active="request()->routeIs('student.enrollments.*')">
                            {{ __('My Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                            {{ __('Assignments') }}
                        </x-nav-link>
                        <x-nav-link :href="route('submissions.my')" :active="request()->routeIs('submissions.my')">
                            {{ __('My Submissions') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                <!-- Notifications Dropdown -->
                <div class="relative" x-data="notificationDropdown()">
                    <button @click="toggleDropdown()" 
                            class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md transition-all duration-200 hover:bg-gray-50"
                            :class="unreadCount > 0 ? 'animate-pulse' : ''">
                        <!-- Bell notification icon -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                        <!-- Improved notification count badge -->
                        <span x-show="unreadCount > 0" 
                              x-text="unreadCount > 99 ? '99+' : unreadCount" 
                              id="notification-count"
                              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold border-2 border-white shadow-lg animate-bounce">
                        </span>
                    </button>

                    <!-- Enhanced Notifications Dropdown -->
                    <div x-show="isOpen" 
                         @click.away="isOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="transform opacity-0 scale-95 translate-y-1"
                         class="absolute right-0 mt-3 w-96 bg-white rounded-xl shadow-xl py-1 z-50 border border-gray-100 overflow-hidden">
                        
                        <!-- Header with gradient -->
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-3 text-white">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                    </svg>
                                    <h3 class="text-sm font-semibold">Notifications</h3>
                                    <span x-show="unreadCount > 0" 
                                          x-text="`(${unreadCount} new)`"
                                          class="text-xs bg-white bg-opacity-20 px-2 py-0.5 rounded-full">
                                    </span>
                                </div>
                                <a href="{{ route('notifications.index') }}" 
                                   class="text-xs text-blue-100 hover:text-white underline transition-colors">
                                    View All
                                </a>
                            </div>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <!-- Empty state with better design -->
                            <div x-show="notifications.length === 0" class="px-6 py-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">No new notifications</p>
                                <p class="text-gray-400 text-xs mt-1">You're all caught up! 🎉</p>
                            </div>
                            
                            <!-- Enhanced notification items -->
                            <template x-for="notification in notifications" :key="notification.id">
                                <div class="border-b border-gray-50 last:border-b-0 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 cursor-pointer transition-all duration-200" 
                                     :class="notification.read_at ? 'bg-white' : 'bg-blue-50 border-l-4 border-l-blue-500'"
                                     @click="markAsRead(notification.id)">
                                    <div class="px-4 py-4">
                                        <div class="flex items-start space-x-3">
                                            <!-- Enhanced icon with background -->
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg font-semibold"
                                                     :class="getIconBgClass(notification.type)">
                                                    <span x-text="getIcon(notification.type)"></span>
                                                </div>
                                            </div>
                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-900 mb-1" 
                                                           :class="notification.read_at ? '' : 'font-semibold'"
                                                           x-text="notification.title"></p>
                                                        <p class="text-xs text-gray-600 leading-relaxed" 
                                                           x-text="notification.message"></p>
                                                        <div class="flex items-center mt-2 space-x-2">
                                                            <p class="text-xs text-gray-400" x-text="formatDate(notification.created_at)"></p>
                                                            <span x-show="!notification.read_at" 
                                                                  class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                New
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Unread indicator -->
                                                    <div x-show="!notification.read_at" class="flex-shrink-0 ml-2">
                                                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Footer with action buttons -->
                            <div x-show="notifications.length > 0" class="border-t border-gray-100 bg-gray-50 px-4 py-3">
                                <div class="flex justify-between items-center">
                                    <button @click="markAllAsRead()" 
                                            x-show="unreadCount > 0"
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                        Mark all as read
                                    </button>
                                    <a href="{{ route('notifications.index') }}" 
                                       class="text-xs text-gray-600 hover:text-gray-800 font-medium transition-colors">
                                        View history →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full mr-2 object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                        <span class="text-gray-500">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>{{ Auth::user()->name }}</div>
                            </div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Responsive menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if (Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.semesters.index')" :active="request()->routeIs('admin.semesters.*')">
                    {{ __('Semesters') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*') && !request()->routeIs('courses.show')">
                    {{ __('Courses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                    {{ __('Assignments') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->isLecturer())
                <x-responsive-nav-link :href="route('lecturer.courses.dashboard')" :active="request()->routeIs('lecturer.courses.*')">
                    {{ __('My Courses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*') || request()->routeIs('submissions.*') || request()->routeIs('rubrics.*')">
                    {{ __('Assignments') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->isStudent())
                <x-responsive-nav-link :href="route('student.enrollments.index')" :active="request()->routeIs('student.enrollments.*')">
                    {{ __('My Courses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                    {{ __('Assignments') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('submissions.my')" :active="request()->routeIs('submissions.my')">
                    {{ __('My Submissions') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center justify-between">
                <div class="flex items-center">
                    @if(Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full mr-3 object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                            <span class="text-gray-500">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <!-- Mobile Dark Mode Toggle - REMOVED -->
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>