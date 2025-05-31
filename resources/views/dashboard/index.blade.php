<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <style>
    /* Custom Dashboard Styles */
    .dashboard-header {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,.075)' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.3;
    }

    .stat-card {
        transition: all 0.3s ease;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .stat-icon {
        position: relative;
        z-index: 10;
    }

    .stat-icon::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: -1;
    }

    .activity-card {
        position: relative;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }

    .activity-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .card-title {
        position: relative;
        display: inline-block;
        padding-bottom: 0.5rem;
    }

    .card-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40%;
        height: 3px;
        background: linear-gradient(to right, #c21313, rgba(194, 19, 19, 0.5));
        border-radius: 3px;
    }

    .gradient-red {
        background: linear-gradient(135deg, #c21313 0%, #e65758 100%);
    }

    .announcement-item {
        transition: all 0.2s ease;
        border-radius: 0.75rem;
        cursor: pointer;
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }

    .announcement-item:hover {
        background-color: #fef2f2;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* Modal Animation Styles */
    .modal-fade-enter {
        opacity: 0;
    }
    .modal-fade-enter-active {
        opacity: 1;
        transition: opacity 300ms ease-out;
    }
    .modal-fade-exit {
        opacity: 1;
    }
    .modal-fade-exit-active {
        opacity: 0;
        transition: opacity 200ms ease-in;
    }

    .modal-scale-enter {
        opacity: 0;
        transform: scale(0.95);
    }
    .modal-scale-enter-active {
        opacity: 1;
        transform: scale(1);
        transition: opacity 300ms, transform 300ms;
    }
    .modal-scale-exit {
        opacity: 1;
        transform: scale(1);
    }
    .modal-scale-exit-active {
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 200ms, transform 200ms;
    }

    .announcement-modal {
        max-height: 90vh;
        overflow-y: auto;
    }

    /* Announcement modal styles */
    .announcement-modal {
        border-radius: 1rem;
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
    }

    .announcement-modal .text-justify {
        text-align: justify;
        line-height: 1.7;
        font-size: 1.05rem;
    }

    .announcement-modal h3 {
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
        letter-spacing: -0.025em;
    }

    .break-words {
        word-break: break-word;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .announcement-modal {
            margin: 1rem;
            max-height: calc(100vh - 2rem);
        }

        .announcement-modal .prose {
            font-size: 0.95rem;
            line-height: 1.6;
            padding: 0 0.5rem;
        }
    }
</style>

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Current Academic Period Card -->
        <div class="mb-6 bg-[#c21313] overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium uppercase">CURRENT ACADEMIC PERIOD</h3>
                        <p class="text-2xl font-bold mt-1">{{ $currentCalendar ? $currentCalendar->school_calendar_short_desc : 'No academic period set' }}</p>
                    </div>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.school-calendars.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-[#c21313] border border-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 transition ease-in-out duration-150">
                            Manage
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dashboard Header -->
        <div class="dashboard-header gradient-red shadow-xl mb-8 overflow-hidden">
            <div class="px-6 py-10 md:px-10 md:py-14">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Admin Dashboard</h2>
                        <p class="text-red-100 text-lg mb-4">ICS Organization Management Panel</p>
                        <p class="text-red-100 bg-red-900 bg-opacity-20 inline-block px-4 py-2 rounded-full text-sm font-medium">
                            Welcome, {{ auth()->user()->firstname }} {{ auth()->user()->lastname }}!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Members Stats -->
            <div class="stat-card bg-white">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">Members</p>
                            <h3 class="text-3xl font-bold text-green-600">{{ $memberCount }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Total registered members</p>
                        </div>
                        <div class="stat-icon p-4 rounded-full bg-gradient-to-br from-green-500 to-green-600 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-50">
                        <a href="{{ route('members.index') }}" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-800">
                            View All Members
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Events Stats -->
            <div class="stat-card bg-white">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">Events</p>
                            <h3 class="text-3xl font-bold text-purple-600">{{ $eventCount }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $selectedCalendar ? $selectedCalendar->school_calendar_short_desc : 'All' }} events</p>
                        </div>
                        <div class="stat-icon p-4 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-50">
                        <a href="{{ route('admin.events.custom-calendar') }}" class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-800">
                            View All Events
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Announcements Stats -->
            <div class="stat-card bg-white">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">Announcements</p>
                            <h3 class="text-3xl font-bold text-red-600">{{ $announcementCount }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $selectedCalendar ? $selectedCalendar->school_calendar_short_desc : 'All' }} announcements</p>
                        </div>
                        <div class="stat-icon p-4 rounded-full bg-gradient-to-br from-red-500 to-red-600 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-50">
                        <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800">
                            View All Announcements
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Row -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <a href="{{ route('admin.announcements.index') }}" class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-red-200">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Manage Announcements</span>
                </a>
                <a href="{{ route('events.create') }}" class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-purple-200">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Create Event</span>
                </a>
                <a href="{{ route('admin.events.calendar') }}" class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-blue-200">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">View Calendar</span>
                </a>
                <a href="{{ route('dashboard.member-stats') }}" class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-green-200">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Statistics</span>
                </a>
                <a href="{{ route('admin.deletion-requests.index') }}" class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-yellow-200 relative">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Deletion Requests</span>
                    @if($deletionRequestsCount > 0)
                        <span class="absolute top-2 right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform bg-red-600 rounded-full">
                            {{ $deletionRequestsCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Recent Announcements -->
            <div class="activity-card bg-white p-6 rounded-lg shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                    <h3 class="card-title text-xl font-bold text-gray-800">Recent Announcements</h3>
                    <div class="mt-2 sm:mt-0">
                        <form action="{{ route('dashboard.index') }}" method="GET" class="flex items-center">
                            <div class="flex items-center">
                                <div class="relative">
                                    <select name="calendar_id" id="announcement_calendar_id" class="pl-4 pr-10 py-1.5 border border-gray-300 bg-white text-gray-800 rounded-l-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm appearance-none">
                                        @foreach($schoolCalendars as $calendar)
                                            <option value="{{ $calendar->id }}" {{ $selectedCalendar && $selectedCalendar->id == $calendar->id ? 'selected' : '' }}>
                                                A.Y. {{ $calendar->school_calendar_short_desc }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" class="px-3 py-1.5 bg-[#c21313] text-white border border-[#c21313] rounded-r-md font-semibold text-xs uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    @if(isset($announcements) && count($announcements) > 0)
                        <div class="space-y-4">
                            @foreach($announcements as $announcement)
                                <div x-data="{ showModal: false }" class="announcement-item bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all duration-200">
                                    <div class="p-4 cursor-pointer" @click="showModal = true">
                                        <div class="flex flex-col">
                                            <div class="text-base font-semibold text-gray-800 hover:text-red-600 transition break-words mb-2">
                                                {{ $announcement->title ?? 'No Title' }}
                                            </div>
                                            <p class="text-sm text-gray-600 line-clamp-2" @if(isset($announcement->text_color)) style="color: {{ $announcement->text_color }};" @endif>
                                                {{ \Illuminate\Support\Str::limit(strip_tags($announcement->content ?? ''), 120) }}
                                            </p>
                                            <div class="flex items-center gap-2 flex-wrap mt-3">
                                                @if(isset($announcement->is_boosted) && $announcement->is_boosted)
                                                    <span class="text-xs font-medium px-2 py-1 bg-blue-50 text-blue-600 rounded-full flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                        </svg>
                                                        Pinned
                                                    </span>
                                                @endif
                                                @if(isset($announcement->priority) && $announcement->priority == 'high')
                                                    <span class="text-xs font-medium px-2 py-1 bg-red-50 text-red-600 rounded-full">Important</span>
                                                @endif
                                                <span class="text-xs font-medium px-2 py-1 bg-gray-50 text-gray-600 rounded-full whitespace-nowrap">{{ isset($announcement->created_at) ? $announcement->created_at->diffForHumans() : 'Unknown date' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Announcement Modal -->
                                    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <!-- Background overlay -->
                                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                            </div>

                                            <!-- Modal panel -->
                                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <!-- Modal header -->
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <div class="sm:flex sm:items-start">
                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                            <div class="flex justify-between items-start">
                                                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                                    {{ $announcement->title ?? 'No Title' }}
                                                                </h3>
                                                                <button @click="showModal = false" type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                                                                    <span class="sr-only">Close</span>
                                                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="flex items-center gap-2 flex-wrap mt-2 mb-4">
                                                                @if(isset($announcement->is_boosted) && $announcement->is_boosted)
                                                                    <span class="text-xs font-medium px-2 py-1 bg-blue-50 text-blue-600 rounded-full flex items-center">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                                        </svg>
                                                                        Pinned
                                                                    </span>
                                                                @endif
                                                                @if(isset($announcement->priority) && $announcement->priority == 'high')
                                                                    <span class="text-xs font-medium px-2 py-1 bg-red-50 text-red-600 rounded-full">Important</span>
                                                                @endif
                                                                <span class="text-xs font-medium px-2 py-1 bg-gray-50 text-gray-600 rounded-full">{{ isset($announcement->created_at) ? $announcement->created_at->diffForHumans() : 'Unknown date' }}</span>
                                                            </div>

                                                            @if(isset($announcement->image_path) && $announcement->image_path)
                                                                <div class="mt-4 mb-4">
                                                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($announcement->image_path) }}" alt="{{ $announcement->title ?? 'Announcement image' }}" class="w-full h-auto rounded-lg">
                                                                </div>
                                                            @elseif(isset($announcement->media_path) && $announcement->media_path)
                                                                <div class="mt-4 mb-4">
                                                                    @php
                                                                        $extension = pathinfo($announcement->media_path, PATHINFO_EXTENSION);
                                                                        $isVideo = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'wmv']);
                                                                    @endphp

                                                                    @if($isVideo)
                                                                        <video controls class="w-full h-auto rounded-lg">
                                                                            <source src="{{ \Illuminate\Support\Facades\Storage::url($announcement->media_path) }}" type="video/{{ $extension }}">
                                                                            Your browser does not support the video tag.
                                                                        </video>
                                                                    @else
                                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($announcement->media_path) }}" alt="{{ $announcement->title ?? 'Announcement media' }}" class="w-full h-auto rounded-lg">
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            <div class="mt-2 text-sm text-gray-700 whitespace-pre-wrap"
                                                                 @if(isset($announcement->text_color)) style="color: {{ $announcement->text_color }};" @endif>
                                                                {!! nl2br(e($announcement->content ?? 'No content available')) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal footer -->
                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <button @click="showModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-gray-500 mt-2">No recent announcements found.</p>
                        </div>
                    @endif

                    <div class="mt-6 text-right">
                        <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors duration-150 shadow-sm">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Events -->
            <div class="activity-card bg-white p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <h3 class="card-title text-xl font-bold text-gray-800">Pending Events</h3>
                    <div class="mt-2 sm:mt-0">
                        <form action="{{ route('dashboard.index') }}" method="GET" class="flex items-center">
                            <div class="flex items-center">
                                <div class="relative">
                                    <select name="calendar_id" id="event_calendar_id" class="pl-4 pr-10 py-1.5 border border-gray-300 bg-white text-gray-800 rounded-l-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm appearance-none">
                                        @foreach($schoolCalendars as $calendar)
                                            <option value="{{ $calendar->id }}" {{ $selectedCalendar && $selectedCalendar->id == $calendar->id ? 'selected' : '' }}>
                                                A.Y. {{ $calendar->school_calendar_short_desc }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" class="px-3 py-1.5 bg-[#c21313] text-white border border-[#c21313] rounded-r-md font-semibold text-xs uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    @if(isset($events) && count($events) > 0)
                        <div class="space-y-4">
                            @foreach($events as $event)
                                <div class="announcement-item p-3">
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-base font-semibold text-gray-800 hover:text-purple-600 transition">
                                            {{ $event->title ?? 'No Title' }}
                                        </a>
                                        <span class="text-xs font-medium px-2 py-1 bg-amber-100 text-amber-800 rounded-full">
                                            Pending
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">{{ \Illuminate\Support\Str::limit($event->description ?? 'No description', 100) }}</p>
                                    <div class="mt-2 flex items-center space-x-4">
                                        <div class="flex items-center text-xs text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $event->location ?? 'Location not specified' }}
                                        </div>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Ended: {{ isset($event->end_date_time) && $event->end_date_time ? $event->end_date_time->format('M d, Y') : 'No date' }}
                                        </div>
                                    </div>
                                    <div class="mt-3 flex justify-end space-x-2">
                                        <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Update Status
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-500 mt-2">No pending events found.</p>
                        </div>
                    @endif

                    <div class="mt-6 text-right">
                        <a href="{{ route('admin.events.custom-calendar') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>