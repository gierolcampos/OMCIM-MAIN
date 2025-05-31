/**
 * Apple-style Notification System
 * Creates notifications that slide in from the top like Apple's notification system
 */
document.addEventListener('DOMContentLoaded', function() {
    // Create the notification container if it doesn't exist
    if (!document.querySelector('.apple-notification-container')) {
        const container = document.createElement('div');
        container.className = 'apple-notification-container';
        document.body.appendChild(container);
    }
    
    // Initialize the notification system
    initAppleNotifications();
});

/**
 * Initialize the Apple-style notification system
 */
function initAppleNotifications() {
    // Override the default modal behavior for announcements
    overrideAnnouncementModals();
    
    // Add event listeners for notification clicks
    setupNotificationClickHandlers();
}

/**
 * Override the default modal behavior for announcements
 */
function overrideAnnouncementModals() {
    // Find all announcement items
    const announcementItems = document.querySelectorAll('.announcement-item, [x-data="{ showModal: false }"]');
    
    announcementItems.forEach(item => {
        // Find the clickable area
        const clickableArea = item.querySelector('.p-4, [x-data="{ showModal: false }"] > div');
        
        if (clickableArea) {
            // Replace the existing click handler
            const newClickable = clickableArea.cloneNode(true);
            clickableArea.parentNode.replaceChild(newClickable, clickableArea);
            
            // Add our custom click handler
            newClickable.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Get the announcement data
                const title = item.querySelector('h4, .text-lg, .font-semibold')?.textContent.trim() || 'Notification';
                const message = item.querySelector('p')?.textContent.trim() || '';
                const timeElement = item.querySelector('.text-xs, .text-gray-500');
                const time = timeElement ? timeElement.textContent.trim() : 'Now';
                
                // Check for badges
                const isPinned = item.querySelector('.pinned') !== null;
                const isImportant = item.querySelector('.important') !== null || 
                                   item.innerHTML.includes('Important');
                
                // Show the Apple-style notification
                showAppleNotification({
                    title: title,
                    message: message,
                    time: time,
                    pinned: isPinned,
                    important: isImportant,
                    onClick: function() {
                        // Get the Alpine.js component
                        const alpineParent = item.closest('[x-data]');
                        
                        // If using Alpine.js, update the state
                        if (alpineParent && typeof alpineParent.__x !== 'undefined') {
                            if ('activeAnnouncement' in alpineParent.__x.getUnobservedData()) {
                                const id = item.dataset.id || item.dataset.announcementId;
                                if (id) {
                                    alpineParent.__x.updateData('activeAnnouncement', parseInt(id));
                                }
                            } else if ('showModal' in alpineParent.__x.getUnobservedData()) {
                                alpineParent.__x.updateData('showModal', true);
                            }
                        }
                    }
                });
            });
        }
    });
    
    // Watch for dynamically added announcements
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Element node
                        // Check if the node is an announcement item
                        if ((node.classList && 
                            (node.classList.contains('announcement-item') || 
                             node.hasAttribute('x-data')))) {
                            overrideAnnouncementClick(node);
                        } else {
                            // Check for announcement items inside the added node
                            const items = node.querySelectorAll('.announcement-item, [x-data="{ showModal: false }"]');
                            items.forEach(overrideAnnouncementClick);
                        }
                    }
                });
            }
        });
    });
    
    // Start observing the document body
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

/**
 * Override the click behavior for an announcement item
 * @param {HTMLElement} item - The announcement item element
 */
