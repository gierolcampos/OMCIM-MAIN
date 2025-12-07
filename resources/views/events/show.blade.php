@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Event Details</h1>
                <p class="text-sm text-gray-500 mt-1">View event information</p>
            </div>
            <a href="{{ route('omcms.events') }}" class="bg-white border border-gray-300 text-gray-700 text-sm py-2 px-4 rounded-md flex items-center transition duration-200 hover:bg-gray-50 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Events
            </a>
        </div>

        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex items-start gap-6">
                    <div class="flex-shrink-0">
                        <div class="h-20 w-20 bg-red-100 rounded-md flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">{{ $event->title }}</h2>
                                <p class="text-sm text-gray-500 mt-1">{{ $event->event_type ?? 'Event' }}</p>
                            </div>
                            <div>
                                @if($event->isPending())
                                <span class="px-3 py-1 text-sm font-medium rounded-full bg-amber-100 text-amber-800">
                                    Pending
                                </span>
                                @elseif($event->status === 'upcoming')
                                <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                                    Upcoming
                                </span>
                                @elseif($event->status === 'completed')
                                <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">
                                    Completed
                                </span>
                                @else
                                <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">
                                    Cancelled
                                </span>
                                @endif
                            </div>
                        </div>

                        @if(Auth::user()->isAdmin())
                        <div class="flex items-center gap-3 mt-4">
                            <a href="{{ route('events.edit', $event) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit Event
                            </a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-white border border-red-300 rounded-md text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Delete Event
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Event Details -->
            <div class="lg:col-span-2 order-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-800">Event Information</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ strtotime($event->start_date_time) > time() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ strtotime($event->start_date_time) > time() ? 'Upcoming' : 'Past Event' }}
                        </span>
                    </div>
                    <div class="p-6">
                        <!-- Event Title -->
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $event->title }}</h2>
                            <div class="flex flex-wrap items-center text-sm text-gray-500 gap-x-4 gap-y-2">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ isset($event->start_date_time) ? $event->start_date_time->format('M j, Y') : 'Date not set' }}
                                </div>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ isset($event->start_date_time) ? $event->start_date_time->format('g:i A') : '' }}
                                    {{ isset($event->start_date_time) && isset($event->end_date_time) ? '-' : '' }}
                                    {{ isset($event->end_date_time) ? $event->end_date_time->format('g:i A') : '' }}
                                </div>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $event->location ?? 'Location not set' }}
                                </div>
                            </div>
                        </div>

                        <!-- Event Image (Mobile: Top, Desktop: After Description) -->
                        @if($event->image_path)
                        <div class="mb-6 md:hidden">
                            <div class="rounded-lg overflow-hidden shadow-sm">
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
                                <img src="{{ $src }}" alt="Event image" class="w-full h-auto object-cover">
                            </div>
                        </div>
                        @endif

                        <!-- Description -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Description</h4>
                            <div class="text-gray-700 prose max-w-none">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                        </div>

                        <!-- Event Image (Desktop) -->
                        @if($event->image_path)
                        <div class="mb-6 hidden md:block">
                            <div class="rounded-lg overflow-hidden shadow-sm">
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
                                <img src="{{ $src }}" alt="Event image" class="w-full h-auto object-cover max-h-96">
                            </div>
                        </div>
                        @endif

                        <!-- Additional Notes -->
                        @if($event->notes)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Additional Notes</h4>
                            <div class="text-gray-700 prose max-w-none bg-gray-50 p-4 rounded-lg">
                                {!! nl2br(e($event->notes)) !!}
                            </div>
                        </div>
                        @endif

                        <!-- Event Details Footer -->
                        <div class="mt-8 pt-4 border-t border-gray-100">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-gray-600">
                                        Created by: <span class="font-medium text-gray-900">{{ $event->creator ? $event->creator->name : 'Unknown user' }}</span>
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-gray-600">
                                        Created: <span class="font-medium">{{ $event->created_at ? $event->created_at->format('M d, Y') : 'Unknown date' }}</span>
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span class="text-gray-600">
                                        Updated: <span class="font-medium">{{ $event->updated_at ? $event->updated_at->format('M d, Y') : 'Unknown date' }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event RSVP Form -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mt-6 order-3 lg:order-2">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-800">Are you attending?</h3>
                        @php
                            $attendance = $event->attendances()->where('user_id', Auth::id())->first();
                            $status = $attendance?->status;
                            $comment = $attendance?->comment ?? '';
                        @endphp

                        @if($status)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $status === 'attending' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $status === 'attending' ? 'Attending' : 'Not Attending' }}
                            </span>
                        @endif
                    </div>
                    <div class="p-6">
                        <form id="attendanceForm" action="{{ route('events.attend', $event) }}" method="POST">
                            @csrf

                            @if($status)
                                <!-- Already Responded View -->
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            @if($status === 'attending')
                                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900">
                                                You have responded that you {{ $status === 'attending' ? 'will attend' : 'will not attend' }} this event.
                                            </p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Your response has been recorded and cannot be changed.
                                            </p>

                                            @if($comment)
                                                <div class="mt-3 bg-white p-3 rounded-md border border-gray-200">
                                                    <p class="text-xs text-gray-500 mb-1">Your comment:</p>
                                                    <p class="text-sm text-gray-700">{{ $comment }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- RSVP Form -->
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <label class="relative flex items-center p-4 rounded-lg border border-gray-200 cursor-pointer hover:border-gray-300 bg-white">
                                            <input type="radio" name="status" value="attending" class="h-5 w-5 text-[#c21313] focus:ring-[#c21313]">
                                            <div class="ml-3">
                                                <span class="block text-sm font-medium text-gray-900">I'm attending</span>
                                                <span class="block text-xs text-gray-500">I'll be there!</span>
                                            </div>
                                            <div class="absolute -top-2 -right-2 hidden peer-checked:flex">
                                                <div class="w-5 h-5 rounded-full bg-[#c21313] flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="relative flex items-center p-4 rounded-lg border border-gray-200 cursor-pointer hover:border-gray-300 bg-white">
                                            <input type="radio" name="status" value="not_attending" class="h-5 w-5 text-[#c21313] focus:ring-[#c21313]">
                                            <div class="ml-3">
                                                <span class="block text-sm font-medium text-gray-900">I'm not attending</span>
                                                <span class="block text-xs text-gray-500">I can't make it</span>
                                            </div>
                                            <div class="absolute -top-2 -right-2 hidden peer-checked:flex">
                                                <div class="w-5 h-5 rounded-full bg-[#c21313] flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <div>
                                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Comment (optional)</label>
                                        <textarea name="comment" id="comment" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#c21313] focus:ring focus:ring-red-200 focus:ring-opacity-50" placeholder="Add any additional information here...">{{ $comment }}</textarea>
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-4">
                                        <button type="button" id="submitAttendance" class="w-full sm:w-auto bg-[#c21313] text-white px-6 py-2 text-sm rounded-lg hover:bg-red-700 transition duration-300 mb-3 sm:mb-0">
                                            Save Response
                                        </button>
                                        <p class="text-xs text-gray-500 italic">
                                            Note: You cannot change your response after submission.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const submitBtn = document.getElementById('submitAttendance');
                                    const form = document.getElementById('attendanceForm');

                                    if (submitBtn) {
                                        submitBtn.addEventListener('click', function(e) {
                                            e.preventDefault();

                                            // Check if a radio button is selected
                                            const radioButtons = form.querySelectorAll('input[name="status"]');
                                            let isSelected = false;

                                            for (const radioButton of radioButtons) {
                                                if (radioButton.checked) {
                                                    isSelected = true;
                                                    break;
                                                }
                                            }

                                            if (!isSelected) {
                                                alert('Please select whether you are attending or not.');
                                                return;
                                            }

                                            // Show confirmation dialog
                                            if (confirm('Warning: Once you submit your attendance response, you will not be able to change it. Are you sure you want to proceed?')) {
                                                form.submit();
                                            }
                                        });
                                    }
                                });
                            </script>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Event Meta -->
            <div class="order-2 lg:order-3">
                <!-- Event Details Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-medium text-gray-800">Event Details</h3>
                    </div>

                    <!-- Date & Time Section -->
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center mr-3 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#c21313]" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-1">Date & Time</h4>
                                <p class="text-sm text-gray-700">
                                    {{ isset($event->start_date_time) ? $event->start_date_time->format('l, F j, Y') : 'Date not set' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ isset($event->start_date_time) ? $event->start_date_time->format('g:i A') : '' }}
                                    {{ isset($event->start_date_time) && isset($event->end_date_time) ? '-' : '' }}
                                    {{ isset($event->end_date_time) ? $event->end_date_time->format('g:i A') : '' }}
                                </p>
                                @if(isset($event->start_date_time) && isset($event->end_date_time))
                                <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Duration: {{ $event->start_date_time->diffForHumans($event->end_date_time, true) }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center mr-3 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#c21313]" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-1">Location</h4>
                                <p class="text-sm text-gray-700">{{ $event->location ?? 'Location not set' }}</p>
                                @if($event->location_details)
                                <p class="text-sm text-gray-600 mt-1">{{ $event->location_details }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- All Events Link -->
                <div class="mb-6 text-center">
                    @if(Auth::user()->canManageEvents())
                    <a href="{{ route('events.custom-calendar') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        All Events
                    </a>
                    <div class="mt-3 space-x-3">
                        <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center text-sm text-[#c21313] hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                        <a href="{{ route('events.attendees', $event) }}" class="inline-flex items-center text-sm text-[#c21313] hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Attendees
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Event Evaluation Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mt-6 order-4">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-medium text-gray-800">Event Evaluation</h3>
                    </div>
                    <div class="p-6">
                        @php
                            $evaluation = $event->evaluations()->where('user_id', Auth::id())->first();
                            $totalEvaluations = $event->evaluations()->count();
                        @endphp

                        <p class="text-gray-700 mb-6">
                            Your feedback helps us improve future events. Please complete our detailed evaluation form when available.
                        </p>

                        @if(Auth::user()->canManageEvents())
                            <!-- Admin Controls -->
                            <div class="space-y-6">
                                <div class="flex flex-col space-y-0">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                            <div class="mb-3 sm:mb-0">
                                                <h4 class="text-sm font-medium text-gray-700 mb-2">Evaluation Status</h4>
                                                <span class="text-sm {{ $event->isEvaluationOpen() ? 'text-green-600' : 'text-gray-500' }}">
                                                    Status: <strong>{{ $event->isEvaluationOpen() ? 'Open' : 'Closed' }}</strong>
                                                </span>
                                            </div>
                                            <form action="{{ route('events.toggle-evaluation', $event) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-4 py-2 text-sm rounded-lg transition duration-200">
                                                    {{ $event->isEvaluationOpen() ? 'Close Evaluation' : 'Open Evaluation' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4 mt-2">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                            <div class="mb-3 sm:mb-0">
                                                <h4 class="text-sm font-medium text-gray-700 mb-2 mr-4">Responses</h4>
                                                <span class="text-sm text-gray-600">Total: <strong>{{ $totalEvaluations }}</strong></span>
                                            </div>
                                            <a href="{{ route('events.evaluation.respondents', $event) }}" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-4 py-2 text-sm rounded-lg transition duration-200 inline-block text-center">
                                                View All Respondents
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center space-y-4 sm:space-y-0 sm:space-x-6 pt-4 border-t border-gray-100">
                                    <a href="{{ route('events.questions.index', $event) }}" class="inline-flex items-center justify-center text-sm text-[#c21313] hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Manage Evaluation Questions
                                    </a>

                                    <a href="{{ route('events.evaluation.view', $event) }}" class="inline-flex items-center justify-center text-sm text-[#c21313] hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Evaluation Form
                                    </a>
                                </div>
                            </div>
                        @else
                            <!-- Regular Member View -->
                            @if($evaluation)
                                <div class="bg-green-50 border border-green-200 rounded-md p-5">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-green-700">
                                                Thank you! You have already submitted an evaluation for this event.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($event->isEvaluationOpen())
                                <div class="text-center">
                                    <a href="{{ route('events.evaluation', $event) }}" class="bg-[#c21313] text-white px-6 py-2 text-sm rounded-lg hover:bg-red-700 transition duration-300 inline-block">
                                        Complete Evaluation Form
                                    </a>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-md p-5">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-700">
                                                The evaluation form for this event is currently closed.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection