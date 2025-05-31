<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ICS Organization Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-800 rounded-lg shadow-xl mb-6 overflow-hidden">
                <div class="px-6 py-8 md:px-10 md:py-12">
                    <h2 class="text-3xl font-bold text-white mb-2">Welcome to ICS Organization Portal</h2>
                    <p class="text-blue-100 text-lg mb-6">Navotas Polytechnic College - Integrated Computer Society</p>
                    <p class="text-blue-100">Your hub for all ICS activities, events, and announcements.</p>
                </div>
            </div>

            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Dashboard Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-br from-blue-50 to-white">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Dashboard</h3>
                                <p class="text-sm text-gray-600">Overview & Analytics</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('dashboard.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                Go to Dashboard
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Members Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-br from-green-50 to-white">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Members</h3>
                                <p class="text-sm text-gray-600">Member Directory</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('members.index') }}" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-800">
                                View Members
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Events Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-br from-purple-50 to-white">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 text-white mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Events</h3>
                                <p class="text-sm text-gray-600">Upcoming Activities</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('events.index') }}" class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-800">
                                See Events
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Announcements Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-br from-red-50 to-white">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-500 text-white mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Announcements</h3>
                                <p class="text-sm text-gray-600">Latest Updates</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800">
                                Read Announcements
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity</h3>
                <div class="border-t border-gray-200 pt-4">
                    <p class="text-gray-600 italic">No recent activities yet. Check back soon!</p>
                </div>
            </div>

            <!-- Tech Resources Section -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Tech Resources</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <h4 class="font-medium text-gray-800 mb-2">Programming Tutorials</h4>
                        <p class="text-sm text-gray-600">Access tutorials for different programming languages.</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <h4 class="font-medium text-gray-800 mb-2">Project Ideas</h4>
                        <p class="text-sm text-gray-600">Explore project ideas for skill development.</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <h4 class="font-medium text-gray-800 mb-2">Tech Events</h4>
                        <p class="text-sm text-gray-600">Stay updated on upcoming tech conferences and webinars.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>