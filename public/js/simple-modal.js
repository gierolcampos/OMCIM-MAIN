/**
 * Simple Modal - Basic modal functionality without flickering
 */
document.addEventListener('DOMContentLoaded', function() {
    // Make sure Alpine.js is loaded
    if (typeof Alpine === 'undefined') {
        console.warn('Alpine.js is not loaded. Modal functionality may not work properly.');
        return;
    }
    
    // Add x-cloak to all modals that don't have it
    document.querySelectorAll('[x-show="showModal"]').forEach(modal => {
        if (!modal.hasAttribute('x-cloak')) {
            modal.setAttribute('x-cloak', '');
        }
    });
});
