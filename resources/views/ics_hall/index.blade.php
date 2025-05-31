@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Helper function to get the image source based on the path
 * Handles both regular and base64 encoded images
 */
function getImageSrc($path, $useAsset = false) {
    // Check if it's a base64 file
    if (strpos($path, 'base64/') === 0 && file_exists(public_path($path))) {
        return file_get_contents(public_path($path));
    } else {
        return $useAsset ? asset($path) : Storage::url($path);
    }
}
@endphp

@section('content')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-fadeInUp {
        opacity: 0;
    }

    .animate-slideInRight {
        opacity: 0;
    }

    .animate-fadeInRight {
        opacity: 0;
    }

    .animate-fadeInLeft {
        opacity: 0;
    }

    .animate-fadeInUp.visible {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .animate-slideInRight.visible {
        animation: slideInRight 0.6s ease-out forwards;
    }

    .animate-fadeInRight.visible {
        animation: fadeInRight 0.8s ease-out forwards;
    }

    .animate-fadeInLeft.visible {
        animation: fadeInLeft 0.8s ease-out forwards;
    }

    .custom-btn {
        @apply bg-[#c21313] text-white px-6 py-2 text-sm rounded-lg transition-all duration-300 hover:bg-[#a11010] hover:shadow-md;
    }

    .outline-btn {
        @apply border border-[#c21313] text-[#c21313] px-6 py-2 text-sm rounded-lg transition-all duration-300;
        background-color: transparent;
    }

    .outline-btn::before {
        content: '';
        @apply absolute inset-0 bg-[#c21313] transition-all duration-500 transform scale-x-0 origin-left;
        z-index: -1;
    }

    .outline-btn::after {
        content: '';
        @apply absolute inset-0 bg-[#a11010] transition-all duration-500 transform scale-x-0 origin-right;
        z-index: -1;
    }

    .outline-btn:hover {
        @apply bg-[#c21313] text-white shadow-md;
    }

    .outline-btn:hover::before {
        @apply scale-x-100;
    }

    .outline-btn:hover::after {
        @apply scale-x-100;
    }

    .outline-btn span {
        @apply relative z-10 transition-transform duration-500;
    }

    .outline-btn:hover span {
        @apply transform translate-x-1;
    }

    .outline-btn.bg {
        @apply bg-[#c21313] text-white;
    }

    .outline-btn.bg:hover {
        @apply bg-[#a11010];
    }

    .custom-text {
        @apply text-[#c21313] transition-colors duration-300 hover:text-[#a11010];
    }

    .custom-link {
        @apply transition-all duration-300 hover:text-[#c21313];
    }

    .event-date {
        @apply text-right pr-5 border-r-2 border-[#c21313] w-24;
    }

    .event-month {
        @apply text-[#c21313] text-xl font-semibold leading-none;
    }

    .event-day {
        @apply text-3xl font-bold leading-none;
    }

    .event-title {
        @apply text-[#c21313] font-semibold mb-2 text-lg;
    }

    .event-description {
        @apply text-gray-600 text-sm;
    }

    .event-thumbnail {
        @apply w-32 h-24 object-cover mr-4 rounded-lg;
    }

    .news-card {
        @apply bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:bg-gray-50;
    }

    .news-image {
        @apply w-full h-64 object-cover transition-transform duration-500 hover:scale-105;
    }

    .event-card {
        @apply bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-all duration-300 hover:bg-gray-50;
    }
</style>

<!-- Hero Section -->
<div class="relative h-[80vh] bg-cover bg-center" style="background-image: url('{{ asset('img/homebg.jpg') }}');">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="relative z-10 flex items-center justify-center h-full text-center px-4">
        <div class="text-white max-w-4xl">
            <h1 class="font-marker text-5xl md:text-7xl animate-fadeInUp" style="animation-delay: 0.2s;">Welcome to ICS Hall!</h1>
            <p class="mt-6 text-xl md:text-2xl animate-fadeInUp" style="animation-delay: 0.4s;">A place where you code your path.</p>
            <div class="mt-8 animate-fadeInUp flex justify-center" style="animation-delay: 0.6s;">
                <a href="#news" class="border border-[#c21313] hover:bg-[#c21313] hover:text-white px-6 py-2 text-sm rounded-lg transition duration-300">
                    <span>Explore More</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Who We Are Section -->
<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row gap-12 items-center animate-fadeInRight">

            <div class="md:w-1/2 flex items-center justify-center order-1 md:order-2 mb-8 md:mb-0">
                <img src="{{ asset('img/officersq.jpg') }}" alt="ICS Picture" class="w-full md:w-4/5 h-auto shadow-md">
            </div>

            <div class="md:w-1/2 order-2 md:order-1">
                <h2 class="text-2xl font-bold mb-6 text-[#c21313] border-b-2 border-[#c21313] pb-2 inline-block">Who We Are</h2>
                <p class="mb-4 text-base text-gray-800 leading-relaxed">
                    The Integrated Computer Society (ICS) is a family of passionate, driven, and innovative students who share a love for technology, coding, and problem-solving. We believe that learning goes beyond the classroom—and that growth happens when we collaborate, explore, and create together.
                </p>
                <p class="text-base text-gray-800 leading-relaxed">
                    ICS is a space where every member, whether a beginner or an experienced programmer, is encouraged to take bold steps, challenge themselves, and discover their potential. Our organization may not justify its continually evolving identity in the larger social network, but we have earned a uniqueness of sort.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- What We Do Section -->
<div class="bg-white py-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row gap-12 items-center animate-fadeInLeft">
            <div class="md:w-1/2 order-1">
                <h2 class="text-2xl font-bold mb-6 text-[#c21313] border-b-2 border-[#c21313] pb-2 inline-block">What We Do</h2>
                <p class="mb-4 text-base text-gray-800 leading-relaxed">
                    From coding workshops and seminars to tech competitions and collaborative projects, ICS provides opportunities for every member to explore, create, and excel. Whether you're taking your first steps in coding or building your next big project, ICS is here to support your journey.
                </p>
                <p class="text-base text-gray-800 leading-relaxed">
                    Our activities are designed to complement academic learning with practical experience, industry exposure, and community engagement. We believe in creating a supportive environment where members can develop both technical skills and professional networks.
                </p>
            </div>

            <div class="md:w-1/2 flex items-center justify-center order-2 mb-8 md:mb-0">
                <img src="{{ asset('img/prog.jpg') }}" alt="ICS Activities" class="w-full md:w-4/5 h-auto shadow-md">
            </div>
        </div>
    </div>
</div>


<!-- News Section -->
<div id="news" class="container mx-auto px-4 py-16">
    <h2 class="text-4xl font-bold mb-12 custom-text text-[#c21313] text-center animate-fadeInUp">Latest News & Announcements</h2>

    <div x-data="{ activeAnnouncement: null }" x-cloak>
        <!-- Announcement Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
            @forelse($announcements as $index => $announcement)
            <div class="news-card animate-fadeInUp hover:shadow-xl transition-all duration-300 cursor-pointer"
                 style="animation-delay: {{ 0.2 + $index * 0.2 }}s"
                 @click="activeAnnouncement = {{ $announcement->id }}">
                <div class="block">
                    @if($announcement->media_path)
                        <img src="{{ getImageSrc($announcement->media_path) }}" alt="{{ $announcement->title }}" class="news-image">
                    @elseif($announcement->image_path)
                        <img src="{{ getImageSrc($announcement->image_path) }}" alt="{{ $announcement->title }}" class="news-image">
                    @else
                        <img src="{{ asset('img/news' . ($index + 1) . '.jpg') }}" alt="{{ $announcement->title }}" class="news-image">
                    @endif
                    <div class="p-6 flex flex-col h-full">
                        <div class="text-sm text-gray-500 mb-2">{{ $announcement->created_at->format('F j, Y') }}</div>
                        <h3 class="text-xl font-semibold mb-3 custom-link" style="{{ $announcement->text_color ? "color: {$announcement->text_color}" : '' }}">{{ $announcement->title }}</h3>
                        <p class="text-gray-600 mb-4 flex-grow">{{ Str::limit(strip_tags($announcement->content), 100) }}</p>
                        <div class="w-full flex items-center justify-start">
                            <span class="text-[#c21313] font-medium hover:underline">Read More</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-8">
                <p class="text-gray-500">No announcements available at the moment.</p>
            </div>
            @endforelse
        </div>

        <!-- Announcement Modals -->
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
                                    <div class="flex items-center text-gray-500 text-sm">
                                        <span class="mr-4">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ $announcement->created_at->format('F j, Y') }}
                                        </span>
                                        @if($announcement->creator)
                                            <span>
                                                <i class="far fa-user mr-1"></i>
                                                {{ $announcement->creator->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="prose prose-lg max-w-none text-gray-700 mb-8 whitespace-pre-wrap" @if($announcement->text_color) style="color: {{ $announcement->text_color }};" @endif>
                                    {!! nl2br(e($announcement->content)) !!}
                                </div>

                                <div class="mt-8">
                                    <a href="{{ route('announcements.show', $announcement) }}" class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800">
                                        View full announcement
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Right Image Section -->
                            <div class="w-full md:w-1/2 bg-gray-50">
                                @if($announcement->media_path)
                                    <div class="h-full flex items-center justify-center p-4">
                                        <img src="{{ getImageSrc($announcement->media_path) }}" alt="{{ $announcement->title }}" class="max-w-full max-h-[500px] object-contain rounded-lg shadow-sm">
                                    </div>
                                @elseif($announcement->image_path)
                                    <div class="h-full flex items-center justify-center p-4">
                                        <img src="{{ getImageSrc($announcement->image_path) }}" alt="{{ $announcement->title }}" class="max-w-full max-h-[500px] object-contain rounded-lg shadow-sm">
                                    </div>
                                @else
                                    <div class="h-full flex items-center justify-center p-4">
                                        <img src="{{ asset('img/news' . (($index % 3) + 1) . '.jpg') }}" alt="{{ $announcement->title }}" class="max-w-full max-h-[500px] object-contain rounded-lg shadow-sm">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        @endforeach
    </div>

    <div class="text-center mt-12 animate-fadeInUp flex justify-center" style="animation-delay: 0.8s">
        <a href="{{ url('omcms/announcements') }}" class="bg-[#c21313] text-white px-6 py-2 text-sm rounded-lg transition duration-300 hover:bg-[#a11010] shadow-md">
            See More News & Announcements
        </a>
    </div>
</div>

<!-- Events Section -->
<div class="container mx-auto px-4 py-16">
    <h2 class="text-4xl font-bold mb-12 text-[#c21313] text-center animate-fadeInUp">Upcoming Events</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Featured Event -->
        <div class="lg:col-span-2 animate-fadeInUp">
            @if($featuredEvent)
            <a href="{{ route('events.show', $featuredEvent) }}" class="block">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="relative">
                        @if($featuredEvent->image_path)
                            <img src="{{ getImageSrc($featuredEvent->image_path, true) }}" alt="{{ $featuredEvent->title }}" class="w-full h-[450px] object-cover">
                        @else
                            <img src="{{ asset('img/oathtaking.jpg') }}" alt="{{ $featuredEvent->title }}" class="w-full h-[450px] object-cover">
                        @endif
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-8">
                            <div class="flex items-center mb-2">
                                <span class="bg-[#c21313] text-white text-xs font-bold px-3 py-1 rounded-full mr-2">UPCOMING</span>
                                <span class="text-white text-sm">{{ $featuredEvent->start_date_time->format('F j, Y') }}</span>
                            </div>
                            <h3 class="text-3xl font-bold mt-2 text-white">{{ $featuredEvent->title }}</h3>
                            <p class="mt-2 text-base text-gray-200">{{ Str::limit(strip_tags($featuredEvent->description), 120) }}</p>
                            <div class="mt-4 flex items-center text-white/80">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $featuredEvent->location }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @else
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="relative">
                    <img src="{{ asset('img/oathtaking.jpg') }}" alt="No Featured Event" class="w-full h-[450px] object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-8">
                        <div class="text-white text-lg font-semibold">No upcoming events</div>
                        <h3 class="text-3xl font-bold mt-2 text-white">Stay tuned for future events</h3>
                        <p class="mt-2 text-base text-gray-200">Check back later for upcoming ICS events and activities.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Completed Events - Latest to Oldest -->
        <div class="space-y-6 animate-slideInRight">
            <h3 class="text-lg font-semibold text-[#c21313] mb-3">Past Events</h3>
            @forelse($completedEvents as $event)
            <div class="flex mb-6 bg-white p-3 rounded-lg shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-24 h-24 flex-shrink-0 mr-4">
                    @if($event->image_path)
                        <img src="{{ getImageSrc($event->image_path, true) }}" alt="{{ $event->title }}" class="w-full h-full object-cover rounded">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center rounded">
                            <span class="text-gray-400">No Image</span>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#c21313] hover:underline">
                        <a href="{{ route('events.show', $event) }}">{{ $event->title }}</a>
                    </h3>
                    <div class="flex items-center text-gray-600 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        @if($event->start_date_time->format('Y-m-d') == $event->end_date_time->format('Y-m-d'))
                            <span>{{ $event->start_date_time->format('F j') }} | {{ $event->start_date_time->format('g:i A') }} – {{ $event->end_date_time->format('g:i A') }}</span>
                        @else
                            <span>{{ $event->start_date_time->format('F j') }} – {{ $event->end_date_time->format('F j') }} | {{ $event->start_date_time->format('g:i A') }} – {{ $event->end_date_time->format('g:i A') }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit(strip_tags($event->description), 100) }}</p>
                </div>
            </div>
            @empty
                <div class="text-center py-8 bg-white rounded-lg shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500">No past events available at the moment.</p>
                </div>
            @endforelse

            <div class="mt-4 text-center">
                <a href="{{ route('events.custom-calendar') }}" class="text-[#c21313] hover:underline text-sm font-medium">
                    View all events →
                </a>
            </div>
        </div>
    </div>

    <div class="w-full flex items-center justify-center mt-12 animate-fadeInUp">
        <a href="{{ route('events.custom-calendar') }}" class="bg-[#c21313] text-white px-6 py-2 text-sm rounded-lg transition duration-300 hover:bg-[#a11010] shadow-md mx-auto">
            View Events Calendar
        </a>
    </div>
</div>

<script>
    // Intersection Observer for animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1
    });

    // Observe all animated elements
    document.querySelectorAll('.animate-fadeInUp, .animate-slideInRight, .animate-fadeInRight, .animate-fadeInLeft').forEach((el) => {
        observer.observe(el);
    });
</script>

@endsection