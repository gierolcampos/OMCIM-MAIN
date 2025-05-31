/**
 * Fullscreen Modal Implementation - Modified to prevent modals
 * This version prevents announcement modals from showing
 */
document.addEventListener('DOMContentLoaded', function() {
    // Add the CSS file to the head
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = '/css/fullscreen-modal.css';
    document.head.appendChild(link);

    // Wait for Alpine.js to initialize
    if (typeof Alpine !== 'undefined') {
        // If Alpine.js is already loaded
        preventAnnouncementModals();
    } else {
        // Wait for Alpine.js to load
        document.addEventListener('alpine:initialized', function() {
            preventAnnouncementModals();
        });

        // Fallback in case the event doesn't fire
        setTimeout(preventAnnouncementModals, 500);
    }
});

/**
 * Prevent announcement modals from showing
 */
function preventAnnouncementModals() {
    // Find all Alpine.js components that manage announcements
    const announcementComponents = document.querySelectorAll('[x-data*="activeAnnouncement"], [x-data*="showModal"]');

    // Disable the Alpine.js components
    announcementComponents.forEach(component => {
        // For components with x-data="{ activeAnnouncement: null }"
        if (component.hasAttribute('x-data') && component.getAttribute('x-data').includes('activeAnnouncement')) {
            // Override the x-data attribute
            component.setAttribute('x-data', '{ activeAnnouncement: null }');

            // Find all elements with @click that set activeAnnouncement
            component.querySelectorAll('[\\@click*="activeAnnouncement ="]').forEach(el => {
                // Remove the click attribute
                el.removeAttribute('@click');

                // Add a new click event that does nothing
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
        }

        // For components with x-data="{ showModal: false }"
        if (component.hasAttribute('x-data') && component.getAttribute('x-data').includes('showModal')) {
            // Override the x-data attribute
            component.setAttribute('x-data', '{ showModal: false }');

            // Find all elements with @click that set showModal
            component.querySelectorAll('[\\@click*="showModal ="]').forEach(el => {
                // Remove the click attribute
                el.removeAttribute('@click');

                // Add a new click event that does nothing
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
        }

        // If Alpine.js is already initialized on this component
        if (window.Alpine && component.__x) {
            try {
                // For activeAnnouncement components
                if ('activeAnnouncement' in component.__x.getUnobservedData()) {
                    // Override the setter for activeAnnouncement
                    Object.defineProperty(component.__x.getUnobservedData(), 'activeAnnouncement', {
                        set: function() { return null; },
                        get: function() { return null; }
                    });
                }

                // For showModal components
                if ('showModal' in component.__x.getUnobservedData()) {
                    // Override the setter for showModal
                    Object.defineProperty(component.__x.getUnobservedData(), 'showModal', {
                        set: function() { return false; },
                        get: function() { return false; }
                    });
                }
            } catch (e) {
                console.log('Error overriding Alpine.js data:', e);
            }
        }
    });

    // Find all announcement cards and news cards
    const announcementItems = document.querySelectorAll('.announcement-item, .news-card');

    // Prevent click events on these items
    announcementItems.forEach(item => {
        // Find the clickable area
        const clickableArea = item.querySelector('.p-4, div.block');
        if (clickableArea) {
            // Clone the element to remove all event listeners
            const newClickable = clickableArea.cloneNode(true);
            clickableArea.parentNode.replaceChild(newClickable, clickableArea);

            // Add a new click event that does nothing
            newClickable.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // Do nothing - this effectively disables the modal
            });
        }
    });

    // Also observe for dynamically added announcement items
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Element node
                        // Check if the node is an announcement item
                        if (node.classList &&
                            (node.classList.contains('announcement-item') ||
                             node.classList.contains('news-card'))) {
                            // Find the clickable area
                            const clickableArea = node.querySelector('.p-4, div.block');
                            if (clickableArea) {
                                // Clone the element to remove all event listeners
                                const newClickable = clickableArea.cloneNode(true);
                                clickableArea.parentNode.replaceChild(newClickable, clickableArea);

                                // Add a new click event that does nothing
                                newClickable.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    // Do nothing - this effectively disables the modal
                                });
                            }
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
