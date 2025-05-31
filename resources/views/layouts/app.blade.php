@php
use Illuminate\Support\Facades\Auth;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
        <link rel="stylesheet" href="{{ asset('css/modal-fix.css') }}">
        <link rel="stylesheet" href="{{ asset('css/fullscreen-modal.css') }}">
        <link rel="stylesheet" href="{{ asset('css/event-status.css') }}">
        <!-- <link rel="stylesheet" href="{{ asset('css/apple-notification.css') }}"> -->

        <style>
            /* Anti-flickering fix for modals */
            [x-cloak] { display: none !important; }

            /* Simple modal styles */
            .announcement-item {
                cursor: pointer;
            }

            /* Prevent text selection on double-click */
            .announcement-item {
                user-select: none;
            }

            /* Prevent modal flickering */
            .fixed.inset-0.z-50 {
                animation: none !important;
                transition: opacity 0.3s ease !important;
            }

            /* Prevent event bubbling issues */
            .announcement-item button,
            .announcement-item a {
                pointer-events: auto;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Custom Styles -->
        @yield('styles')
        @isset($styles)
            {{ $styles }}
        @endisset
    </head>
    <body class="font-sans antialiased {{ Auth::check() && Auth::user()->isAdmin() ? 'admin-user' : '' }}">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                <div>

                </div>
                @yield('content')
                @isset($slot)
                    {{ $slot }}
                @endisset
            </main>
        </div>
        @include('layouts.footer')

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Fullscreen Modal Script -->
        <script src="{{ asset('js/fullscreen-modal.js') }}"></script>

        <!-- Modal Fix Script - Disabled to fix flickering -->
        <!-- <script src="{{ asset('js/modal-fix.js') }}"></script> -->

        <!-- Modal Smooth Script - Disabled to fix flickering -->
        <!-- <script src="{{ asset('js/modal-smooth.js') }}"></script> -->

        <!-- Notification Click Fix Script -->
        <!-- <script src="{{ asset('js/notification-click-fix.js') }}"></script> -->

        <!-- Apple Notification Script -->
        <!-- <script src="{{ asset('js/apple-notification.js') }}"></script> -->

        <!-- Event Status Checker Script -->
        <script src="{{ asset('js/event-status-checker.js') }}"></script>

        <!-- Image Utilities Script -->
        <script src="{{ asset('js/image-utils.js') }}"></script>

        <!-- Notification Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Load notifications when dropdown is opened
                const notificationButton = document.querySelector('[x-data="{ open: false }"] button');
                const notificationList = document.getElementById('notification-list');
                const markAllReadBtn = document.getElementById('mark-all-read-dropdown');

                // Mobile notification elements
                const mobileNotificationButton = document.querySelector('[x-data="{ mobileNotificationOpen: false }"] button');
                const mobileNotificationList = document.getElementById('mobile-notification-list');
                const mobileMarkAllReadBtn = document.getElementById('mobile-mark-all-read');

                // Desktop notifications
                if (notificationButton && notificationList) {
                    notificationButton.addEventListener('click', function() {
                        loadNotifications('desktop');
                    });
                }

                // Mobile notifications
                if (mobileNotificationButton && mobileNotificationList) {
                    mobileNotificationButton.addEventListener('click', function() {
                        loadNotifications('mobile');
                    });
                }

                // Mark all as read - Desktop
                if (markAllReadBtn) {
                    markAllReadBtn.addEventListener('click', function() {
                        markAllNotificationsAsRead('desktop');
                    });
                }

                // Mark all as read - Mobile
                if (mobileMarkAllReadBtn) {
                    mobileMarkAllReadBtn.addEventListener('click', function() {
                        markAllNotificationsAsRead('mobile');
                    });
                }

                // Function to mark all notifications as read
                function markAllNotificationsAsRead(device) {
                    fetch('/notifications/mark-all-as-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update all notification badges
                            const badges = document.querySelectorAll('.notification-badge, .mobile-notification-badge');
                            badges.forEach(badge => {
                                badge.classList.add('hidden');
                            });

                            // Reload notifications for the appropriate device
                            loadNotifications(device);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }

                function loadNotifications(device = 'desktop') {
                    fetch('/notifications/latest')
                        .then(response => response.json())
                        .then(data => {
                            // Determine which notification list to update
                            const targetList = device === 'mobile' ? mobileNotificationList : notificationList;

                            // Update notification list
                            if (targetList) {
                                if (data.notifications.length === 0) {
                                    targetList.innerHTML = `
                                        <div class="px-4 py-8 text-center text-gray-500 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                            No notifications
                                        </div>
                                    `;
                                } else {
                                    let html = '';

                                    data.notifications.forEach(notification => {
                                        let iconClass = '';

                                        switch (notification.type) {
                                            case 'event':
                                                iconClass = 'fa-calendar-alt text-blue-500';
                                                break;
                                            case 'announcement':
                                                iconClass = 'fa-bullhorn text-yellow-500';
                                                break;
                                            case 'evaluation':
                                                iconClass = 'fa-clipboard-check text-green-500';
                                                break;
                                            default:
                                                iconClass = 'fa-bell text-gray-500';
                                        }

                                        let url = '';
                                        switch (notification.type) {
                                            case 'event':
                                                url = `/events/${notification.reference_id}`;
                                                break;
                                            case 'announcement':
                                                url = `/announcements/${notification.reference_id}`;
                                                break;
                                            case 'evaluation':
                                                url = `/events/${notification.reference_id}/evaluation`;
                                                break;
                                            default:
                                                url = '#';
                                        }

                                        const date = new Date(notification.created_at);
                                        const timeAgo = getTimeAgo(date);

                                        html += `
                                            <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 ${notification.is_read ? '' : 'bg-red-50'}">
                                                <div class="flex">
                                                    <div class="flex-shrink-0 pt-1">
                                                        <i class="fas ${iconClass}"></i>
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <div class="flex justify-between items-baseline">
                                                            <p class="text-sm font-medium ${notification.is_read ? 'text-gray-700' : 'text-gray-900'}">${notification.title}</p>
                                                            <span class="text-xs text-gray-500">${timeAgo}</span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">${notification.message}</p>
                                                        <div class="mt-1 flex">
                                                            <a href="${url}" class="text-xs text-blue-600 hover:text-blue-800">View</a>
                                                            ${!notification.is_read ? `
                                                                <button class="ml-3 text-xs text-gray-500 hover:text-gray-700"
                                                                        onclick="markAsRead(event, ${notification.id}, '${device}')">
                                                                    Mark as read
                                                                </button>
                                                            ` : ''}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    });

                                    targetList.innerHTML = html;
                                }
                            }

                            // Update notification badges
                            updateNotificationBadges(data.unreadCount);
                        })
                        .catch(error => {
                            console.error('Error loading notifications:', error);
                            if (targetList) {
                                targetList.innerHTML = `
                                    <div class="px-4 py-8 text-center text-gray-500 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-2 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Error loading notifications
                                    </div>
                                `;
                            }
                        });
                }

                // Helper function to update all notification badges
                function updateNotificationBadges(count) {
                    const badges = document.querySelectorAll('.notification-badge, .mobile-notification-badge');
                    badges.forEach(badge => {
                        if (count === 0) {
                            badge.classList.add('hidden');
                        } else {
                            badge.textContent = count > 99 ? '99+' : count;
                            badge.classList.remove('hidden');
                        }
                    });
                }

                // Helper function to format time ago
                function getTimeAgo(date) {
                    const seconds = Math.floor((new Date() - date) / 1000);

                    let interval = Math.floor(seconds / 31536000);
                    if (interval >= 1) {
                        return interval + " year" + (interval === 1 ? "" : "s") + " ago";
                    }

                    interval = Math.floor(seconds / 2592000);
                    if (interval >= 1) {
                        return interval + " month" + (interval === 1 ? "" : "s") + " ago";
                    }

                    interval = Math.floor(seconds / 86400);
                    if (interval >= 1) {
                        return interval + " day" + (interval === 1 ? "" : "s") + " ago";
                    }

                    interval = Math.floor(seconds / 3600);
                    if (interval >= 1) {
                        return interval + " hour" + (interval === 1 ? "" : "s") + " ago";
                    }

                    interval = Math.floor(seconds / 60);
                    if (interval >= 1) {
                        return interval + " minute" + (interval === 1 ? "" : "s") + " ago";
                    }

                    return "just now";
                }
            });

            // Function to mark a notification as read
            function markAsRead(event, notificationId, device = 'desktop') {
                event.preventDefault();
                event.stopPropagation();

                fetch(`/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the UI
                        const notificationItem = event.target.closest('div.px-4');
                        if (notificationItem) {
                            notificationItem.classList.remove('bg-red-50');
                            event.target.remove();
                        }

                        // Fetch the latest unread count
                        fetch('/notifications/latest')
                            .then(response => response.json())
                            .then(data => {
                                // Update all notification badges
                                updateNotificationBadges(data.unreadCount);
                            })
                            .catch(error => console.error('Error updating badges:', error));
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        </script>

        <!-- Custom Scripts -->
        @yield('scripts')
        @isset($scripts)
            {{ $scripts }}
        @endisset
    </body>
</html>