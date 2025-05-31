/**
 * Simple fix for notification card flickering
 * This script prevents the default click behavior and adds a smooth transition
 */
document.addEventListener('DOMContentLoaded', function() {
    // Add click handling to all announcement items
    const announcementItems = document.querySelectorAll('.announcement-item, [x-data="{ showModal: false }"]');
    
    announcementItems.forEach(item => {
        // Find the clickable area inside the item
        const clickableArea = item.querySelector('.p-4, [x-data="{ showModal: false }"] > div');
        
        if (clickableArea) {
            // Replace the existing click handler with our own
            const newClickable = clickableArea.cloneNode(true);
            clickableArea.parentNode.replaceChild(newClickable, clickableArea);
            
            // Add our custom click handler
            newClickable.addEventListener('click', function(e) {
                // Prevent default behavior that might cause flickering
                e.preventDefault();
                e.stopPropagation();
                
                // Add a visual feedback class
                item.classList.add('active-card');
                
                // Get the parent Alpine.js component
                const alpineParent = item.closest('[x-data]');
                
                // If using Alpine.js, update the state with a slight delay
                if (alpineParent && typeof alpineParent.__x !== 'undefined') {
                    setTimeout(() => {
                        // Set the showModal to true
                        alpineParent.__x.updateData('showModal', true);
                    }, 50);
                }
            });
        }
    });
    
    // Add styles to prevent flickering
    const style = document.createElement('style');
    style.textContent = `
        .announcement-item {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            will-change: transform, box-shadow;
        }
        .announcement-item.active-card {
            transform: scale(0.98);
        }
        .announcement-item:active {
            transform: scale(0.98);
        }
        [x-data="{ showModal: false }"] > div {
            user-select: none;
        }
    `;
    document.head.appendChild(style);
    
    // Create a mutation observer to handle dynamically added notifications
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Element node
                        // Check if the node is an announcement item or contains one
                        if (node.classList && node.classList.contains('announcement-item')) {
                            setupClickHandling(node);
                        } else {
                            // Check for announcement items inside the added node
                            const items = node.querySelectorAll('.announcement-item, [x-data="{ showModal: false }"]');
                            items.forEach(setupClickHandling);
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
    
    // Function to set up click handling for a notification item
    function setupClickHandling(item) {
        const clickableArea = item.querySelector('.p-4, [x-data="{ showModal: false }"] > div');
        
        if (clickableArea) {
            // Replace the existing click handler with our own
            const newClickable = clickableArea.cloneNode(true);
            clickableArea.parentNode.replaceChild(newClickable, clickableArea);
            
            // Add our custom click handler
            newClickable.addEventListener('click', function(e) {
                // Prevent default behavior that might cause flickering
                e.preventDefault();
                e.stopPropagation();
                
                // Add a visual feedback class
                item.classList.add('active-card');
                
                // Get the parent Alpine.js component
                const alpineParent = item.closest('[x-data]');
                
                // If using Alpine.js, update the state with a slight delay
                if (alpineParent && typeof alpineParent.__x !== 'undefined') {
                    setTimeout(() => {
                        // Set the showModal to true
                        alpineParent.__x.updateData('showModal', true);
                    }, 50);
                }
            });
        }
    }
});