function overrideAnnouncementClick(item) {
    // Find the clickable area
    const clickableArea = item.querySelector('.p-4, [x-data="{ showModal: false }"] > div');
    
    if (clickableArea) {
        // Replace the existing click handler
        const newClickable = clickableArea.cloneNode(true);
        clickableArea.parentNode.replaceChild(newClickable, clickableArea);
        
        // Add our custom click handler
        newClickable.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get the announcement data
            const title = item.querySelector('h4, .text-lg, .font-semibold')?.textContent.trim() || 'Notification';
            const message = item.querySelector('p')?.textContent.trim() || '';
            const timeElement = item.querySelector('.text-xs, .text-gray-500');
            const time = timeElement ? timeElement.textContent.trim() : 'Now';
            
            // Check for badges
            const isPinned = item.querySelector('.pinned') !== null;
            const isImportant = item.querySelector('.important') !== null || 
                               item.innerHTML.includes('Important');
            
            // Show the Apple-style notification
            showAppleNotification({
                title: title,
                message: message,
                time: time,
                pinned: isPinned,
                important: isImportant,
                onClick: function() {
                    // Get the Alpine.js component
                    const alpineParent = item.closest('[x-data]');
                    
                    // If using Alpine.js, update the state
                    if (alpineParent && typeof alpineParent.__x !== 'undefined') {
                        if ('activeAnnouncement' in alpineParent.__x.getUnobservedData()) {
                            const id = item.dataset.id || item.dataset.announcementId;
                            if (id) {
                                alpineParent.__x.updateData('activeAnnouncement', parseInt(id));
                            }
                        } else if ('showModal' in alpineParent.__x.getUnobservedData()) {
                            alpineParent.__x.updateData('showModal', true);
                        }
                    }
                }
            });
        });
    }
}

/**
 * Set up click handlers for notification items
 */
function setupNotificationClickHandlers() {
    // Add click handlers to notification items
    document.addEventListener('click', function(e) {
        const notificationItem = e.target.closest('.notification-item');
        if (notificationItem) {
            e.preventDefault();
            
            // Get the notification data
            const title = notificationItem.querySelector('h3')?.textContent.trim() || 'Notification';
            const message = notificationItem.querySelector('p')?.textContent.trim() || '';
            const timeElement = notificationItem.querySelector('.text-xs, .text-gray-500');
            const time = timeElement ? timeElement.textContent.trim() : 'Now';
            
            // Show the Apple-style notification
            showAppleNotification({
                title: title,
                message: message,
                time: time,
                important: !notificationItem.classList.contains('bg-white'),
                onClick: function() {
                    // Get the URL to navigate to
                    const link = notificationItem.querySelector('a[href]');
                    if (link) {
                        window.location.href = link.getAttribute('href');
                    }
                }
            });
        }
    });
}

/**
 * Show an Apple-style notification
 * @param {Object} options - Notification options
 * @param {string} options.title - Notification title
 * @param {string} options.message - Notification message
 * @param {string} options.time - Time string
 * @param {boolean} options.pinned - Whether the notification is pinned
 * @param {boolean} options.important - Whether the notification is important
 * @param {Function} options.onClick - Click handler function
 */
function showAppleNotification(options) {
    // Get the container
    const container = document.querySelector('.apple-notification-container');
    
    // Create the notification element
    const notification = document.createElement('div');
    notification.className = 'apple-notification';
    
    // Set the HTML content
    notification.innerHTML = `
        <div class="apple-notification-header">
            <div class="apple-notification-icon">N</div>
            <div class="apple-notification-title">${options.title}</div>
            <div class="apple-notification-time">${options.time}</div>
        </div>
        <div class="apple-notification-content">
            <div class="apple-notification-message">${options.message}</div>
            <div class="apple-notification-badges">
                ${options.pinned ? '<span class="apple-notification-badge pinned">Pinned</span>' : ''}
                ${options.important ? '<span class="apple-notification-badge important">Important</span>' : ''}
            </div>
        </div>
        <div class="apple-notification-close">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
    `;
    
    // Add click handler
    notification.addEventListener('click', function(e) {
        // If clicking the close button, just close the notification
        if (e.target.closest('.apple-notification-close')) {
            closeNotification(notification);
            return;
        }
        
        // Otherwise, trigger the onClick handler and close the notification
        if (typeof options.onClick === 'function') {
            options.onClick();
        }
        
        closeNotification(notification);
    });
    
    // Add the notification to the container
    container.appendChild(notification);
    
    // Trigger a reflow to ensure the transition works
    notification.offsetHeight;
    
    // Show the notification
    notification.classList.add('visible');
    
    // Auto-close after 5 seconds
    setTimeout(() => {
        closeNotification(notification);
    }, 5000);
}

/**
 * Close a notification
 * @param {HTMLElement} notification - The notification element to close
 */
function closeNotification(notification) {
    // Add the exiting class
    notification.classList.add('exiting');
    
    // Remove the notification after the transition
    setTimeout(() => {
        notification.remove();
    }, 500);
}
