/**
 * Notification Interaction Fixes
 * Prevents flickering when clicking on notifications
 */
document.addEventListener('DOMContentLoaded', function() {
    // Apply fixes to all notification cards
    initNotificationCards();

    // Watch for dynamically added notification cards
    observeNewNotifications();
});

/**
 * Initialize all notification cards with proper event handling
 */
function initNotificationCards() {
    const notificationCards = document.querySelectorAll('.notification-card, .announcement-item');
    
    notificationCards.forEach(card => {
        // Remove any existing click listeners to prevent duplicates
        const newCard = card.cloneNode(true);
        card.parentNode.replaceChild(newCard, card);
        
        // Add proper click handling
        setupCardClickHandling(newCard);
    });
}

/**
 * Set up proper click handling for a notification card
 * @param {HTMLElement} card - The notification card element
 */
function setupCardClickHandling(card) {
    // Prevent default click behavior that might cause flickering
    card.addEventListener('click', function(e) {
        // If the click is on a button or link inside the card, let it handle normally
        if (e.target.closest('button, a, [data-no-action]')) {
            return;
        }
        
        e.preventDefault();
        e.stopPropagation();
        
        // Get the card data
        const cardId = card.dataset.id || card.dataset.announcementId;
        const cardUrl = card.dataset.url;
        
        // If there's a URL to navigate to
        if (cardUrl) {
            // Add a visual feedback class before navigation
            card.classList.add('card-active');
            
            // Slight delay for the visual feedback to be visible
            setTimeout(() => {
                window.location.href = cardUrl;
            }, 150);
            return;
        }
        
        // If using Alpine.js for modals
        const alpineData = card.closest('[x-data]');
        if (alpineData && alpineData.__x) {
            // Check which Alpine variable to update
            if ('activeAnnouncement' in alpineData.__x.getUnobservedData()) {
                // Apply a visual feedback class
                card.classList.add('card-active');
                
                // Slight delay for the visual feedback
                setTimeout(() => {
                    alpineData.__x.updateData('activeAnnouncement', parseInt(cardId));
                }, 50);
            } else if ('showModal' in alpineData.__x.getUnobservedData()) {
                // Apply a visual feedback class
                card.classList.add('card-active');
                
                // Slight delay for the visual feedback
                setTimeout(() => {
                    alpineData.__x.updateData('showModal', true);
                }, 50);
            }
        }
    });
    
    // Add touch event handling for mobile
    card.addEventListener('touchstart', function() {
        card.classList.add('card-touch');
    });
    
    card.addEventListener('touchend', function() {
        card.classList.remove('card-touch');
    });
    
    // Prevent text selection on double click
    card.addEventListener('mousedown', function(e) {
        if (e.detail > 1) {
            e.preventDefault();
        }
    });
}

/**
 * Observe the DOM for dynamically added notification cards
 */
function observeNewNotifications() {
    // Create a mutation observer to watch for new notification cards
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(node => {
                    // Check if the added node is a notification card or contains one
                    if (node.nodeType === 1) { // Element node
                        if (node.classList && 
                            (node.classList.contains('notification-card') || 
                             node.classList.contains('announcement-item'))) {
                            setupCardClickHandling(node);
                        } else {
                            // Check for notification cards inside the added node
                            const cards = node.querySelectorAll('.notification-card, .announcement-item');
                            cards.forEach(setupCardClickHandling);
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
