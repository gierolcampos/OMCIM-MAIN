@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">School Calendars</h1>
        <a href="{{ route('admin.school-calendars.create') }}" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded">
            Add New School Calendar
        </a>
    </div>

    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Current Academic Year</h2>
            @if ($currentCalendar)
            <div class="bg-gray-100 p-4 rounded-lg mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium">{{ $currentCalendar->school_calendar_desc }}</h3>
                        <p class="text-gray-600">{{ $currentCalendar->school_calendar_short_desc }}</p>
                    </div>
                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm">Active</span>
                </div>
            </div>
            @else
            <div class="bg-yellow-100 p-4 rounded-lg mb-6">
                <p class="text-yellow-700">No school calendar is currently selected. Please select one below.</p>
            </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Short Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created At
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($schoolCalendars as $calendar)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $calendar->school_calendar_desc }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $calendar->school_calendar_short_desc }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($calendar->is_selected)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $calendar->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @if (!$calendar->is_selected)
                                <form action="{{ route('admin.school-calendars.set-current', $calendar) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                        Set as Current
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('admin.school-calendars.edit', $calendar) }}" class="text-blue-600 hover:text-blue-900">
                                    Edit
                                </a>
                                @if (!$calendar->is_selected)
                                <form action="{{ route('admin.school-calendars.destroy', $calendar) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this school calendar?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No school calendars found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
