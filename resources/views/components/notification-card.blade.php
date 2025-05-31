@props([
    'id' => null,
    'title' => 'Notification Title',
    'message' => null,
    'important' => false,
    'pinned' => false,
    'timeAgo' => null,
    'url' => null,
    'type' => 'default'
])

<div {{ $attributes->merge([
    'class' => 'notification-card',
    'data-id' => $id,
    'data-url' => $url,
    'data-type' => $type
]) }}>
    <div class="notification-content">
        <div class="notification-header">
            <h3 class="notification-title">{{ $title }}</h3>
            
            <div class="notification-badges">
                @if($pinned)
                <span class="notification-badge pinned">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                    Pinned
                </span>
                @endif
                
                @if($important)
                <span class="notification-badge important">Important</span>
                @endif
                
                @if($timeAgo)
                <span class="notification-badge time">{{ $timeAgo }}</span>
                @endif
            </div>
        </div>
        
        @if($message)
        <div class="notification-message">
            {{ $message }}
        </div>
        @endif
        
        {{ $slot }}
    </div>
</div>
