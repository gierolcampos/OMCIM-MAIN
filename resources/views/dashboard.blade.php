<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $currentCalendar = \App\Models\SchoolCalendar::where('is_selected', true)->first();
            @endphp

            <!-- Current Academic Period Card -->
            <div class="mb-6 bg-[#c21313] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium uppercase">CURRENT ACADEMIC PERIOD</h3>
                            <p class="text-2xl font-bold mt-1">{{ $currentCalendar ? $currentCalendar->school_calendar_short_desc : 'No academic period set' }}</p>
                        </div>
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.school-calendars.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-[#c21313] border border-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 transition ease-in-out duration-150">
                                Manage
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Members Card -->
                        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Total Members</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ \App\Models\User::where('user_role', 'member')->count() }}</p>
                                </div>
                            </div>
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.members.index') }}" class="mt-4 text-sm text-[#c21313] hover:underline inline-block">View all members →</a>
                            @endif
                        </div>

                        <!-- Events Card -->
                        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Upcoming Events</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Event::where('start_date_time', '>=', now())->count() }}</p>
                                </div>
                            </div>
                            <a href="{{ route('events.custom-calendar') }}" class="mt-4 text-sm text-[#c21313] hover:underline inline-block">View calendar →</a>
                        </div>

                        <!-- Announcements Card -->
                        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Active Announcements</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Announcement::where('status', 'published')->count() }}</p>
                                </div>
                            </div>
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.announcements.index') }}" class="mt-4 text-sm text-[#c21313] hover:underline inline-block">View announcements →</a>
                            @else
                                <a href="{{ route('omcms.announcements') }}" class="mt-4 text-sm text-[#c21313] hover:underline inline-block">View announcements →</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
