@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Edit Evaluation Question</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
            </div>
            <a href="{{ route('events.questions.index', $event) }}" class="bg-white border border-gray-300 text-gray-700 text-sm py-2 px-4 rounded-md flex items-center transition duration-200 hover:bg-gray-50 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Questions
            </a>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('events.questions.update', [$event, $question]) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="mb-6">
                    <label for="question_text" class="block text-sm font-medium text-gray-700 mb-1">Question Text <span class="text-red-500">*</span></label>
                    <input type="text" name="question_text" id="question_text" value="{{ old('question_text', $question->question_text) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50" placeholder="Enter your question here...">
                    @error('question_text')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="question_type" class="block text-sm font-medium text-gray-700 mb-1">Question Type <span class="text-red-500">*</span></label>
                    <select name="question_type" id="question_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <option value="rating" {{ old('question_type', $question->question_type) === 'rating' ? 'selected' : '' }}>Rating (1-5 scale)</option>
                        <option value="text" {{ old('question_type', $question->question_type) === 'text' ? 'selected' : '' }}>Text (Free response)</option>
                    </select>
                    @error('question_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_required" id="is_required" value="1" {{ old('is_required', $question->is_required) ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="is_required" class="ml-2 block text-sm text-gray-700">Required Question</label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Check this box if an answer to this question is required.</p>
                    @error('is_required')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#c21313] text-white px-6 py-2 rounded-lg hover:bg-red-700 transition duration-300">
                        Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
