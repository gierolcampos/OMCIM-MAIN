@extends('layouts.app')
@section('content')
<style>
    /* Custom Dashboard Styles - Event Stats */
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
    
    .event-table {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .event-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
    }
    
    .event-item {
        transition: all 0.2s ease;
    }
    
    .event-item:hover {
        background-color: #f9fafb;
    }
    
    .status-badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-upcoming {
        background-color: #d1fae5;
        color: #047857;
    }
    
    .status-completed {
        background-color: #dbeafe;
        color: #1d4ed8;
    }
    
    .status-cancelled {
        background-color: #fef2f2;
        color: #b91c1c;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Dashboard Header -->
        <div class="dashboard-header gradient-red shadow-xl mb-8 overflow-hidden">
            <div class="px-6 py-10 md:px-10 md:py-14">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Event Statistics</h2>
                        <p class="text-red-100 text-lg">Detailed analytics about organization events</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <div class="px-4 py-3 bg-white bg-opacity-10 backdrop-blur-sm rounded-lg text-white">
                            <div class="text-xs uppercase tracking-wider text-red-200 mb-1">Upcoming Events</div>
                            <div class="text-2xl font-bold">{{ $events->where('start_date_time', '>=', now())->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Stats Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 fade-in">
            <h3 class="card-title text-xl font-bold text-gray-800 mb-8">Event Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="stat-card bg-gradient-to-br from-purple-50 to-white p-6 rounded-xl border border-purple-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 rounded-full bg-purple-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">Total Events</h4>
                            <p class="text-3xl font-bold text-purple-600">{{ $events->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-gradient-to-br from-green-50 to-white p-6 rounded-xl border border-green-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">Upcoming Events</h4>
                            <p class="text-3xl font-bold text-green-600">{{ $events->where('start_date_time', '>=', now())->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl border border-blue-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">Past Events</h4>
                            <p class="text-3xl font-bold text-blue-600">{{ $events->where('start_date_time', '<', now())->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events List -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h3 class="card-title text-xl font-bold text-gray-800">Upcoming Events</h3>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('events.index') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                        View All Events
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 event-table">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($events->where('start_date_time', '>=', now())->sortBy('start_date_time')->take(10) as $event)
                            <tr class="event-item hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <a href="{{ route('events.show', $event) }}" class="hover:text-red-600 transition-colors">
                                                {{ $event->title }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-purple-50 mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $event->start_date_time ? $event->start_date_time->format('M d, Y') : 'No date' }}</div>
                                            <div class="text-xs text-gray-500">{{ $event->start_date_time ? $event->start_date_time->format('g:i A') : 'No time specified' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-blue-50 mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div class="text-sm text-gray-900">{{ $event->location ?? 'Not specified' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge {{ $event->status === 'upcoming' ? 'status-upcoming' : ($event->status === 'completed' ? 'status-completed' : 'status-cancelled') }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">No upcoming events found</p>
                                        <p class="text-gray-400 text-sm mt-1">Create new events to see them here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Past Events Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 fade-in">
            <h3 class="card-title text-xl font-bold text-gray-800 mb-6">Recent Past Events</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 event-table">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($events->where('start_date_time', '<', now())->sortByDesc('start_date_time')->take(5) as $event)
                            <tr class="event-item hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <a href="{{ route('events.show', $event) }}" class="hover:text-red-600 transition-colors">
                                                {{ $event->title }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-purple-50 mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $event->start_date_time ? $event->start_date_time->format('M d, Y') : 'No date' }}</div>
                                            <div class="text-xs text-gray-500">{{ $event->start_date_time ? $event->start_date_time->format('g:i A') : 'No time specified' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-blue-50 mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div class="text-sm text-gray-900">{{ $event->location ?? 'Not specified' }}</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">No past events found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 