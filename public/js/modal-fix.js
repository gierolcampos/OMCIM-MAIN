/**
 * Enhanced Modal Fix for Announcement Modals
 *
 * This script fixes issues with modals:
 * 1. Prevents modals from reappearing after closing
 * 2. Fixes stuttering in desktop view
 * 3. Ensures smooth transitions without flickering
 * 4. Properly handles body overflow
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fix for body overflow issue when modal is closed
    document.body.classList.remove('overflow-hidden');
    document.body.classList.remove('overflow-y-hidden');

    // Enhanced modal close handling
    const setupModalCloseHandlers = () => {
        // Target all close buttons
        const modalCloseButtons = document.querySelectorAll('[data-modal-close]');

        modalCloseButtons.forEach(button => {
            // Remove any existing event listeners to prevent duplicates
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);

            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Find the closest modal container
                const modal = this.closest('.modal-backdrop, .modal-content');
                if (!modal) return;

                // Get the Alpine component
                const alpineEl = modal.closest('[x-data]');
                if (!alpineEl || !alpineEl.__x) return;

                // Prevent default Alpine behavior
                e.stopPropagation();

                // Add closing class for smooth animation
                modal.classList.add('modal-closing');

                // Set Alpine variables to close the modal
                if ('activeAnnouncement' in alpineEl.__x.getUnobservedData()) {
                    // Wait for animation to complete before changing Alpine state
                    setTimeout(() => {
                        alpineEl.__x.updateData('activeAnnouncement', null);
                        document.body.classList.remove('overflow-hidden');
                    }, 200); // Slightly shorter than CSS transition
                } else if ('showModal' in alpineEl.__x.getUnobservedData()) {
                    setTimeout(() => {
                        alpineEl.__x.updateData('showModal', false);
                        document.body.classList.remove('overflow-hidden');
                    }, 200);
                }
            });
        });
    };

    // Apply hardware acceleration to all modals
    const applyHardwareAcceleration = () => {
        const modals = document.querySelectorAll('.modal-backdrop, .modal-content');

        modals.forEach(modal => {
            // Apply hardware acceleration for smoother animations
            modal.style.transform = 'translateZ(0)';
            modal.style.backfaceVisibility = 'hidden';
            modal.style.willChange = 'transform, opacity';

            // Optimize for desktop
            if (window.innerWidth >= 1024) {
                modal.style.transition = 'all 0.25s cubic-bezier(0.4, 0, 0.2, 1)';
            }
        });
    };

    // Handle modal opening
    const handleModalOpen = () => {
        document.addEventListener('click', function(e) {
            // Check if the clicked element should open a modal
            const modalTrigger = e.target.closest('[x-data]');
            if (!modalTrigger) return;

            // Add hardware acceleration to any newly created modals
            setTimeout(() => {
                applyHardwareAcceleration();
                setupModalCloseHandlers();
            }, 50);
        });

        // Also handle modals that might be created by other means
        const observer = new MutationObserver((mutations) => {
            let modalAdded = false;

            mutations.forEach(mutation => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(node => {
                        if (node.classList &&
                            (node.classList.contains('modal-backdrop') ||
                             node.classList.contains('modal-content'))) {
                            modalAdded = true;
                        }
                    });
                }
            });

            if (modalAdded) {
                applyHardwareAcceleration();
                setupModalCloseHandlers();
            }
        });

        // Start observing the document
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    };

    // Initialize all fixes
    const initModalFixes = () => {
        applyHardwareAcceleration();
        setupModalCloseHandlers();
        handleModalOpen();

        // Also handle window resize
        window.addEventListener('resize', applyHardwareAcceleration);
    };

    // Run initialization
    initModalFixes();

    // Add data-modal-close attribute to all close buttons that might not have it
    document.querySelectorAll('.modal-backdrop button, .modal-content button').forEach(button => {
        if ((button.innerHTML.includes('M6 18L18 6M6 6l12 12') ||
             button.innerHTML.includes('×') ||
             button.classList.contains('close-button')) &&
            !button.hasAttribute('data-modal-close')) {
            button.setAttribute('data-modal-close', 'true');
        }
    });
});
