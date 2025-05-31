@props(['event', 'showActions' => true])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 overflow-hidden']) }}>
    <div class="p-5">
        <div class="flex items-center">
            <!-- Event Icon -->
            <div class="flex-shrink-0 w-16 h-16 bg-red-50 rounded-lg flex items-center justify-center mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#c21313]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>

            <!-- Event Content -->
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $event->title ?? 'Programming Contest' }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $event->event_type ?? 'Competition' }}</p>
                    </div>

                    <div class="mt-2 sm:mt-0 sm:ml-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            {{ isset($event->status) && $event->status === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                            {{ isset($event->status) ? ucfirst($event->status) : 'Upcoming' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($showActions)
        <div class="mt-5 grid grid-cols-2 gap-4">
            <a href="{{ isset($event) ? route('events.edit', $event) : '#' }}"
               class="flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Event
            </a>

            <form action="{{ isset($event) ? route('events.destroy', $event) : '#' }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this event?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Event
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
