@extends('layouts.app')

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('styles')
<link rel="stylesheet" href="{{ asset('css/custom-calendar.css') }}">
<style>
    /* Additional inline styles for calendar */
    .calendar-grid {
        margin-bottom: 1rem;
    }

    .calendar-day {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        cursor: pointer;
        position: relative;
        border-radius: 0;
        margin: 1px;
    }

    .calendar-day:hover {
        background-color: #f3f4f6;
    }

    .calendar-day.other-month {
        color: #d1d5db;
    }

    .calendar-day.selected {
        background-color: #c21313 !important;
        color: white !important;
        font-weight: bold;
    }

    .calendar-day.today {
        border: 1px solid #c21313;
        font-weight: bold;
    }

    .calendar-day.has-events {
        position: relative;
    }

    .calendar-day.has-events::after {
        content: '';
        position: absolute;
        bottom: 2px;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: #c21313;
    }

    .calendar-day.selected.has-events::after {
        background-color: white;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between space-y-4 md:space-y-0 mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">ICS Events</h1>
                <p class="text-sm text-gray-500 mt-1">View and manage all club events</p>
                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        @if(request()->filled('school_calendar_id') && isset($schoolCalendars))
                            @php
                                $selectedCalendar = $schoolCalendars->firstWhere('id', request('school_calendar_id'));
                            @endphp
                            @if($selectedCalendar)
                                {{ $selectedCalendar->school_calendar_desc }}
                            @elseif(isset($currentCalendar))
                                {{ $currentCalendar->school_calendar_desc }}
                            @else
                                All Academic Years
                            @endif
                        @elseif(isset($currentCalendar))
                            {{ $currentCalendar->school_calendar_desc }}
                        @else
                            All Academic Years
                        @endif
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @if(Auth::user()->canManageEvents())
                <a href="{{ route('events.create') }}" class="border border-[#c21313] hover:bg-[#c21313] hover:text-white px-6 py-2 text-sm rounded-lg transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Event
                </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Calendar Section -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 sticky top-8">
                    <!-- Search and Filter -->
                    <div class="mb-4">
                        <div class="flex">
                            <div class="flex-1">
                                <input type="text" id="searchInput" placeholder="Search Events..." class="w-full border border-gray-300 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#c21313] transition duration-200">
                            </div>
                            <button type="button" id="searchButton" class="bg-[#c21313] text-white px-4 py-2 rounded-r-md text-sm hover:bg-[#a51010] transition duration-200">
                                Search
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <form action="{{ route('events.custom-calendar') }}" method="GET">
                            <div class="flex">
                                <div class="flex-1">
                                    <select name="school_calendar_id" id="school_calendar_id"
                                            class="w-full border border-gray-300 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#c21313] transition duration-200">
                                        @if(Auth::user()->isAdmin() && isset($schoolCalendars) && count($schoolCalendars) > 0)
                                            <option value="">All Academic Years</option>
                                            @foreach($schoolCalendars as $calendar)
                                                <option value="{{ $calendar->id }}" {{ request('school_calendar_id') == $calendar->id ? 'selected' : ((!request()->has('school_calendar_id') && $calendar->is_selected) ? 'selected' : '') }}>
                                                    {{ $calendar->school_calendar_short_desc }}
                                                    @if($calendar->is_selected) (Current) @endif
                                                </option>
                                            @endforeach
                                        @elseif(isset($currentCalendar))
                                            <option value="{{ $currentCalendar->id }}" selected>
                                                {{ $currentCalendar->school_calendar_short_desc }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                                <button type="submit" class="bg-[#c21313] text-white px-4 py-2 rounded-r-md text-sm hover:bg-[#a51010] transition duration-200">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Month Navigation -->
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-800">
                            <span id="currentMonthYear">{{ now()->format('F Y') }}</span>
                        </h2>
                        <div class="flex space-x-2">
                            <button id="prevMonth" class="p-1 text-gray-600 hover:text-[#c21313]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button id="nextMonth" class="p-1 text-gray-600 hover:text-[#c21313]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="calendar-grid">
                        <!-- Days of Week -->
                        <div class="grid grid-cols-7 text-center text-xs font-medium text-gray-500 mb-2">
                            <div class="py-1">S</div>
                            <div class="py-1">M</div>
                            <div class="py-1">T</div>
                            <div class="py-1">W</div>
                            <div class="py-1">T</div>
                            <div class="py-1">F</div>
                            <div class="py-1">S</div>
                        </div>

                        <!-- Calendar Days -->
                        <div id="calendarDays" class="grid grid-cols-7 gap-0 text-center">
                            <!-- Calendar days will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 space-y-2">
                        <!-- Calendar view is now the only view -->
                    </div>
                </div>
            </div>

            <!-- Events List Section -->
            <div class="md:col-span-2">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Events</h2>

                <!-- Static Events List (for testing) -->
                <div class="space-y-4 mb-6">
                    @php
                        // Sort events by status first (upcoming, then completed, then cancelled)
                        // Then sort by start_date_time (newest first)
                        $sortedEvents = $events->sort(function($a, $b) {
                            // First sort by status
                            $statusOrder = ['upcoming' => 1, 'completed' => 2, 'cancelled' => 3];
                            $statusDiff = $statusOrder[$a->status] - $statusOrder[$b->status];

                            if ($statusDiff !== 0) return $statusDiff;

                            // If same status, sort by date (newest first)
                            return $b->start_date_time <=> $a->start_date_time;
                        });
                    @endphp

                    @foreach($sortedEvents as $event)
                    <div class="event-card bg-white rounded-lg shadow-sm overflow-hidden flex cursor-pointer border border-gray-200 hover:border-[#c21313] transition-all duration-300"
                         data-event-id="{{ $event->id }}"
                         data-event-title="{{ $event->title }}"
                         data-event-status="{{ $event->isPending() ? 'pending' : $event->status }}"
                         data-event-end-date="{{ $event->end_date_time }}">
                        <div class="w-1/4 md:w-1/6 h-32 overflow-hidden">
                            @if($event->image_path)
                                @php
                                    $imagePath = $event->image_path;
                                    // Check if it's a base64 file
                                    if (strpos($imagePath, 'base64/') === 0 && file_exists(public_path($imagePath))) {
                                        $base64Content = file_get_contents(public_path($imagePath));
                                        $src = $base64Content;
                                    } else {
                                        $src = asset($imagePath);
                                    }
                                @endphp
                                <img src="{{ $src }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=250&q=80" alt="{{ $event->title }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="p-4 w-3/4 md:w-5/6">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-[#c21313] font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $event->start_date_time->format('M j') }} | {{ $event->start_date_time->format('g:i A') }} - {{ $event->end_date_time->format('g:i A') }}
                                </div>
                                <div class="text-xs font-semibold px-3 py-1 rounded-full
                                    @if($event->isPending()) bg-amber-100 text-amber-800
                                    @elseif($event->status == 'upcoming') bg-green-100 text-green-800
                                    @elseif($event->status == 'completed') bg-gray-200 text-gray-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $event->isPending() ? 'Pending' : ucfirst($event->status) }}
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold mb-1">{{ $event->title }}</h3>
                            <p class="text-gray-600 text-sm line-clamp-2">{{ $event->description ?? 'No description available.' }}</p>
                            <div class="mt-2 flex items-center text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-500">{{ $event->location }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Dynamic Events List (populated by JavaScript) -->
                <div id="eventsList" class="space-y-6 hidden">
                    <!-- Events will be populated by JavaScript -->
                </div>

                <!-- No Events Message (hidden by default) -->
                <div id="noEventsMessage" class="hidden bg-white rounded-lg shadow-sm border border-gray-100 p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 mb-2">No events found</h3>
                    <p class="text-gray-500" id="noEventsText">There are no events scheduled for the selected date.</p>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sample events data (will be replaced with actual data from backend)
        const events = @json($events);

        // Current date state
        let currentDate = new Date(); // Use current date instead of hardcoded May 2025
        let selectedDate = null;

        // Initialize calendar
        renderCalendar(currentDate);

        // Log events to console for debugging
        console.log('Events data:', events);

        // Add click event to static event cards
        document.querySelectorAll('.space-y-4.mb-6 .event-card').forEach(card => {
            card.addEventListener('click', function() {
                const eventTitle = this.querySelector('.text-lg.font-semibold').textContent;
                const matchingEvent = events.find(e => e.title === eventTitle);

                if (matchingEvent) {
                    window.location.href = `/events/${matchingEvent.id}`;
                }
            });
        });

        // Add search functionality
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');

        // Search when button is clicked
        searchButton.addEventListener('click', function() {
            performSearch();
        });

        // Search when Enter key is pressed
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        // Function to perform search
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();

            // Get all static event cards
            const staticEventCards = document.querySelectorAll('.space-y-4.mb-6 .event-card');
            const staticEventsContainer = document.querySelector('.space-y-4.mb-6');

            if (searchTerm === '') {
                // If search is empty, show all static events
                staticEventCards.forEach(card => {
                    card.style.display = '';
                });

                // Show static events container
                staticEventsContainer.style.display = '';

                // Hide no events message
                document.getElementById('noEventsMessage').classList.add('hidden');
                return;
            }

            // Filter static event cards based on search term
            let matchFound = false;

            staticEventCards.forEach(card => {
                const title = card.querySelector('.text-lg.font-semibold').textContent.toLowerCase();
                const description = card.querySelector('.text-gray-600.text-sm').textContent.toLowerCase();
                const location = card.querySelector('.text-gray-500').textContent.toLowerCase();
                const dateInfo = card.querySelector('.text-\\[\\#c21313\\]').textContent.toLowerCase();

                if (
                    title.includes(searchTerm) ||
                    description.includes(searchTerm) ||
                    location.includes(searchTerm) ||
                    dateInfo.includes(searchTerm)
                ) {
                    card.style.display = '';
                    matchFound = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no events message based on search results
            if (!matchFound) {
                document.getElementById('noEventsMessage').classList.remove('hidden');
                document.getElementById('noEventsText').textContent = `No events found matching your search.`;
            } else {
                document.getElementById('noEventsMessage').classList.add('hidden');
            }

            // Clear any selected date
            document.querySelectorAll('.calendar-day.selected').forEach(el => {
                el.classList.remove('selected');
            });
            selectedDate = null;
        }

        // Event listeners for month navigation
        document.getElementById('prevMonth').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });

        // Function to render calendar
        function renderCalendar(date) {
            const year = date.getFullYear();
            const month = date.getMonth();

            // Update month/year display
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            document.getElementById('currentMonthYear').textContent = `${monthNames[month]} ${year}`;

            // Get first day of month and total days
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const totalDays = lastDay.getDate();

            // Get day of week for first day (0 = Sunday, which is correct for Sunday start)
            let firstDayOfWeek = firstDay.getDay();

            // Get days from previous month
            const prevMonthLastDay = new Date(year, month, 0).getDate();

            // Clear calendar
            const calendarDays = document.getElementById('calendarDays');
            calendarDays.innerHTML = '';

            // Add days from previous month
            for (let i = 0; i < firstDayOfWeek; i++) {
                const dayNum = prevMonthLastDay - firstDayOfWeek + i + 1;
                const dayEl = createDayElement(dayNum, 'other-month', new Date(year, month - 1, dayNum));
                calendarDays.appendChild(dayEl);
            }

            // Add days from current month
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Normalize today's date for comparison

            for (let i = 1; i <= totalDays; i++) {
                const currentDateObj = new Date(year, month, i);
                currentDateObj.setHours(0, 0, 0, 0); // Normalize for comparison

                const isToday = today.getTime() === currentDateObj.getTime();

                // Check if this date has events
                const hasEvents = events.some(event => {
                    const eventDate = new Date(event.start_date_time);
                    return eventDate.getDate() === i &&
                           eventDate.getMonth() === month &&
                           eventDate.getFullYear() === year;
                });

                // Check if this date is selected
                const isSelected = selectedDate &&
                    selectedDate.getDate() === i &&
                    selectedDate.getMonth() === month &&
                    selectedDate.getFullYear() === year;

                // Build class list
                const classes = [];
                if (isToday) classes.push('today');
                if (hasEvents) classes.push('has-events');
                if (isSelected) classes.push('selected');

                const dayEl = createDayElement(i, classes.join(' '), currentDateObj);

                // Apply special styling for today and selected dates
                if (isSelected) {
                    dayEl.style.backgroundColor = '#c21313';
                    dayEl.style.color = 'white';
                    dayEl.style.border = '1px solid #c21313';
                } else if (isToday) {
                    dayEl.style.border = '1px solid #c21313';
                }

                calendarDays.appendChild(dayEl);
            }

            // Add days from next month
            const totalCells = 35; // 5 rows of 7 days (or 42 for 6 rows if needed)
            const remainingCells = totalCells - (firstDayOfWeek + totalDays);

            // Only add next month days if we need them
            if (remainingCells > 0) {
                for (let i = 1; i <= remainingCells; i++) {
                    const dayEl = createDayElement(i, 'other-month', new Date(year, month + 1, i));
                    calendarDays.appendChild(dayEl);
                }
            }
        }

        // Function to create a day element
        function createDayElement(dayNum, classes, dateObj) {
            const dayEl = document.createElement('div');
            dayEl.className = `calendar-day ${classes}`;
            dayEl.textContent = dayNum;
            dayEl.setAttribute('data-day', dayNum);

            // Add a red border for selected dates
            if (classes.includes('selected')) {
                dayEl.style.border = '1px solid #c21313';
            } else if (classes.includes('today')) {
                // Add a subtle border for today's date if not selected
                dayEl.style.border = '1px solid #e5e7eb';
            }

            // Make sure all days have the red hover effect
            dayEl.addEventListener('mouseover', function() {
                this.style.backgroundColor = '#c21313';
                this.style.color = 'white';
            });

            dayEl.addEventListener('mouseout', function() {
                if (!this.classList.contains('selected')) {
                    this.style.backgroundColor = '';
                    this.style.color = '';
                }
            });

            // Add a red dot for dates with events
            if (classes.includes('has-events') && !classes.includes('selected')) {
                const dot = document.createElement('div');
                dot.className = 'event-dot';
                dot.style.width = '4px';
                dot.style.height = '4px';
                dot.style.backgroundColor = '#c21313';
                dot.style.borderRadius = '50%';
                dot.style.position = 'absolute';
                dot.style.bottom = '2px';
                dot.style.left = '50%';
                dot.style.transform = 'translateX(-50%)';
                dayEl.appendChild(dot);
            }

            dayEl.addEventListener('click', function() {
                // Remove selected class from all days
                document.querySelectorAll('.calendar-day.selected').forEach(el => {
                    el.classList.remove('selected');
                    el.style.border = '';

                    // Restore event dots for previously selected dates with events
                    if (el.classList.contains('has-events') && !el.querySelector('.event-dot')) {
                        const dot = document.createElement('div');
                        dot.className = 'event-dot';
                        dot.style.width = '4px';
                        dot.style.height = '4px';
                        dot.style.backgroundColor = '#c21313';
                        dot.style.borderRadius = '50%';
                        dot.style.position = 'absolute';
                        dot.style.bottom = '2px';
                        dot.style.left = '50%';
                        dot.style.transform = 'translateX(-50%)';
                        el.appendChild(dot);
                    }
                });

                // Add selected class to clicked day
                dayEl.classList.add('selected');
                dayEl.style.border = '1px solid #c21313';

                // Remove event dot from selected date
                const dot = dayEl.querySelector('.event-dot');
                if (dot) {
                    dot.remove();
                }

                // Update selected date
                selectedDate = dateObj;

                // Filter events for the selected date
                filterEventsByDate(dateObj);
            });

            return dayEl;
        }

        // Function to filter events by date
        function filterEventsByDate(date) {
            // Clear search input
            searchInput.value = '';

            // Get all static event cards
            const staticEventCards = document.querySelectorAll('.space-y-4.mb-6 .event-card');
            const staticEventsContainer = document.querySelector('.space-y-4.mb-6');

            // Format the selected date for comparison
            const selectedDateStr = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;

            // Filter static event cards based on date
            let matchFound = false;

            staticEventCards.forEach(card => {
                // Get the event title
                const eventTitle = card.querySelector('.text-lg.font-semibold').textContent;

                // Find the matching event in the events array
                const matchingEvent = events.find(e => e.title === eventTitle);

                if (matchingEvent) {
                    const eventDate = new Date(matchingEvent.start_date_time);

                    if (
                        eventDate.getDate() === date.getDate() &&
                        eventDate.getMonth() === date.getMonth() &&
                        eventDate.getFullYear() === date.getFullYear()
                    ) {
                        card.style.display = '';
                        matchFound = true;
                    } else {
                        card.style.display = 'none';
                    }
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no events message based on filter results
            if (!matchFound) {
                document.getElementById('noEventsMessage').classList.remove('hidden');
                document.getElementById('noEventsText').textContent = `There are no events scheduled for the selected date.`;
            } else {
                document.getElementById('noEventsMessage').classList.add('hidden');
            }
        }

        // Function to render events list
        function renderEventsList(eventsList) {
            const eventsListEl = document.getElementById('eventsList');
            const noEventsMessage = document.getElementById('noEventsMessage');
            const noEventsText = document.getElementById('noEventsText');
            const searchTerm = searchInput.value.toLowerCase().trim();

            // Clear events list
            eventsListEl.innerHTML = '';

            console.log('Rendering events list:', eventsList);

            if (!eventsList || eventsList.length === 0) {
                // Update the message based on whether we're searching or filtering by date
                if (searchTerm !== '') {
                    noEventsText.textContent = `No events found matching your search.`;
                } else if (selectedDate) {
                    noEventsText.textContent = `There are no events scheduled for the selected date.`;
                } else {
                    noEventsText.textContent = `No events found.`;
                }

                eventsListEl.classList.add('hidden');
                noEventsMessage.classList.remove('hidden');
                return;
            }

            eventsListEl.classList.remove('hidden');
            noEventsMessage.classList.add('hidden');

            // Sort events by status first (upcoming, then completed, then cancelled)
            // Then sort by start_date_time (soonest first for upcoming, latest first for completed)
            eventsList.sort((a, b) => {
                // First sort by status
                const statusOrder = { 'upcoming': 1, 'completed': 2, 'cancelled': 3 };
                const statusDiff = statusOrder[a.status] - statusOrder[b.status];

                if (statusDiff !== 0) return statusDiff;

                // If same status, sort by date
                if (a.status === 'upcoming') {
                    // For upcoming events, sort by soonest first
                    return new Date(a.start_date_time) - new Date(b.start_date_time);
                } else {
                    // For completed/cancelled events, sort by most recent first
                    return new Date(b.start_date_time) - new Date(a.start_date_time);
                }
            });

            // Add events to list with staggered animation
            eventsList.forEach((event, index) => {
                setTimeout(() => {
                    const eventCard = createEventCard(event);
                    eventCard.classList.add('fade-in');
                    eventsListEl.appendChild(eventCard);
                }, index * 100);
            });
        }

        // Function to create an event card
        function createEventCard(event) {
            const eventDate = new Date(event.start_date_time);
            const endDate = new Date(event.end_date_time);

            const card = document.createElement('div');
            card.className = 'event-card bg-white rounded-lg shadow-sm overflow-hidden flex cursor-pointer border border-gray-200 hover:border-[#c21313] transition-all duration-300';
            card.setAttribute('data-event-id', event.id);

            // Default placeholder image if no image_path is available
            const defaultImage = 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=250&q=80';

            // Use event's image_path if available, otherwise use default image
            const eventImage = event.image_path
                ? `/${event.image_path}`
                : defaultImage;

            // Check if event is pending (past end date but still marked as upcoming)
            const now = new Date();
            const isPending = event.status === 'upcoming' && new Date(event.end_date_time) < now;

            card.innerHTML = `
                <div class="w-1/4 md:w-1/6 event-image h-32 overflow-hidden">
                    <img src="${eventImage}" alt="${event.title}" class="w-full h-full object-cover">
                </div>
                <div class="p-4 w-3/4 md:w-5/6">
                    <div class="flex justify-between items-start mb-2">
                        <div class="event-date text-[#c21313] font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            ${formatDate(eventDate)} | ${formatTime(eventDate)} - ${formatTime(endDate)}
                        </div>
                        <div class="text-xs font-semibold px-3 py-1 rounded-full
                            ${isPending ? 'bg-amber-100 text-amber-800' :
                            event.status === 'upcoming' ? 'bg-green-100 text-green-800' :
                            event.status === 'completed' ? 'bg-gray-200 text-gray-800' :
                            'bg-red-100 text-red-800'}">
                            ${
                            isPending ? 'Pending' :
                            event.status === 'upcoming' ? 'Upcoming' :
                            event.status === 'completed' ? 'Completed' : 'Cancelled'
                            }
                        </div>
                    </div>
                    <h3 class="event-title text-lg font-semibold mb-1">${event.title}</h3>
                    <p class="text-gray-600 text-sm line-clamp-2">${event.description || 'No description available.'}</p>
                    <div class="mt-2 flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-500">${event.location}</span>
                    </div>
                </div>
            `;

            // Add click event to navigate to event details
            card.addEventListener('click', function() {
                window.location.href = `/events/${event.id}`;
            });

            return card;
        }

        // Helper function to format date
        function formatDate(date) {
            const options = { month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Helper function to format time
        function formatTime(date) {
            const options = { hour: 'numeric', minute: '2-digit', hour12: true };
            return date.toLocaleTimeString('en-US', options);
        }
    });
</script>
@endsection
