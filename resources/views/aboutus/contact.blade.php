<x-app-layout>
    @section('styles')
    <style>
        .contact-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .tab-button {
            transition: all 0.3s ease;
        }

        .tab-button.active {
            background-color: #c21313;
            color: white;
        }

        .tab-button:hover:not(.active) {
            background-color: #f3f4f6;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            border-color: #c21313;
            box-shadow: 0 0 0 3px rgba(194, 19, 19, 0.2);
        }

        .rating-star {
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .rating-star:hover {
            transform: scale(1.2);
        }

        .rating-star.selected {
            color: #c21313;
        }

        .submit-button {
            transition: all 0.3s ease;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .map-container {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
        }

        .map-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.05);
            pointer-events: none;
            transition: background 0.3s ease;
        }

        .map-container:hover::after {
            background: rgba(0, 0, 0, 0);
        }
    </style>
    @endsection

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Sidebar -->
                <div class="md:w-1/4">
                    <x-about-sidebar />
                </div>

                <!-- Main Content -->
                <div class="md:w-3/4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-2xl text-[#c21313] font-bold mb-6">Contact Us</h3>

                            <!-- Alert Messages -->
                            @if(session('success'))
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                                    <p>{{ session('success') }}</p>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                                    <p>{{ session('error') }}</p>
                                </div>
                            @endif

                            <!-- Contact Information Cards -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                                <!-- Email -->
                                <div class="contact-card bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden h-full">
                                    <div class="p-6 text-center flex flex-col h-full">
                                        <div class="flex justify-center mb-5">
                                            <div class="bg-[#ffeaea] rounded-full p-4 w-20 h-20 flex items-center justify-center">
                                                <i class="fas fa-envelope text-[#c21313] text-2xl"></i>
                                            </div>
                                        </div>
                                        <h4 class="text-lg font-bold mb-2">Email Us</h4>
                                        <p class="text-gray-600 mb-3">For general inquiries and support</p>
                                        <a href="mailto:ics@navotaspolytechniccollege.edu.ph" class="text-[#c21313] hover:underline break-words text-sm md:text-base">ics@navotaspolytechniccollege.edu.ph</a>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="contact-card bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden h-full">
                                    <div class="p-6 text-center flex flex-col h-full">
                                        <div class="flex justify-center mb-5">
                                            <div class="bg-[#ffeaea] rounded-full p-4 w-20 h-20 flex items-center justify-center">
                                                <i class="fas fa-phone-alt text-[#c21313] text-2xl"></i>
                                            </div>
                                        </div>
                                        <h4 class="text-lg font-bold mb-2">Call Us</h4>
                                        <p class="text-gray-600 mb-3">Monday to Friday, 8am - 5pm</p>
                                        <a href="tel:+639123456789" class="text-[#c21313] hover:underline">(+63) 912-345-6789</a>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="contact-card bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden h-full">
                                    <div class="p-6 text-center flex flex-col h-full">
                                        <div class="flex justify-center mb-5">
                                            <div class="bg-[#ffeaea] rounded-full p-4 w-20 h-20 flex items-center justify-center">
                                                <i class="fas fa-map-marker-alt text-[#c21313] text-2xl"></i>
                                            </div>
                                        </div>
                                        <h4 class="text-lg font-bold mb-2">Visit Us</h4>
                                        <p class="text-gray-600 mb-3">Our campus location</p>
                                        <p class="text-[#c21313]">Navotas Polytechnic College,<br>Navotas City</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs for Contact Form and Feedback Form -->
                            <div class="mb-8">
                                <div class="flex border-b border-gray-200">
                                    <button id="contactTab" class="tab-button active py-2 px-4 font-medium rounded-t-lg" onclick="switchTab('contact')">
                                        Contact Form
                                    </button>
                                    <button id="feedbackTab" class="tab-button py-2 px-4 font-medium rounded-t-lg" onclick="switchTab('feedback')">
                                        Feedback Form
                                    </button>
                                </div>

                                <!-- Contact Form -->
                                <div id="contactForm" class="bg-white p-6 rounded-b-lg border border-t-0 border-gray-200">
                                    <p class="mb-4 text-gray-700">
                                        Have a question or need assistance? Fill out the form below and we'll get back to you as soon as possible.
                                    </p>

                                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                                        @csrf

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-600">*</span></label>
                                                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-[#c21313] focus:ring focus:ring-[#c21313] focus:ring-opacity-20">
                                                @error('name')
                                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-600">*</span></label>
                                                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-[#c21313] focus:ring focus:ring-[#c21313] focus:ring-opacity-20">
                                                @error('email')
                                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div>
                                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-600">*</span></label>
                                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-[#c21313] focus:ring focus:ring-[#c21313] focus:ring-opacity-20">
                                            @error('subject')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-600">*</span></label>
                                            <textarea id="message" name="message" rows="5" required class="form-textarea w-full rounded-md border-gray-300 shadow-sm focus:border-[#c21313] focus:ring focus:ring-[#c21313] focus:ring-opacity-20">{{ old('message') }}</textarea>
                                            @error('message')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="flex justify-end">
                                            <button type="submit" class="submit-button bg-[#c21313] text-white px-6 py-2 rounded-md hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:ring-opacity-50">
                                                Send Message
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Feedback Form -->
                                <div id="feedbackForm" class="bg-white p-6 rounded-b-lg border border-t-0 border-gray-200 hidden">
                                    <p class="mb-4 text-gray-700">
                                        Help us improve OMCMS by sharing your feedback, reporting bugs, or suggesting new features.
                                    </p>

                                    <form action="{{ route('feedback.submit') }}" method="POST" class="space-y-4">
                                        @csrf

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="feedback_name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-600">*</span></label>
                                                <input type="text" id="feedback_name" name="name" value="{{ old('name') }}" required class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-[#c21313] focus:ring focus:ring-[#c21313] focus:ring-opacity-20">
                                                @error('name')
                                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="feedback_email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-600">*</span></label>
                                                <input type="email" id="feedback_email" name="email" value="{{ old('email') }}" required class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-[#c21313] focus:ring focus:ring-[#c21313] focus:ring-opacity-20">
                                                @error('email')
                                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div>
                                            <label for="feedback_type" class="block text-sm font-medium text-gray-700 mb-1">Feedback Type <span class="text-red-600">*</span></label>
                                            <select id="feedback_type" name="feedback_type" required class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-[#c21313] focus:ring focus:ring-[#c21313] focus:ring-opacity-20">
                                                <option value="">Select a type</option>
                                                <option value="bug" {{ old('feedback_type') == 'bug' ? 'selected' : '' }}>Bug Report</option>
                                                <option value="feature" {{ old('feedback_type') == 'feature' ? 'selected' : '' }}>Feature Request</option>
                                                <option value="improvement" {{ old('feedback_type') == 'improvement' ? 'selected' : '' }}>Improvement Suggestion</option>
                                                <option value="other" {{ old('feedback_type') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('feedback_type')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">Your Feedback <span class="text-red-600">*</span></label>
                                            <textarea id="feedback" name="feedback" rows="5" required class="form-textarea w-full rounded-md border-gray-300 shadow-sm focus:border-[#c21313] focus:ring focus:ring-[#c21313] focus:ring-opacity-20">{{ old('feedback') }}</textarea>
                                            @error('feedback')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Rate Your Experience <span class="text-red-600">*</span></label>
                                            <div class="flex space-x-2">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="rating-star text-2xl cursor-pointer" data-rating="{{ $i }}">☆</span>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="rating" value="{{ old('rating', 0) }}" required>
                                            @error('rating')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="flex justify-end">
                                            <button type="submit" class="submit-button bg-[#c21313] text-white px-6 py-2 rounded-md hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:ring-opacity-50">
                                                Submit Feedback
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Map Section -->
                            <div class="mb-8">
                                <h4 class="text-xl font-bold text-[#c21313] mb-4">Find Us</h4>
                                <div class="map-container h-80 bg-gray-100">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.4106261456647!2d120.94149731483993!3d14.629700989779652!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b5f4b542b62b%3A0x5c5f5a8c3c3f3f3f!2sNavotas%20Polytechnic%20College!5e0!3m2!1sen!2sph!4v1620000000000!5m2!1sen!2sph" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                </div>
                            </div>

                            <!-- FAQ Section -->
                            <div>
                                <h4 class="text-xl font-bold text-[#c21313] mb-4">Frequently Asked Questions</h4>
                                <div class="space-y-4">
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <button class="faq-toggle w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none" onclick="toggleFAQ(this)">
                                            <span class="font-medium">How do I join the ICS organization?</span>
                                            <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                                        </button>
                                        <div class="faq-content hidden p-4 border-t border-gray-200">
                                            <p class="text-gray-700">
                                                To join the ICS organization, you need to be a student of Navotas Polytechnic College enrolled in an IT-related course. Visit the ICS office or contact us through this form for more information about membership registration and fees.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <button class="faq-toggle w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none" onclick="toggleFAQ(this)">
                                            <span class="font-medium">How can I reset my OMCMS password?</span>
                                            <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                                        </button>
                                        <div class="faq-content hidden p-4 border-t border-gray-200">
                                            <p class="text-gray-700">
                                                You can reset your password by clicking on the "Forgot Password" link on the login page. Follow the instructions sent to your registered email address to create a new password.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <button class="faq-toggle w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none" onclick="toggleFAQ(this)">
                                            <span class="font-medium">How do I register for ICS events?</span>
                                            <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                                        </button>
                                        <div class="faq-content hidden p-4 border-t border-gray-200">
                                            <p class="text-gray-700">
                                                You can register for ICS events through the OMCMS platform. Navigate to the Events section, select the event you're interested in, and click the "Register" or "Attend" button. Follow the instructions to complete your registration.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <button class="faq-toggle w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none" onclick="toggleFAQ(this)">
                                            <span class="font-medium">How can I make a payment for ICS membership?</span>
                                            <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"></i>
                                        </button>
                                        <div class="faq-content hidden p-4 border-t border-gray-200">
                                            <p class="text-gray-700">
                                                Payments for ICS membership can be made through the OMCMS platform. Go to the Payments section, select "Membership Fee" as the payment purpose, and choose your preferred payment method (GCash or Cash). Follow the instructions to complete your payment.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        function switchTab(tab) {
            const contactTab = document.getElementById('contactTab');
            const feedbackTab = document.getElementById('feedbackTab');
            const contactForm = document.getElementById('contactForm');
            const feedbackForm = document.getElementById('feedbackForm');

            if (tab === 'contact') {
                contactTab.classList.add('active');
                feedbackTab.classList.remove('active');
                contactForm.classList.remove('hidden');
                feedbackForm.classList.add('hidden');
            } else {
                contactTab.classList.remove('active');
                feedbackTab.classList.add('active');
                contactForm.classList.add('hidden');
                feedbackForm.classList.remove('hidden');
            }
        }

        // Rating stars functionality
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.rating-star');
            const ratingInput = document.getElementById('rating');

            // Set initial rating if it exists
            if (ratingInput.value > 0) {
                updateStars(ratingInput.value);
            }

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    ratingInput.value = rating;
                    updateStars(rating);
                });

                star.addEventListener('mouseover', function() {
                    const rating = this.getAttribute('data-rating');
                    highlightStars(rating);
                });

                star.addEventListener('mouseout', function() {
                    resetStars();
                    if (ratingInput.value > 0) {
                        updateStars(ratingInput.value);
                    }
                });
            });

            function updateStars(rating) {
                stars.forEach(star => {
                    const starRating = star.getAttribute('data-rating');
                    if (starRating <= rating) {
                        star.innerHTML = '★';
                        star.classList.add('selected');
                    } else {
                        star.innerHTML = '☆';
                        star.classList.remove('selected');
                    }
                });
            }

            function highlightStars(rating) {
                stars.forEach(star => {
                    const starRating = star.getAttribute('data-rating');
                    if (starRating <= rating) {
                        star.innerHTML = '★';
                    } else {
                        star.innerHTML = '☆';
                    }
                });
            }

            function resetStars() {
                stars.forEach(star => {
                    star.innerHTML = '☆';
                    star.classList.remove('selected');
                });
            }
        });

        // FAQ toggle functionality
        function toggleFAQ(element) {
            const content = element.nextElementSibling;
            const icon = element.querySelector('i');

            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>
</x-app-layout>