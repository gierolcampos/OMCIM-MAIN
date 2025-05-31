<style>
    .footer {
        background-color: #1a1a1a;
        color: #ffffff;
        padding: 4rem 0 0 0;
    }
    .footer-heading {
        color: #ffffff;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        position: relative;
    }
    .footer-heading::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -8px;
        width: 40px;
        height: 2px;
        background-color: #c21313;
    }
    .footer-link {
        color: #9ca3af;
        transition: all 0.3s ease;
        display: block;
        margin-bottom: 0.75rem;
        text-decoration: none;
    }
    .footer-link:hover {
        color: #ffffff;
        transform: translateX(5px);
    }
    .footer-social {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.1);
        margin-right: 0.75rem;
        transition: all 0.3s ease;
    }
    .footer-social:hover {
        background-color: #c21313;
        transform: translateY(-3px);
    }
    .footer-bottom {
        background-color: #141414;
        padding: 1.5rem 0;
        margin-top: 4rem;
    }
    .contact-info {
        color: #9ca3af;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<footer class="footer">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Column 1: About -->
            <div class="mb-8 md:mb-0">
                <h5 class="footer-heading">About OMCMS - ICS</h5>
                <p class="text-gray-400">
The Integrated Computer Society (ICS) of Navotas Polytechnic College empowers students to learn, connect, and lead through the Organization Management and Club Management System (OMCMS).                <div class="mt-4 flex">
                    <a href="https://www.facebook.com/npc.ics.organization" target="_blank" class="footer-social" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" class="footer-social" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://instagram.co   m" target="_blank" class="footer-social" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://linkedin.com" target="_blank" class="footer-social" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="mb-8 md:mb-0">
                <h5 class="footer-heading">Quick Links</h5>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <a href="{{ url('omcms/ics_hall') }}" class="footer-link">ICS Hall</a>
                        <a href="{{ url('omcms/events') }}" class="footer-link">Events</a>
                        @if (!Auth::user()->canManageAnnouncements())
                        <a href="{{ url('omcms/announcements') }}" class="footer-link">Announcements</a>
                        @else
                        <a href="{{ url('admin/announcements') }}" class="footer-link">Announcements</a>
                        @endif

                        @if(Auth::user()->isAdmin())
                        <a href="{{ url('admin/payments') }}" class="footer-link">Payments</a>
                        @else
                        <a href="{{ url('omcms/payments') }}" class="footer-link">Payments</a>
                        @endif
                    </div>
                    <div>
                        @if(Auth::user()->canManageMembers())
                        <a href="{{ url('admin/members') }}" class="footer-link">Members</a>
                        @endif
                        @if(Auth::user()->canGenerateReports())
                        <a href="{{ url('admin/letters') }}" class="footer-link">Reports</a>
                        @endif
                        <a href="{{ url('/aboutus') }}" class="footer-link">About Us</a>
                    </div>
                </div>
            </div>

            <!-- Column 3: Contact -->
            <div class="mb-8 md:mb-0">
                <h5 class="footer-heading">Contact Information</h5>
                <div class="space-y-4">
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Navotas Polytechnic College, Navotas City</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <span>ics@navotaspolytechniccollege.edu.ph</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+1 (877) 700-0666</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>Monday - Friday: 8:00 AM - 5:00 PM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright Section -->
    <div class="footer-bottom">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <p class="mb-0">&copy; {{ date('Y') }} OMCMS - All Rights Reserved</p>
                </div>
                <div class="flex flex-wrap justify-center md:justify-end gap-4">
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                    <a href="#" class="footer-link">FAQ</a>
                </div>
            </div>
        </div>
    </div>
</footer>
