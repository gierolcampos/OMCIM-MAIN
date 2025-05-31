@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">Event Card Examples</h1>
            <p class="text-sm text-gray-500 mt-1">Different styles and variations of event cards</p>
        </div>

        <div class="space-y-6">
            <!-- Example 1: Basic Event Card (Matches Screenshot) -->
            <div>
                <h2 class="text-lg font-medium text-gray-700 mb-3">Basic Event Card (Matches Screenshot)</h2>
                <div class="max-w-md">
                    <x-event-card :event="(object)[
                        'title' => 'Programming Contest',
                        'event_type' => 'Competition',
                        'status' => 'upcoming'
                    ]" />
                </div>
            </div>

            <!-- Example 2: Event Card with Date and Location -->
            <div>
                <h2 class="text-lg font-medium text-gray-700 mb-3">Event Card with Date and Location</h2>
                <x-event-card :event="(object)[
                    'title' => 'Web Development Workshop',
                    'event_type' => 'Workshop',
                    'status' => 'upcoming',
                    'start_date_time' => now()->addDays(5)->setTime(13, 0),
                    'end_date_time' => now()->addDays(5)->setTime(16, 0),
                    'location' => 'Computer Laboratory 3'
                ]" />
            </div>

            <!-- Example 3: Completed Event -->
            <div>
                <h2 class="text-lg font-medium text-gray-700 mb-3">Completed Event</h2>
                <x-event-card :event="(object)[
                    'title' => 'Python Basics',
                    'event_type' => 'Training',
                    'status' => 'completed',
                    'start_date_time' => now()->subDays(10)->setTime(9, 0),
                    'end_date_time' => now()->subDays(10)->setTime(12, 0),
                    'location' => 'Online via Zoom'
                ]" />
            </div>

            <!-- Example 4: Without Action Buttons -->
            <div>
                <h2 class="text-lg font-medium text-gray-700 mb-3">Without Action Buttons</h2>
                <x-event-card
                    :event="(object)[
                        'title' => 'Data Science Seminar',
                        'event_type' => 'Seminar',
                        'status' => 'upcoming',
                        'start_date_time' => now()->addDays(15)->setTime(14, 0),
                        'end_date_time' => now()->addDays(15)->setTime(17, 0),
                        'location' => 'Auditorium'
                    ]"
                    :showActions="false"
                />
            </div>
        </div>
    </div>
</div>
@endsection
