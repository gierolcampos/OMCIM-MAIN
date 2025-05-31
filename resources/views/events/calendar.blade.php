@extends('layouts.app')

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Calendar View</h1>
                <p class="text-sm text-gray-500 mt-1">View all scheduled events</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('omcms.events') }}" class="border border-[#c21313] hover:bg-[#c21313] hover:text-white px-6 py-2 text-sm rounded-lg transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    List View
                </a>
                <a href="{{ route('events.custom-calendar') }}" class="border border-[#c21313] hover:bg-[#c21313] hover:text-white px-6 py-2 text-sm rounded-lg transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm11 14V8H4v8h12z" clip-rule="evenodd" />
                    </svg>
                    Custom Calendar
                </a>

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

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Include FullCalendar.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

<!-- Include jQuery and Bootstrap for tooltips -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* Custom styles for event tooltips */
    .fc-tooltip {
        max-width: 300px;
    }
    .fc-tooltip-image {
        margin-bottom: 10px;
        text-align: center;
    }
    .fc-tooltip-content {
        padding: 5px;
    }
    .tooltip-inner {
        max-width: 300px;
        padding: 10px;
        background-color: white;
        color: #333;
        border: 1px solid #ddd;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #ddd;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                @foreach($events as $event)
                {
                    id: '{{ $event->id }}',
                    title: '{{ $event->title }}',
                    start: '{{ $event->start_date_time }}',
                    end: '{{ $event->end_date_time }}',
                    url: '{{ route('events.show', $event->id) }}',
                    backgroundColor: '{{ $event->status === "upcoming" ? "#EF4444" : ($event->status === "completed" ? "#6B7280" : "#DC2626") }}',
                    borderColor: '{{ $event->status === "upcoming" ? "#EF4444" : ($event->status === "completed" ? "#6B7280" : "#DC2626") }}',
                    extendedProps: {
                        image_path: '{{ $event->image_path }}',
                        location: '{{ $event->location }}',
                        description: '{{ Str::limit($event->description, 100) }}'
                    }
                },
                @endforeach
            ],
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: 'short'
            },
            eventDidMount: function(info) {
                // Create tooltip with image if available
                const eventEl = info.el;
                const event = info.event;

                // Create tooltip content
                let tooltipContent = `<div class="fc-tooltip">`;

                // Add image if available
                if (event.extendedProps.image_path) {
                    tooltipContent += `<div class="fc-tooltip-image">
                        <img src="/storage/${event.extendedProps.image_path}" alt="${event.title}" style="max-width: 100%; max-height: 150px; object-fit: cover;">
                    </div>`;
                }

                // Add event details
                tooltipContent += `<div class="fc-tooltip-content">
                    <h3 style="margin: 5px 0; font-weight: bold;">${event.title}</h3>
                    <p style="margin: 3px 0; font-size: 0.9em;">${event.extendedProps.description || 'No description available'}</p>
                    <p style="margin: 3px 0; font-size: 0.9em;"><strong>Location:</strong> ${event.extendedProps.location}</p>
                </div></div>`;

                // Store tooltip content as a data attribute
                $(eventEl).attr('data-bs-toggle', 'tooltip');
                $(eventEl).attr('data-bs-html', 'true');
                $(eventEl).attr('data-bs-placement', 'top');
                $(eventEl).attr('title', tooltipContent);
            }
        });
        calendar.render();

        // Initialize Bootstrap tooltips after calendar is rendered
        setTimeout(() => {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    container: 'body'
                });
            });
        }, 100);
    });
</script>
@endsection