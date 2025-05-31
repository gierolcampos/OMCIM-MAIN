/**
 * Modal Smooth - Prevents flickering and improves modal transitions
 * 
 * This script fixes issues with Alpine.js modals:
 * 1. Prevents flickering when opening/closing modals
 * 2. Ensures smooth transitions
 * 3. Prevents event propagation issues
 */
document.addEventListener('DOMContentLoaded', function() {
    // Apply hardware acceleration to modals for smoother animations
    const modals = document.querySelectorAll('[x-show="showModal"]');
    modals.forEach(modal => {
        // Add hardware acceleration
        modal.style.transform = 'translateZ(0)';
        modal.style.backfaceVisibility = 'hidden';
        modal.style.willChange = 'opacity';
    });

    // Prevent double-click issues on announcement items
    const announcementItems = document.querySelectorAll('.announcement-item');
    announcementItems.forEach(item => {
        item.addEventListener('mousedown', function(e) {
            if (e.detail > 1) {
                e.preventDefault();
            }
        });
    });

    // Prevent body scrolling when modal is open
    document.addEventListener('click', function(e) {
        // Check if the clicked element is a modal trigger
        const modalTrigger = e.target.closest('.announcement-item');
        if (modalTrigger) {
            // Add a small delay to ensure Alpine.js has time to process
            setTimeout(() => {
                const modalOpen = document.querySelector('[x-show="showModal"]:not([style*="display: none"])');
                if (modalOpen) {
                    document.body.style.overflow = 'hidden';
                }
            }, 50);
        }

        // Check if the clicked element is a modal close button
        const closeButton = e.target.closest('[x-on\\:click\\.stop="showModal = false"]');
        if (closeButton) {
            // Add a small delay to ensure Alpine.js has time to process
            setTimeout(() => {
                document.body.style.overflow = '';
            }, 300); // Match this with the transition duration
        }
    });

    // Also handle the backdrop click
    document.addEventListener('click', function(e) {
        const backdrop = e.target.closest('.fixed.inset-0.transition-opacity');
        if (backdrop) {
            // Add a small delay to ensure Alpine.js has time to process
            setTimeout(() => {
                document.body.style.overflow = '';
            }, 300); // Match this with the transition duration
        }
    });
});
