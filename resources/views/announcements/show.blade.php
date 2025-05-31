@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="mb-6">
            @if(Auth::check() && Auth::user()->canManageAnnouncements())
            <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-red-600 transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Announcements
            </a>
            @else
            <a href="{{ route('omcms.announcements') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-red-600 transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Announcements
            </a>
            @endif
        </div>

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

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden {{ $announcement->priority == 'high' ? 'border-l-4 border-l-red-500' : '' }}">
            <div class="p-4 sm:p-6 md:p-8">
                <!-- Announcement Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 md:mb-8">
                    <div>
                        <div class="flex flex-wrap items-start gap-2 mb-3">
                            <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold text-gray-900 break-words">{{ $announcement->title }}</h1>
                            @if($announcement->is_boosted)
                                <span class="text-xs font-bold uppercase text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full mt-1.5 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    Pinned
                                </span>
                            @endif
                            @if($announcement->priority == 'high')
                                <span class="text-xs font-bold uppercase text-red-600 bg-red-50 px-2 py-0.5 rounded-full mt-1.5">Important</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-y-2 gap-x-4 text-sm text-gray-500">
                            <div class="flex items-center mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $announcement->created_at->format('F j, Y') }}
                            </div>

                            <div class="flex items-center mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $announcement->creator->name ?? 'Unknown' }}
                            </div>
                        </div>
                    </div>

                    @if(Auth::check() && Auth::user()->canManageAnnouncements())
                        <div class="flex space-x-2 mt-2 sm:mt-0 shrink-0">
                            <a href="{{ route('announcements.edit', $announcement) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>

                            <form action="{{ route('announcements.togglePin', $announcement) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white {{ $announcement->is_boosted ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-600 hover:bg-gray-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $announcement->is_boosted ? 'M19 14l-7 7m0 0l-7-7m7 7V3' : 'M5 10l7-7m0 0l7 7m-7-7v18' }}" />
                                    </svg>
                                    {{ $announcement->is_boosted ? 'Unpin' : 'Pin' }}
                                </button>
                            </form>

                            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Announcement Media -->
                @if($announcement->media_path)
                    <div class="mb-8">
                        @php
                            $extension = pathinfo($announcement->media_path, PATHINFO_EXTENSION);
                            $isVideo = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'wmv']);
                        @endphp

                        @if($isVideo)
                            <div class="rounded-lg overflow-hidden shadow-md max-w-full mx-auto bg-gray-900">
                                <video controls class="w-full max-h-[80vh] mx-auto">
                                    <source src="{{ Storage::url($announcement->media_path) }}" type="video/{{ $extension }}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @else
                            <div class="flex justify-center bg-gray-50 rounded-lg p-2">
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
                                <img src="{{ $src }}" alt="{{ $announcement->title }}" class="rounded-lg max-w-full max-h-[80vh] object-contain shadow-md">
                            </div>
                        @endif
                    </div>
                @elseif($announcement->image_path)
                    <div class="mb-8">
                        <div class="flex justify-center bg-gray-50 rounded-lg p-2">
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
                            <img src="{{ $src }}" alt="{{ $announcement->title }}" class="rounded-lg max-w-full max-h-[80vh] object-contain shadow-md">
                        </div>
                    </div>
                @endif

                <!-- Announcement Content -->
                <div class="prose prose-lg max-w-none text-gray-700 mb-8 whitespace-pre-wrap content-container" @if($announcement->text_color) style="color: {{ $announcement->text_color }};" @endif>
                    {!! nl2br(e($announcement->content)) !!}
                </div>

                <!-- Additional Information -->
                @if($announcement->publish_date || $announcement->expiry_date)
                    <div class="mt-8 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Additional Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600">
                            @if($announcement->publish_date)
                                <div>
                                    <span class="font-medium">Published:</span> {{ $announcement->publish_date->format('F j, Y, g:i a') }}
                                </div>
                            @endif

                            @if($announcement->expiry_date)
                                <div>
                                    <span class="font-medium">Expires:</span> {{ $announcement->expiry_date->format('F j, Y, g:i a') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row justify-between gap-4">
            @if(Auth::check() && Auth::user()->canManageAnnouncements())
            <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Announcements
            </a>

            @else
            <a href="{{ route('omcms.announcements') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Announcements
            </a>
            @endif

            @if(Auth::check() && Auth::user()->canManageAnnouncements())
                <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Announcement
                </a>
            @endif
        </div>
    </div>
</div>

<style>
    .prose {
        line-height: 1.6;
        word-break: break-word;
    }
    .prose p:not(:last-child) {
        margin-bottom: 1.5rem;
    }

    @media (max-width: 640px) {
        .content-container {
            font-size: 1rem;
        }
    }

    @media (min-width: 1280px) {
        .content-container {
            font-size: 1.125rem;
        }
    }
</style>
@endsection