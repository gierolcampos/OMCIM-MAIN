@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">Notifications</h1>
            
            @if($notifications->count() > 0)
            <button id="markAllAsRead" class="text-sm text-[#c21313] hover:text-red-700 transition duration-200">
                Mark all as read
            </button>
            @endif
        </div>

        @if($notifications->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="divide-y divide-gray-100">
                    @foreach($notifications as $notification)
                        <div class="notification-item p-4 {{ $notification->is_read ? 'bg-white' : 'bg-red-50' }}" data-id="{{ $notification->id }}">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 pt-1">
                                    @if($notification->type == 'event')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @elseif($notification->type == 'announcement')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                        </svg>
                                    @elseif($notification->type == 'evaluation')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-base font-medium {{ $notification->is_read ? 'text-gray-800' : 'text-gray-900' }}">
                                            {{ $notification->title }}
                                        </h3>
                                        <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-1 text-sm {{ $notification->is_read ? 'text-gray-600' : 'text-gray-800' }}">
                                        {{ $notification->message }}
                                    </p>
                                    <div class="mt-2 flex items-center">
                                        <a href="{{ $notification->getReferenceUrl() }}" class="text-sm text-blue-600 hover:text-blue-800 transition duration-200">
                                            View Details
                                        </a>
                                        @if(!$notification->is_read)
                                            <button class="mark-as-read ml-4 text-sm text-gray-500 hover:text-gray-700 transition duration-200">
                                                Mark as read
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="text-lg font-medium text-gray-800 mb-2">No notifications</h3>
                <p class="text-gray-500">You don't have any notifications at the moment.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mark individual notification as read
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.addEventListener('click', function() {
                const notificationItem = this.closest('.notification-item');
                const notificationId = notificationItem.dataset.id;
                
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
                        notificationItem.classList.remove('bg-red-50');
                        notificationItem.classList.add('bg-white');
                        this.remove();
                        
                        // Update the notification count in the header
                        updateNotificationCount();
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
        
        // Mark all notifications as read
        const markAllBtn = document.getElementById('markAllAsRead');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function() {
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
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.classList.remove('bg-red-50');
                            item.classList.add('bg-white');
                        });
                        
                        document.querySelectorAll('.mark-as-read').forEach(btn => {
                            btn.remove();
                        });
                        
                        // Update the notification count in the header
                        updateNotificationCount(0);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }
        
        function updateNotificationCount(count = null) {
            const notificationBadge = document.querySelector('.notification-badge');
            if (notificationBadge) {
                if (count !== null) {
                    if (count === 0) {
                        notificationBadge.classList.add('hidden');
                    } else {
                        notificationBadge.textContent = count;
                        notificationBadge.classList.remove('hidden');
                    }
                } else {
                    fetch('/notifications/latest')
                        .then(response => response.json())
                        .then(data => {
                            if (data.unreadCount === 0) {
                                notificationBadge.classList.add('hidden');
                            } else {
                                notificationBadge.textContent = data.unreadCount;
                                notificationBadge.classList.remove('hidden');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            }
        }
    });
</script>
@endpush
@endsection
