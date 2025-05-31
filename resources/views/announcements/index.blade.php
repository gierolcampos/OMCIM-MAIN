@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="min-h-screen bg-white py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">
                    @if(isset($isAdminView) && $isAdminView)
                        Manage Announcements
                    @else
                        Announcements
                    @endif
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    @if(isset($isAdminView) && $isAdminView)
                        Create, edit, and manage all announcements
                    @else
                        Stay updated with the latest organization news
                    @endif
                </p>
                @if(isset($currentCalendar))
                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $currentCalendar->school_calendar_desc }}
                    </span>
                </div>
                @endif
            </div>
            @if(Auth::check() && Auth::user()->canManageAnnouncements())
                <a href="{{ route('admin.announcements.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200 hover:bg-red-700 flex items-center shadow-sm">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Announcement
                </a>
            @endif
        </div>

        @if(isset($isAdminView) && $isAdminView)
            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                <form action="{{ route('admin.announcements.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                               placeholder="Search by title or content">
                    </div>

                    <div class="w-full sm:w-auto">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                                class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            <option value="">All Statuses</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-auto">
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" id="priority"
                                class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            <option value="">All Priorities</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>

                    @if(isset($schoolCalendars) && count($schoolCalendars) > 0)
                    <div class="w-full sm:w-auto">
                        <label for="school_calendar_id" class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                        <select name="school_calendar_id" id="school_calendar_id"
                                class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            <option value="">All Academic Years</option>
                            @foreach($schoolCalendars as $calendar)
                                <option value="{{ $calendar->id }}" {{ request('school_calendar_id') == $calendar->id ? 'selected' : '' }}>
                                    {{ $calendar->school_calendar_short_desc }}
                                    @if($calendar->is_selected) (Current) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="w-full sm:w-auto flex items-end">
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200 hover:bg-red-700">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div x-data="{ activeAnnouncement: null }" x-cloak>
            <!-- Announcement Grid -->
            @if(isset($announcements) && count($announcements) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($announcements as $announcement)
                        <div class="announcement-card bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md cursor-pointer"
                             x-data="{ clickable: true }"
                             @click="if(clickable) activeAnnouncement = {{ $announcement->id }}"
                             @mousedown="clickable = true">
                            <div class="p-5 h-full flex flex-col">
                                <div class="flex-grow">
                                    <h4 class="text-lg font-semibold text-gray-900 break-words mb-2">
                                        {{ $announcement->title }}
                                    </h4>

                                    <div class="flex flex-wrap items-center text-sm text-gray-500 mb-3">
                                        <div class="flex items-center mr-3 mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $announcement->time_ago }}
                                        </div>

                                        <div class="flex items-center mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ $announcement->creator->name ?? 'Admin User' }}
                                        </div>
                                    </div>

                                    @if($announcement->media_path)
                                        <div class="mb-4 overflow-hidden rounded-lg shadow-sm">
                                            @php
                                                $extension = pathinfo($announcement->media_path, PATHINFO_EXTENSION);
                                                $isVideo = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'wmv']);
                                            @endphp

                                            @if($isVideo)
                                                <div class="relative aspect-video bg-gray-100 flex items-center justify-center">
                                                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="aspect-[4/3] bg-gray-100">
                                                    @php
                                                        $mediaPath = $announcement->media_path;
                                                        // Check if it's a base64 file
                                                        if (strpos($mediaPath, 'base64/') === 0 && file_exists(public_path($mediaPath))) {
                                                            $base64Content = file_get_contents(public_path($mediaPath));
                                                            $src = $base64Content;
                                                        } else {
                                                            $src = Storage::url($mediaPath);
                                                        }
                                                    @endphp
                                                    <img src="{{ $src }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($announcement->image_path)
                                        <div class="mb-4 overflow-hidden rounded-lg shadow-sm">
                                            <div class="aspect-[4/3] bg-gray-100">
                                                @php
                                                    $imagePath = $announcement->image_path;
                                                    // Check if it's a base64 file
                                                    if (strpos($imagePath, 'base64/') === 0 && file_exists(public_path($imagePath))) {
                                                        $base64Content = file_get_contents(public_path($imagePath));
                                                        $src = $base64Content;
                                                    } else {
                                                        $src = Storage::url($imagePath);
                                                    }
                                                @endphp
                                                <img src="{{ $src }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover">
                                            </div>
                                        </div>
                                    @endif

                                    <div class="text-gray-700 mb-4 line-clamp-3">
                                        {{ Str::limit($announcement->content, 200) }}
                                    </div>
                                </div>

                                <div class="mt-auto pt-4 border-t border-gray-100">
                                    <div class="flex justify-between items-center">
                                        @if(isset($isAdminView) && $isAdminView)
                                            <a href="{{ route('admin.announcements.show', $announcement) }}"
                                               class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center"
                                               onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('admin.announcements.show', $announcement) }}';">
                                                Click to view full announcement
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        @else
                                            <a href="{{ route('announcements.show', $announcement) }}"
                                               class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center"
                                               onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('announcements.show', $announcement) }}';">
                                                Click to view full announcement
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        @endif
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                    </div>

                                    @if(isset($isAdminView) && $isAdminView)
                                        <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between">
                                            <div class="flex space-x-1">
                                                <a href="{{ route('admin.announcements.edit', $announcement) }}"
                                                   class="btn-edit inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150"
                                                   onclick="event.preventDefault(); event.stopPropagation(); clickable = false; window.location.href='{{ route('admin.announcements.edit', $announcement) }}';">
                                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </a>

                                                <form action="{{ route('admin.announcements.togglePin', $announcement) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="button"
                                                            class="btn-pin inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded {{ $announcement->is_boosted ? 'text-white bg-amber-600 hover:bg-amber-700' : 'text-white bg-gray-600 hover:bg-gray-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition duration-150"
                                                            onclick="event.preventDefault(); event.stopPropagation(); clickable = false; this.closest('form').submit();">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                                        </svg>
                                                        {{ $announcement->is_boosted ? 'Unpin' : 'Pin' }}
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn-delete inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150"
                                                            onclick="event.preventDefault(); event.stopPropagation(); clickable = false; if(confirm('Are you sure you want to delete this announcement?')) this.closest('form').submit();">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>


                                        </div>

                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $announcement->status == 'published' ? 'bg-green-100 text-green-800' : ($announcement->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($announcement->status) }}
                                            </span>

                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $announcement->priority == 'high' ? 'bg-red-100 text-red-800' : ($announcement->priority == 'medium' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($announcement->priority) }} Priority
                                            </span>

                                            @if($announcement->is_boosted)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    Pinned
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $announcements->links() }}
                </div>

                <!-- Announcement Modal -->
                @foreach($announcements as $announcement)
                    <template x-if="activeAnnouncement === {{ $announcement->id }}">
                        <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                             @click.self="activeAnnouncement = null"
                             @keydown.escape.window="activeAnnouncement = null"
                             data-modal-id="announcement-{{ $announcement->id }}">
                            <div class="modal-content w-full max-w-5xl mx-auto bg-white rounded-xl shadow-xl overflow-hidden transform transition-all"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95">

                                <div class="absolute top-3 right-3 z-10">
                                    <button @click="activeAnnouncement = null" class="text-gray-400 hover:text-gray-600 focus:outline-none bg-white rounded-full p-1 hover:bg-gray-100 transition-all shadow-sm" data-modal-close="true">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex flex-col md:flex-row">
                                    <!-- Left Content Section -->
                                    <div class="w-full md:w-1/2 p-8">
                                        <div class="mb-6">
                                            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                                {{ $announcement->title }}
                                            </h3>

                                            <div class="flex items-center text-sm text-gray-500 flex-wrap gap-4">
                                                <div class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $announcement->created_at->format('F j, Y') }}
                                                </div>

                                                <div class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ $announcement->creator->name ?? 'Unknown' }}
                                                    <span class="ml-1 text-xs bg-red-50 text-red-600 px-2 py-0.5 rounded-full">Admin</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="prose max-w-none text-gray-700 whitespace-pre-wrap break-words"
                                             @if($announcement->text_color) style="color: {{ $announcement->text_color }};" @endif>
                                            {!! nl2br(e($announcement->content)) !!}
                                        </div>

                                        <div class="mt-8">
                                            @if(isset($isAdminView) && $isAdminView)
                                                <button type="button"
                                                       class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800"
                                                       @click.stop="window.location.href='{{ route('admin.announcements.show', $announcement) }}'">
                                                    View full announcement
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button"
                                                       class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800"
                                                       @click.stop="window.location.href='{{ route('announcements.show', $announcement) }}'">
                                                    View full announcement
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="mt-8">
                                            <button @click="activeAnnouncement = null" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-5 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                                                Close
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Right Image/Media Section -->
                                    <div class="w-full md:w-1/2 bg-gray-50 flex items-center justify-center p-4">
                                        @if($announcement->media_path)
                                            @php
                                                $extension = pathinfo($announcement->media_path, PATHINFO_EXTENSION);
                                                $isVideo = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'wmv']);
                                            @endphp

                                            @if($isVideo)
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <video controls class="max-w-full max-h-[70vh] rounded-lg shadow-md" @click.stop>
                                                        <source src="{{ Storage::url($announcement->media_path) }}" type="video/{{ $extension }}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div>
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    @php
                                                        $mediaPath = $announcement->media_path;
                                                        // Check if it's a base64 file
                                                        if (strpos($mediaPath, 'base64/') === 0 && file_exists(public_path($mediaPath))) {
                                                            $base64Content = file_get_contents(public_path($mediaPath));
                                                            $src = $base64Content;
                                                        } else {
                                                            $src = Storage::url($mediaPath);
                                                        }
                                                    @endphp
                                                    <img src="{{ $src }}" alt="{{ $announcement->title }}" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-md">
                                                </div>
                                            @endif
                                        @elseif($announcement->image_path)
                                            <div class="w-full h-full flex items-center justify-center">
                                                @php
                                                    $imagePath = $announcement->image_path;
                                                    // Check if it's a base64 file
                                                    if (strpos($imagePath, 'base64/') === 0 && file_exists(public_path($imagePath))) {
                                                        $base64Content = file_get_contents(public_path($imagePath));
                                                        $src = $base64Content;
                                                    } else {
                                                        $src = Storage::url($imagePath);
                                                    }
                                                @endphp
                                                <img src="{{ $src }}" alt="{{ $announcement->title }}" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-md">
                                            </div>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <img src="{{ asset('img/ics-logo.png') }}" alt="ICS Logo" class="max-w-full max-h-[50vh] object-contain opacity-75">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                @endforeach
            @else
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <p class="text-gray-500 mt-4">No announcements found.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .announcement-card {
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .announcement-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .prose {
        line-height: 1.6;
    }

    .prose p:not(:last-child) {
        margin-bottom: 1rem;
    }

    .break-words {
        word-break: break-word;
    }

    /* Modal Styles */
    .modal-backdrop {
        backdrop-filter: blur(5px);
    }

    .modal-content {
        max-height: 90vh;
    }

    .announcement-modal {
        border-radius: 0.75rem;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    .announcement-modal .text-justify {
        text-align: justify;
        line-height: 1.6;
        font-size: 1rem;
    }

    .prose {
        font-size: 1rem;
        line-height: 1.7;
    }

    /* Button hover effects */
    .btn-edit:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-pin:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-delete:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modal-content {
            max-height: 85vh;
            overflow-y: auto;
        }

        .prose {
            font-size: 0.95rem;
            line-height: 1.6;
        }
    }
</style>
@endsection