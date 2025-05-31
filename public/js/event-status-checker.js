/**
 * Event Status Checker
 * 
 * This script automatically checks for events with 'upcoming' status that have passed
 * their end date and updates them to 'pending' status, notifying admins to update them.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Run the check when the page loads
    checkPastEvents();
    
    // Set up a timer to check periodically (every 5 minutes)
    setInterval(checkPastEvents, 5 * 60 * 1000);
});

/**
 * Check for events that have passed but still have 'upcoming' status
 */
function checkPastEvents() {
    // Get all event elements on the page
    const eventElements = document.querySelectorAll('[data-event-id]');
    
    if (eventElements.length === 0) {
        return; // No events on the page
    }
    
    // Current date and time
    const now = new Date();
    
    // Track events that need updating
    const eventsToUpdate = [];
    
    // Check each event
    eventElements.forEach(element => {
        const eventId = element.dataset.eventId;
        const endDateStr = element.dataset.eventEndDate;
        const status = element.dataset.eventStatus;
        
        // Skip if not upcoming or missing data
        if (status !== 'upcoming' || !endDateStr) {
            return;
        }
        
        // Parse the end date
        const endDate = new Date(endDateStr);
        
        // Check if the event has passed
        if (endDate < now) {
            eventsToUpdate.push({
                id: eventId,
                title: element.dataset.eventTitle || 'Unknown Event',
                endDate: endDate
            });
            
            // Update the UI immediately
            updateEventStatusUI(element);
        }
    });
    
    // If we found events to update, send them to the server
    if (eventsToUpdate.length > 0) {
        updateEventStatus(eventsToUpdate);
    }
}

/**
 * Update the event status in the UI
 */
function updateEventStatusUI(element) {
    // Find status badge elements
    const statusBadges = element.querySelectorAll('.event-status, .status-badge');
    
    statusBadges.forEach(badge => {
        // Remove existing status classes
        badge.classList.remove('status-upcoming', 'status-completed', 'status-cancelled');
        
        // Add pending status class and update text
        badge.classList.add('status-pending');
        badge.textContent = 'Pending';
        
        // Update the element's data attribute
        element.dataset.eventStatus = 'pending';
        
        // Apply pending style
        badge.style.backgroundColor = '#fef3c7';
        badge.style.color = '#92400e';
        badge.style.border = '1px solid #fcd34d';
    });
}

/**
 * Send the update request to the server
 */
function updateEventStatus(events) {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Prepare the request
    fetch('/api/events/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ events: events })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Events updated successfully:', data.updated);
            
            // Show notification to admins if user is admin
            if (data.isAdmin && data.updated.length > 0) {
                showAdminNotification(data.updated);
            }
        } else {
            console.error('Failed to update events:', data.message);
        }
    })
    .catch(error => {
        console.error('Error updating event status:', error);
    });
}

/**
 * Show a notification to admin users
 */
function showAdminNotification(events) {
    if (events.length === 0) return;
    
    // Create notification container if it doesn't exist
    let container = document.querySelector('.admin-notification-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'admin-notification-container fixed top-20 right-4 z-50 max-w-md';
        document.body.appendChild(container);
    }
    
    // Create the notification
    const notification = document.createElement('div');
    notification.className = 'bg-amber-50 border-l-4 border-amber-500 p-4 mb-4 shadow-md rounded-r';
    
    // Single or multiple events
    if (events.length === 1) {
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0 pt-0.5">
                    <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800">Event Status Update Required</h3>
                    <div class="mt-1 text-sm text-amber-700">
                        <p>The event "${events[0].title}" has ended but is still marked as "Upcoming". It has been temporarily marked as "Pending". Please update its status to "Completed" or "Cancelled".</p>
                    </div>
                    <div class="mt-2">
                        <a href="/events/${events[0].id}" class="text-sm font-medium text-amber-800 hover:text-amber-600">
                            View Event →
                        </a>
                    </div>
                </div>
                <button type="button" class="ml-auto flex-shrink-0 text-amber-500 hover:text-amber-700" onclick="this.parentElement.parentElement.remove()">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        `;
    } else {
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0 pt-0.5">
                    <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800">Event Status Updates Required</h3>
                    <div class="mt-1 text-sm text-amber-700">
                        <p>${events.length} events have ended but are still marked as "Upcoming". They have been temporarily marked as "Pending". Please update their status.</p>
                    </div>
                    <div class="mt-2">
                        <a href="/admin/events/custom-calendar" class="text-sm font-medium text-amber-800 hover:text-amber-600">
                            View Events →
                        </a>
                    </div>
                </div>
                <button type="button" class="ml-auto flex-shrink-0 text-amber-500 hover:text-amber-700" onclick="this.parentElement.parentElement.remove()">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        `;
    }
    
    // Add to container
    container.appendChild(notification);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        notification.remove();
    }, 10000);
}
