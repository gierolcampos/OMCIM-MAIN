<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Upcoming Events</h3>
                    <p class="mb-4">This section will display upcoming events and activities for the Navotas Polytechnic College - Integrated Computer Society.</p>

                    <div class="bg-purple-50 p-4 rounded-lg mb-4">
                        <p class="text-purple-700">Check out our new calendar views!</p>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('events.index') }}" class="border border-[#c21313] hover:bg-[#c21313] hover:text-white px-6 py-3 rounded-lg transition duration-300 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                            </svg>
                            List View
                        </a>
                        <a href="{{ route('events.calendar') }}" class="border border-[#c21313] hover:bg-[#c21313] hover:text-white px-6 py-3 rounded-lg transition duration-300 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            Calendar View
                        </a>
                        <a href="{{ route('events.custom-calendar') }}" class="border border-[#c21313] hover:bg-[#c21313] hover:text-white px-6 py-3 rounded-lg transition duration-300 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm11 14V8H4v8h12z" clip-rule="evenodd" />
                            </svg>
                            Custom Calendar
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>