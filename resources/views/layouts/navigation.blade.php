@php
    use Illuminate\Support\Facades\Auth;
@endphp

<style>
    .nav-hover-effect {
        transition: all 0.3s ease;
    }
    .nav-hover-effect:hover {
        color: #c21313;
    }
    .dropdown-hover:hover {
        background-color: #c21313;
        color: white;
    }
    /* Mobile view styles */
    @media (max-width: 640px) {
        .responsive-nav-link {
            transition: all 0.3s ease;
        }
        .responsive-nav-link:hover {
            color: #c21313 !important;
        }
        .responsive-nav-link.active {
            color: white !important;
            background-color: #c21313 !important;
            border-color: #c21313 !important;
        }
        .responsive-nav-link div {
            color: #c21313;
        }
        .sm\:hidden .space-y-1 a:hover {
            color: #c21313;
        }
        .sm\:hidden .border-t a:hover {
            color: #c21313;
        }
        .sm\:hidden .space-y-1 a[aria-current="page"] {
            background-color: #c21313 !important;
            color: white !important;
        }
    }
    /* About Us Dropdown Styles */
    .about-dropdown {
        position: relative;
        display: inline-block;
    }
    .about-dropdown-content {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        min-width: 200px;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 50;
    }
    .about-dropdown:hover .about-dropdown-content {
        display: block;
    }
    .about-dropdown-item {
        display: block;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        color: #4b5563;
        transition: all 0.3s ease;
    }
    .about-dropdown-item:hover {
        background-color: #c21313;
        color: white;
    }
    /* Profile Avatar Styles */
    .profile-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        background-color: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 2px solid white;
        overflow: hidden;
    }
    .profile-avatar:hover {
        background-color: #c21313;
        color: white;
        transform: scale(1.05);
        box-shadow: 0 3px 6px rgba(194, 19, 19, 0.3);
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .profile-avatar:hover img {
        transform: scale(1.1);
    }
</style>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-full">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('home.index') }}">
                    <x-application-logo class="block h-9 w-auto fill-current" style="color: #c21313;" />
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:flex sm:items-center sm:justify-center flex-1">

                <x-nav-link :href="route('home.index')" :active="request()->routeIs('home.index')">
                    {{ __('ICS Hall') }}
                </x-nav-link>

                @if(Auth::user()->isSuperadmin())
                    <x-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.*')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                @endif

                @if(Auth::user()->canManageMembers())
                    <x-nav-link :href="route('admin.members.index')" :active="request()->routeIs('admin.members.*')">
                        {{ __('Members') }}
                    </x-nav-link>
                @endif

                <x-nav-link :href="route('events.custom-calendar')" :active="request()->routeIs(['events.*', 'events.custom-calendar', 'omcms.events'])">
                    {{ __('Events') }}
                </x-nav-link>

                @if(Auth::user()->canManageAnnouncements())
                    <x-nav-link :href="route('admin.announcements.index')" :active="request()->routeIs('admin.announcements.*')">
                        {{ __('Announcements') }}
                    </x-nav-link>
                @else
                    <x-nav-link :href="route('omcms.announcements')" :active="request()->routeIs('omcms.announcements')">
                        {{ __('Announcements') }}
                    </x-nav-link>
                @endif

                @php
                    $userRole = strtolower(Auth::user()->user_role ?? '');
                    $isModerator = $userRole === 'moderator';
                @endphp
                
                    @if(Auth::user()->canManagePayments())
                        <x-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                            {{ __('Payments') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('client.payments.index')" :active="request()->routeIs('client.payments.*')">
                            {{ __('Payments') }}
                        </x-nav-link>
                    @endif
                

                @if(Auth::user()->canManageReports())
                    <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                        {{ __('Reports') }}
                    </x-nav-link>
                @endif



                <div class="about-dropdown">
                    <x-nav-link :href="route('aboutus')" :active="request()->routeIs('aboutus')" class="inline-flex items-center">
                        {{ __('About Us') }}
                    </x-nav-link>
                    <div class="about-dropdown-content">
                        <a href="{{ route('about_us.about_ics') }}" class="about-dropdown-item">About ICS</a>
                        <a href="{{ route('about_us.vision_mission') }}" class="about-dropdown-item">Vision and Mission</a>
                        <a href="{{ route('about_us.history') }}" class="about-dropdown-item">History</a>
                        <a href="{{ route('about_us.logo_symbolism') }}" class="about-dropdown-item">Logo Symbolism</a>
                        <a href="{{ route('about_us.student_leaders') }}" class="about-dropdown-item">ICS Student Leaders</a>
                        <a href="{{ route('about_us.developers') }}" class="about-dropdown-item">Developers</a>
                        <a href="{{ route('about_us.contact') }}" class="about-dropdown-item">Contact Us</a>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center space-x-4">
                <!-- Notification Button -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="relative p-1 text-gray-600 hover:text-[#c21313] focus:outline-none transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        <!-- Notification Badge -->
                        @if(Auth::user()->unread_notifications_count > 0)
                            <span class="notification-badge absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-[#c21313] rounded-full">
                                {{ Auth::user()->unread_notifications_count > 99 ? '99+' : Auth::user()->unread_notifications_count }}
                            </span>
                        @else
                            <span class="notification-badge absolute top-0 right-0 hidden px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-[#c21313] rounded-full"></span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50"
                         style="display: none;">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-[#c21313] hover:text-red-700">View All</a>
                            </div>

                            <div class="max-h-64 overflow-y-auto" id="notification-list">
                                <!-- Notifications will be loaded here via JavaScript -->
                                <div class="px-4 py-8 text-center text-gray-500 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Loading notifications...
                                </div>
                            </div>

                            <div class="px-4 py-2 border-t border-gray-100 text-center">
                                <button id="mark-all-read-dropdown" class="text-xs text-[#c21313] hover:text-red-700">Mark all as read</button>
                            </div>
                        </div>
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                <div class="profile-avatar">
                                    @if(Auth::user()->profile_picture)
                                        <img src="{{ Auth::user()->profile_picture }}" alt="{{ Auth::user()->firstname }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="dropdown-hover">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="dropdown-hover">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Notification and Hamburger -->
            <div class="flex items-center sm:hidden">
                <!-- Mobile Notification Bell -->
                <div class="relative mr-2" x-data="{ mobileNotificationOpen: false }" @click.away="mobileNotificationOpen = false">
                    <button @click="mobileNotificationOpen = !mobileNotificationOpen" class="relative p-2 text-gray-600 hover:text-[#c21313] focus:outline-none transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        <!-- Mobile Notification Badge -->
                        @if(Auth::user()->unread_notifications_count > 0)
                            <span class="mobile-notification-badge absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-[#c21313] rounded-full">
                                {{ Auth::user()->unread_notifications_count > 99 ? '99+' : Auth::user()->unread_notifications_count }}
                            </span>
                        @endif
                    </button>

                    <!-- Mobile Notification Dropdown -->
                    <div x-show="mobileNotificationOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50"
                         style="display: none;">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-[#c21313] hover:text-red-700">View All</a>
                            </div>

                            <div class="max-h-64 overflow-y-auto" id="mobile-notification-list">
                                <!-- Notifications will be loaded here via JavaScript -->
                                <div class="px-4 py-8 text-center text-gray-500 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Loading notifications...
                                </div>
                            </div>

                            <div class="px-4 py-2 border-t border-gray-100 text-center">
                                <button id="mobile-mark-all-read" class="text-xs text-[#c21313] hover:text-red-700">Mark all as read</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hamburger -->
                <button @click="open = ! open" class="nav-hover-effect inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
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
            <x-responsive-nav-link :href="route('home.index')" :active="request()->routeIs('home.index')">
                {{ __('ICS Hall') }}
            </x-responsive-nav-link>

            @if(Auth::user()->isSuperadmin())
                <x-responsive-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.*')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->canManageMembers())
                <x-responsive-nav-link :href="route('admin.members.index')" :active="request()->routeIs('admin.members.*')">
                    {{ __('Members') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('events.custom-calendar')" :active="request()->routeIs(['events.*', 'events.custom-calendar', 'omcms.events'])">
                {{ __('Events') }}
            </x-responsive-nav-link>
            @if(!Auth::user()->canManageAnnouncements())
                <x-responsive-nav-link :href="route('omcms.announcements')" :active="request()->routeIs('omcms.announcements')">
                    {{ __('Announcements') }}
                </x-responsive-nav-link>
            @endif
            @php
                $userRole = strtolower(Auth::user()->user_role ?? '');
                $isModerator = $userRole === 'moderator';
            @endphp
            @if(!$isModerator)
                @if(Auth::user()->canManagePayments())
                    <x-responsive-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                        {{ __('Payments') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('client.payments.index')" :active="request()->routeIs('client.payments.*')">
                        {{ __('Payments') }}
                    </x-responsive-nav-link>
                @endif
            @endif

            @if(Auth::user()->canManageReports())
                <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->canManageAnnouncements())
                <x-responsive-nav-link :href="route('admin.announcements.index')" :active="request()->routeIs('admin.announcements.*')">
                    {{ __('Announcements') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('aboutus')" :active="request()->routeIs('aboutus')">
                {{ __('About Us') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center">
                <div class="profile-avatar mr-3">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ Auth::user()->profile_picture }}" alt="{{ Auth::user()->firstname }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="nav-hover-effect">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="nav-hover-effect">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
