<x-app-layout>
    @section('styles')
    <style>
        .dev-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .dev-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .dev-image-container {
            overflow: hidden;
            position: relative;
        }

        .dev-image {
            transition: transform 0.5s ease;
        }

        .dev-card:hover .dev-image {
            transform: scale(1.05);
        }

        .social-icon {
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .social-icon:hover {
            transform: translateY(-3px);
            color: #c21313;
        }

        .tech-badge {
            transition: all 0.2s ease;
        }

        .tech-badge:hover {
            background-color: #c21313;
            color: white;
            transform: scale(1.05);
        }

        .quote-mark {
            font-size: 4rem;
            line-height: 1;
            color: rgba(194, 19, 19, 0.1);
            font-family: Georgia, serif;
            position: absolute;
            top: -10px;
            left: -15px;
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
                            <h3 class="text-2xl text-[#c21313] font-bold mb-6">Meet Our Development Team</h3>

                            <p class="mb-8 text-gray-700">
                                The OMCMS platform was designed and developed by a dedicated team of student developers from Navotas Polytechnic College.
                                Their expertise, creativity, and commitment have made this system possible.
                            </p>

                            <!-- Developers Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                                <!-- Developer 1 -->
                                <div class="dev-card bg-white rounded-xl overflow-hidden shadow-lg border border-gray-100">
                                    <div class="dev-image-container h-64">
                                        <img src="{{ asset('img/giee.jpg') }}" alt="Developer 1" class="dev-image w-full h-full object-cover">
                                    </div>
                                    <div class="p-6">
                                        <h4 class="text-xl font-bold text-[#c21313]">Geirol M. Campos</h4>
                                        <p class="text-gray-600 mb-3">Lead Developer</p>
                                        <div class="border-t border-gray-100 my-4"></div>
                                        <div class="relative">
                                            <span class="quote-mark">"</span>
                                            <p class="text-gray-700 italic text-sm mb-4 relative z-10">
                                                Passionate about creating intuitive user experiences and robust backend systems.
                                            </p>
                                        </div>
                                        <div class="flex space-x-3 mb-4">
                                            <a href="#" class="social-icon text-gray-500">
                                                <i class="fab fa-github text-xl"></i>
                                            </a>
                                            <a href="#" class="social-icon text-gray-500">
                                                <i class="fab fa-linkedin text-xl"></i>
                                            </a>
                                            <a href="#" class="social-icon text-gray-500">
                                                <i class="fas fa-envelope text-xl"></i>
                                            </a>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Laravel</span>
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">HTML/CSS</span>
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">PHP</span>
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">MySQL</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Developer 2 -->
                                <div class="dev-card bg-white rounded-xl overflow-hidden shadow-lg border border-gray-100">
                                    <div class="dev-image-container h-64">
                                        <img src="{{ asset('img/kitenn.jpg') }}" alt="Developer 2" class="dev-image w-full h-full object-cover">
                                    </div>
                                    <div class="p-6">
                                        <h4 class="text-xl font-bold text-[#c21313]">Kirsten Abegyle A. Mangali</h4>
                                        <p class="text-gray-600 mb-3">Frontend Developer</p>
                                        <div class="border-t border-gray-100 my-4"></div>
                                        <div class="relative">
                                            <span class="quote-mark">"</span>
                                            <p class="text-gray-700 italic text-sm mb-4 relative z-10">
                                                Dedicated to crafting beautiful, responsive interfaces that enhance user engagement.
                                            </p>
                                        </div>
                                        <div class="flex space-x-3 mb-4">
                                            <a href="#" class="social-icon text-gray-500">
                                                <i class="fab fa-github text-xl"></i>
                                            </a>
                                            <a href="#" class="social-icon text-gray-500">
                                                <i class="fab fa-linkedin text-xl"></i>
                                            </a>
                                            <a href="#" class="social-icon text-gray-500">
                                                <i class="fas fa-envelope text-xl"></i>
                                            </a>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">HTML/CSS</span>
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">JavaScript</span>
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Tailwind CSS</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Developer 3 -->
                                <div class="dev-card bg-white rounded-xl overflow-hidden shadow-lg border border-gray-100">
                                    <div class="dev-image-container h-64">
                                        <img src="{{ asset('img/rico.jpg') }}" alt="Developer 3" class="dev-image w-full h-full object-cover">
                                    </div>
                                    <div class="p-6">
                                        <h4 class="text-xl font-bold text-[#c21313]">Rico P. Escalicas</h4>
                                        <p class="text-gray-600 mb-3">Backend Developer</p>
                                        <div class="border-t border-gray-100 my-4"></div>
                                        <div class="relative">
                                            <span class="quote-mark">"</span>
                                            <p class="text-gray-700 italic text-sm mb-4 relative z-10">
                                                Focused on building secure, scalable systems that power modern web applications.
                                            </p>
                                        </div>
                                        <div class="flex space-x-3 mb-4">
                                            <a href="#" class="social-icon text-gray-500">
                                                <i class="fab fa-github text-xl"></i>
                                            </a>
                                            <a href="#" class="social-icon text-gray-500">
                                                <i class="fab fa-linkedin text-xl"></i>
                                            </a>
                                            <a href="#" class="social-icon text-gray-500">
                                                <i class="fas fa-envelope text-xl"></i>
                                            </a>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">PHP</span>
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Laravel</span>
                                            <span class="tech-badge px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Tailwind CSS</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Development Process Section -->
                            <div class="bg-gray-50 rounded-xl p-6 mb-8">
                                <h4 class="text-xl font-bold text-[#c21313] mb-4">Our Development Process</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="bg-white p-5 rounded-lg shadow-sm">
                                        <div class="flex items-center mb-3">
                                            <div class="bg-[#c21313] text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</div>
                                            <h5 class="font-bold">Planning & Design</h5>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            We began with extensive research and planning, creating wireframes and prototypes to visualize the system.
                                        </p>
                                    </div>
                                    <div class="bg-white p-5 rounded-lg shadow-sm">
                                        <div class="flex items-center mb-3">
                                            <div class="bg-[#c21313] text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</div>
                                            <h5 class="font-bold">Development</h5>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            Using modern frameworks and best practices, we built the system with a focus on security and performance.
                                        </p>
                                    </div>
                                    <div class="bg-white p-5 rounded-lg shadow-sm">
                                        <div class="flex items-center mb-3">
                                            <div class="bg-[#c21313] text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</div>
                                            <h5 class="font-bold">Testing & Deployment</h5>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            Rigorous testing ensured a bug-free experience, followed by careful deployment and ongoing maintenance.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Technologies Used Section -->
                            <div class="mb-8">
                                <h4 class="text-xl font-bold text-[#c21313] mb-4">Technologies Used</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                                        <i class="fab fa-laravel text-4xl text-[#c21313] mb-2"></i>
                                        <p class="font-semibold">Laravel</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                                        <i class="fab fa-js text-4xl text-[#c21313] mb-2"></i>
                                        <p class="font-semibold">JavaScript</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                                        <i class="fas fa-database text-4xl text-[#c21313] mb-2"></i>
                                        <p class="font-semibold">MySQL</p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                                        <i class="fab fa-css3-alt text-4xl text-[#c21313] mb-2"></i>
                                        <p class="font-semibold">Tailwind CSS</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Section -->
                            <div class="bg-[#c21313] bg-opacity-5 rounded-xl p-6">
                                <h4 class="text-xl font-bold text-[#c21313] mb-4">Get in Touch</h4>
                                <p class="mb-4">
                                    Have questions about the development of OMCMS or interested in collaborating on future projects?
                                    Feel free to reach out to our development team.
                                </p>
                                <a href="mailto:developers@navotaspolytechniccollege.edu.ph" class="inline-block bg-[#c21313] text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition-colors">
                                    Contact Us
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>