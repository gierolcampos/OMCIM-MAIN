@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Event Evaluation</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
            </div>
            <a href="{{ route('events.show', $event) }}" class="bg-white border border-gray-300 text-gray-700 text-sm py-2 px-4 rounded-md flex items-center transition duration-200 hover:bg-gray-50 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Event
            </a>
        </div>

        <form action="{{ route('events.evaluate.submit', $event) }}" method="POST">
            @csrf

            <!-- Event Rating Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-800">Event Rating</h3>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <h4 class="text-base font-medium text-gray-800 mb-4">Please rate the following aspects of the {{ $event->title }} using the scale below:</h4>
                        <div class="bg-gray-50 p-3 rounded-md mb-4">
                            <p class="text-sm text-gray-700">1 = Poor</p>
                            <p class="text-sm text-gray-700">2 = Fair</p>
                            <p class="text-sm text-gray-700">3 = Good</p>
                            <p class="text-sm text-gray-700">4 = Very Good</p>
                            <p class="text-sm text-gray-700">5 = Excellent</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-base font-medium text-gray-800 mb-3">
                            How would you rate the overall quality of this event? <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-5 gap-4 text-center">
                            <div>
                                <p class="mb-2 text-sm">1</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="overall_quality" value="1" required class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">2</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="overall_quality" value="2" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">3</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="overall_quality" value="3" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">4</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="overall_quality" value="4" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">5</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="overall_quality" value="5" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-base font-medium text-gray-800 mb-3">
                            How relevant and engaging was the content presented during the event? <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-5 gap-4 text-center">
                            <div>
                                <p class="mb-2 text-sm">1</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="content_relevance" value="1" required class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">2</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="content_relevance" value="2" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">3</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="content_relevance" value="3" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">4</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="content_relevance" value="4" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">5</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="content_relevance" value="5" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-base font-medium text-gray-800 mb-3">
                            How enjoyable and engaging were the activities during the event? <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-5 gap-4 text-center">
                            <div>
                                <p class="mb-2 text-sm">1</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="activities_enjoyment" value="1" required class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">2</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="activities_enjoyment" value="2" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">3</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="activities_enjoyment" value="3" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">4</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="activities_enjoyment" value="4" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                            <div>
                                <p class="mb-2 text-sm">5</p>
                                <label class="inline-flex justify-center">
                                    <input type="radio" name="activities_enjoyment" value="5" class="form-radio h-5 w-5 text-red-600">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="concerns" class="block text-base font-medium text-gray-800 mb-2">
                            Do you have concerns that you want to raise about this event? <span class="text-red-500">*</span>
                        </label>
                        <textarea id="concerns" name="concerns" rows="3" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50" placeholder="Your concerns..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Feedback Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-800">Feedback</h3>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <label for="feedback" class="block text-base font-medium text-gray-800 mb-2">
                            Please provide any feedback or suggestion for the improvement of our events. <span class="text-red-500">*</span>
                        </label>
                        <textarea id="feedback" name="feedback" rows="3" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50" placeholder="Your feedback..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Privacy Agreement -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-800">Privacy Agreement</h3>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <h4 class="text-base font-medium text-gray-800 mb-4">The Data Privacy Act of 2012, also known as Republic Act No. 10173</h4>
                        <p class="text-sm text-gray-600 mb-4">Description (optional)</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-base font-medium text-gray-800 mb-3">
                            Data Privacy Notice: Any and all information provided through this system shall be used for the event evaluation only and will not be shared with any other outside parties, in compliance with the Republic Act No. 10173 otherwise known as Data Privacy Act of 2012. <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="privacy_agreement" value="1" required class="form-checkbox h-5 w-5 text-red-600">
                                <span class="ml-2 text-gray-700">I agree.</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-[#c21313] text-white px-6 py-2 rounded-lg hover:bg-red-700 transition duration-300">
                    Submit Evaluation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
