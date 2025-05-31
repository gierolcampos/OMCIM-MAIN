@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Edit Event</h1>
                <p class="text-sm text-gray-500 mt-1">Update event details</p>
            </div>
            <a href="{{ route('omcms.events') }}" class="bg-white border border-gray-300 text-gray-700 text-sm py-2 px-4 rounded-md flex items-center transition duration-200 hover:bg-gray-50 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Events
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <!-- Event Details Section -->
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Event Details</h2>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-4">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Event Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $event->title) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('title') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Type -->
                        <div>
                            <label for="event_type" class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                            <select name="event_type" id="event_type"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('event_type') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <option value="" disabled>Select event type</option>
                                <option value="General Assembly" {{ old('event_type', $event->event_type) == 'General Assembly' ? 'selected' : '' }}>General Assembly</option>
                                <option value="Tech Talks / Guest Lectures" {{ old('event_type', $event->event_type) == 'Tech Talks / Guest Lectures' ? 'selected' : '' }}>Tech Talks / Guest Lectures</option>
                                <option value="Hackathons" {{ old('event_type', $event->event_type) == 'Hackathons' ? 'selected' : '' }}>Hackathons</option>
                                <option value="Coding Competitions" {{ old('event_type', $event->event_type) == 'Coding Competitions' ? 'selected' : '' }}>Coding Competitions</option>
                                <option value="Community Outreach / ICT4D Events" {{ old('event_type', $event->event_type) == 'Community Outreach / ICT4D Events' ? 'selected' : '' }}>Community Outreach / ICT4D Events</option>
                                <option value="Cybersecurity Drills" {{ old('event_type', $event->event_type) == 'Cybersecurity Drills' ? 'selected' : '' }}>Cybersecurity Drills</option>
                                <option value="Other" {{ old('event_type', $event->event_type) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('event_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Date & Time Section -->
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Date & Time</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <!-- Start Date & Time -->
                        <div>
                            <label for="start_date_time" class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
                            <input type="datetime-local" name="start_date_time" id="start_date_time"
                                value="{{ old('start_date_time', $event->start_date_time->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('start_date_time') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('start_date_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date & Time -->
                        <div>
                            <label for="end_date_time" class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                            <input type="datetime-local" name="end_date_time" id="end_date_time"
                                value="{{ old('end_date_time', $event->end_date_time->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('end_date_time') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('end_date_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location Section -->
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Location</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $event->location) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('location') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location Details -->
                        <div>
                            <label for="location_details" class="block text-sm font-medium text-gray-700 mb-1">Location Details</label>
                            <input type="text" name="location_details" id="location_details" value="{{ old('location_details', $event->location_details) }}" placeholder="Building, room, etc."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('location_details') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('location_details')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Additional Information</h2>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-4">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Event Status</label>
                            <select name="status" id="status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('status') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <option value="upcoming" {{ old('status', $event->status) == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="completed" {{ old('status', $event->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" id="notes" rows="3" placeholder="Additional information about the event"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('notes') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('notes', $event->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Image -->
                        <div class="mt-4">
                            <label for="event_image" class="block text-sm font-medium text-gray-700 mb-1">Event Image</label>

                            @if($event->image_path)
                                <div class="mb-2">
                                    <p class="text-sm text-gray-600 mb-1">Current image:</p>
                                    <img src="{{ asset('storage/' . $event->image_path) }}" alt="Event image" class="w-full max-w-md rounded-md border border-gray-200">
                                </div>
                            @endif

                            <input type="file" name="event_image" id="event_image" accept=".jpg,.jpeg"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 @error('event_image') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Upload a new JPG image for the event (optional)</p>
                            @error('event_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 mt-8">
                    <a href="{{ route('omcms.events') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="border border-[#c21313] hover:bg-[#c21313] hover:text-white px-6 py-2 text-sm rounded-lg transition duration-300">
                        Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection