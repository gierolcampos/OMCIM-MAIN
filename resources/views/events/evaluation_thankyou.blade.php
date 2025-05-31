@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6 p-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center h-20 w-20 bg-green-100 rounded-full mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-gray-800 mb-2">Thank You for Your Feedback!</h1>
                <p class="text-gray-600 mb-6">Your evaluation for "{{ $event->title }}" has been successfully submitted.</p>
                <p class="text-gray-500 mb-8">Your feedback helps us improve future events and activities.</p>
                
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('events.show', $event) }}" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-50 transition duration-200">
                        Return to Event
                    </a>
                    <a href="{{ route('events.custom-calendar') }}" class="bg-[#c21313] text-white px-6 py-2 rounded-md hover:bg-red-700 transition duration-200">
                        Browse More Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
