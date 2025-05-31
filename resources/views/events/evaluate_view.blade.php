@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">View Evaluation Form</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
            </div>
            <a href="{{ route('events.show', $event) }}" class="bg-white border border-gray-300 text-gray-700 text-sm py-2 px-4 rounded-md flex items-center transition duration-200 hover:bg-gray-50 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Event
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-800">Evaluation Form Preview</h3>
                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $event->isEvaluationOpen() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $event->isEvaluationOpen() ? 'Open' : 'Closed' }}
                </span>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-gray-700">
                        This is a preview of the evaluation form that will be shown to members. Members can only submit this form when the evaluation status is set to "Open".
                    </p>
                </div>
            </div>
        </div>
            
        @if($questions->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-gray-500 mb-2">No evaluation questions have been created for this event.</p>
                    <a href="{{ route('events.questions.create', $event) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Questions
                    </a>
                </div>
            </div>
        @else
            <!-- Rating Scale Legend -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-800">Rating Scale</h3>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 p-3 rounded-md">
                        <p class="text-sm text-gray-700">1 = Poor</p>
                        <p class="text-sm text-gray-700">2 = Fair</p>
                        <p class="text-sm text-gray-700">3 = Good</p>
                        <p class="text-sm text-gray-700">4 = Very Good</p>
                        <p class="text-sm text-gray-700">5 = Excellent</p>
                    </div>
                </div>
            </div>

            <!-- Dynamic Questions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-800">Evaluation Questions</h3>
                </div>
                <div class="p-6">
                    @foreach($questions as $question)
                        <div class="mb-6 pb-6 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <label class="block text-base font-medium text-gray-800 mb-3">
                                {{ $question->question_text }} 
                                @if($question->is_required)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            
                            @if($question->isRatingType())
                                <div class="grid grid-cols-5 gap-4 text-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div>
                                            <p class="mb-2 text-sm">{{ $i }}</p>
                                            <div class="inline-flex justify-center">
                                                <div class="h-5 w-5 rounded-full border border-gray-300 bg-gray-50"></div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Rating question (1-5 scale)</p>
                            @else
                                <div class="w-full h-24 rounded-md border border-gray-300 bg-gray-50"></div>
                                <p class="mt-2 text-xs text-gray-500">Text question (free response)</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Privacy Agreement -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-800">Privacy Agreement</h3>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <label class="block text-base font-medium text-gray-800 mb-3">
                            Data Privacy Notice: Any and all information provided through this system shall be used for the event evaluation only and will not be shared with any other outside parties, in compliance with the Republic Act No. 10173 otherwise known as Data Privacy Act of 2012. <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <div class="h-5 w-5 rounded border border-gray-300 bg-gray-50"></div>
                                <span class="ml-2 text-gray-700">I agree.</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('events.questions.index', $event) }}" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-50 transition duration-300">
                    Edit Questions
                </a>
                
                <button type="button" disabled class="bg-gray-300 text-white px-6 py-2 rounded-lg cursor-not-allowed">
                    Submit Evaluation (Preview Only)
                </button>
            </div>
        @endif
    </div>
</div>
@endsection
