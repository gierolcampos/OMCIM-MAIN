@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit School Calendar</h1>
        <a href="{{ route('admin.school-calendars.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Back to School Calendars
        </a>
    </div>

    @if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.school-calendars.update', $schoolCalendar) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="school_calendar_desc" class="block text-gray-700 text-sm font-bold mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="school_calendar_desc" id="school_calendar_desc" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('school_calendar_desc') border-red-500 @enderror"
                        value="{{ old('school_calendar_desc', $schoolCalendar->school_calendar_desc) }}" required>
                    @error('school_calendar_desc')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">Example: Academic Year 2024-2025</p>
                </div>

                <div class="mb-4">
                    <label for="school_calendar_short_desc" class="block text-gray-700 text-sm font-bold mb-2">
                        Short Description <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="school_calendar_short_desc" id="school_calendar_short_desc" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('school_calendar_short_desc') border-red-500 @enderror"
                        value="{{ old('school_calendar_short_desc', $schoolCalendar->school_calendar_short_desc) }}" required>
                    @error('school_calendar_short_desc')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">Example: AY 2024-2025</p>
                </div>

                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_selected" id="is_selected" class="mr-2" value="1" 
                            {{ old('is_selected', $schoolCalendar->is_selected) ? 'checked' : '' }}
                            {{ $schoolCalendar->is_selected ? 'disabled' : '' }}>
                        <label for="is_selected" class="text-gray-700 text-sm font-bold">
                            Set as current academic year
                        </label>
                    </div>
                    @if ($schoolCalendar->is_selected)
                    <p class="text-green-600 text-xs mt-1">This is already the current academic year.</p>
                    @else
                    <p class="text-gray-600 text-xs mt-1">If checked, this will become the active academic year and all other academic years will be deactivated.</p>
                    @endif
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update School Calendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
