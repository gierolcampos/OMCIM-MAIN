<x-app-layout>
    @section('styles')
    <style>
        .officer-image {
            transition: transform 0.3s ease;
            overflow: hidden;
            cursor: pointer;
        }

        .officer-image img {
            transition: transform 0.3s ease;
        }

        .officer-image:hover img {
            transform: scale(1.1);
        }

        /* Close button animation */
        #closeModal {
            transition: transform 0.2s ease;
        }

        #closeModal:hover {
            transform: rotate(90deg);
        }

        /* Debug button */
        .debug-button {
            position: fixed;
            bottom: 50px;
            right: 10px;
            background-color: #c21313;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            z-index: 9999;
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
                            <h3 class="text-2xl text-[#c21313] font-bold mb-4">ICS Student Leaders</h3>

                            <!-- SY 2024-2025 -->
                            <div class="mb-10">
                                <div class="text-center mb-6">
                                    <div class="flex justify-center mb-3">
                                        <img src="{{ asset('img/ics-logo.png') }}" alt="ICS Logo" class="h-20">
                                    </div>
                                    <h4 class="text-xl font-bold text-[#c21313]">Integrated Computer Society (ICS) SY 2024-2025</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- President -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2024-2025/1.png') }}" alt="President" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">RICO P. ESCALICAS</h5>
                                                <p class="text-sm">PRESIDENT</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vice President -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2024-2025/2.png') }}" alt="Vice President" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">JOSHUA B. DELA CRUZ</h5>
                                                <p class="text-sm">VICE PRESIDENT</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Secretary -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2024-2025/3.png') }}" alt="Secretary" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">KIRSTEN ABEGYLE A. MANGALI</h5>
                                                <p class="text-sm">SECRETARY</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Treasurer -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2024-2025/4.png') }}" alt="Treasurer" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">ERICA B. LUMUTAC</h5>
                                                <p class="text-sm">TREASURER</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Auditor -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2024-2025/5.png') }}" alt="Auditor" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">JOAN P. FALCON</h5>
                                                <p class="text-sm">AUDITOR</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Business Manager -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2024-2025/6.png') }}" alt="Business Manager" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">LORENZ T. MAGDAEL</h5>
                                                <p class="text-sm">BUSINESS MANAGER</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PIO -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2024-2025/7.png') }}" alt="PIO" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">GEIROL M. CAMPOS</h5>
                                                <p class="text-sm">PIO</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SY 2023-2024 -->
                            <div class="mb-10">
                                <div class="text-center mb-6">
                                    <div class="flex justify-center mb-3">
                                        <img src="{{ asset('img/ics-logo.png') }}" alt="ICS Logo" class="h-20">
                                    </div>
                                    <h4 class="text-xl font-bold text-[#c21313]">Integrated Computer Society (ICS) SY 2022-2023</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- President -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2023-2024/pres2023.png') }}" alt="President" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">ARC ANGEL MATITO</h5>
                                                <p class="text-sm">PRESIDENT</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vice President -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2023-2024/vp2023.png') }}" alt="Vice President" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">JANZ VINCENT REYES</h5>
                                                <p class="text-sm">VICE PRESIDENT</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Secretary -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2023-2024/sec2023.png') }}" alt="Secretary" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">MERELIZA ANIBAN</h5>
                                                <p class="text-sm">SECRETARY</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Treasurer -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2023-2024/treasurer2023.png') }}" alt="Treasurer" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">REYBIE DELA CRUZ</h5>
                                                <p class="text-sm">TREASURER</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Auditor -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2023-2024/auditor2023.png') }}" alt="Auditor" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">MICHERVIN FRIAS</h5>
                                                <p class="text-sm">AUDITOR</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Business Manager -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2023-2024/busman2023.png') }}" alt="Business Manager" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold">JAYSON MARTINEZ</h5>
                                                <p class="text-sm">BUSINESS MANAGER</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SY 2022-2023 -->
                            <div class="mb-10">
                                <div class="text-center mb-6">
                                    <div class="flex justify-center mb-3">
                                        <img src="{{ asset('img/ics-logo.png') }}" alt="ICS Logo" class="h-20">
                                    </div>
                                    <h4 class="text-xl font-bold text-[#c21313]">Integrated Computer Society (ICS) SY 2022-2023</h4>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <!-- Adviser -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2022-2023/adviser.png') }}" alt="Adviser" class="w-full h-48 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold text-sm">EDSAN C. MORENO</h5>
                                                <p class="text-xs">ADVISER</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- President -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2022-2023/pres2022.png') }}" alt="President" class="w-full h-48 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold text-sm">MICHAEL DENOSTA</h5>
                                                <p class="text-xs">PRESIDENT</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vice President -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2022-2023/vp2022.png') }}" alt="Vice President" class="w-full h-48 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold text-sm">DANICA REYES</h5>
                                                <p class="text-xs">VICE PRESIDENT</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Secretary -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2022-2023/sec2022.png') }}" alt="Secretary" class="w-full h-48 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold text-sm">RONA GUIMALAN</h5>
                                                <p class="text-xs">SECRETARY</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Treasurer -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2022-2023/treasurer2022.png') }}" alt="Treasurer" class="w-full h-48 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold text-sm">LOVELY RICAFORTE</h5>
                                                <p class="text-xs">TREASURER</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Auditor -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2022-2023/auditor.png') }}" alt="Auditor" class="w-full h-48 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold text-sm">DANIELLA PACALA </h5>
                                                <p class="text-xs">AUDITOR</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Business Manager -->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2022-2023/busman.png') }}" alt="Business Manager" class="w-full h-48 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold text-sm">CARLA MAE LINDA</h5>
                                                <p class="text-xs">BUSINESS MANAGER</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PIO-->
                                    <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200">
                                        <div class="relative officer-image">
                                            <img src="{{ asset('img/officers/2022-2023/pio.png') }}" alt="PIO" class="w-full h-48 object-cover">
                                            <div class="absolute bottom-0 left-0 right-0 bg-[#c21313] text-white p-2 text-center">
                                                <h5 class="font-bold text-sm">ALLEAH FUENTES</h5>
                                                <p class="text-xs">PIO</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Note about past officers -->
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-6">
                                <p class="text-gray-700">
                                    <i class="fas fa-info-circle text-[#c21313] mr-2"></i>
                                    The ICS Organization honors all past officers who have contributed to the growth and success of our society.
                                    Their leadership and dedication have helped shape the organization into what it is today.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Officer Modal - Simplified Version -->
    <div id="officerModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto relative">
            <!-- Close button -->
            <button id="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-[#c21313] focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal content -->
            <div class="p-8 flex flex-col md:flex-row">
                <!-- Left side: Position description -->
                <div class="md:w-3/5 pr-0 md:pr-12 mb-6 md:mb-0">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h3 id="modalTitle" class="text-3xl font-bold text-[#c21313] mb-2"></h3>
                        <h4 id="modalOfficer" class="text-2xl font-semibold text-gray-800"></h4>
                    </div>
                    <div id="modalDescription" class="text-gray-700 prose prose-lg max-w-none"></div>
                </div>

                <!-- Right side: Image -->
                <div class="md:w-2/5">
                    <div class="bg-gray-50 rounded-xl overflow-hidden shadow-inner border border-gray-100">
                        <div class="relative pb-4">
                            <img id="modalImage" src="" alt="Officer" class="w-full h-auto object-contain max-h-[500px]">
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-500">Click anywhere outside or press ESC to close</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Position descriptions
        const officerDescriptions = {
            'PRESIDENT': `<p class="mb-3">The President serves as the chief executive officer of the Integrated Computer Society (ICS), providing leadership and direction to the organization.</p>
                        <p class="mb-3">Key responsibilities include:</p>
                        <ul class="list-disc pl-5 mb-3">
                            <li>Presiding over all meetings of the organization</li>
                            <li>Represents the ICS Organization on official meetings and external/internal affairs when deemed necessary and report to the ICS officers</li>
                            <li>Implement all plans, programs, and projects of the organization</li>
                            <li>Coordinating with faculty advisers and school administration</li>
                            <li>Sign all official minutes, resolution, correspondence, and other official paper/documents in the name of ICS Organization. </li>
                        </ul>
                        <p>The President works closely with other officers to foster a collaborative environment that promotes academic excellence and professional development in the field of computing.</p>`,

            'VICE PRESIDENT': `<p class="mb-3">The Vice President assists the President in leading the Integrated Computer Society (ICS) and assumes presidential duties in the President's absence.</p>
                        <p class="mb-3">Key responsibilities include:</p>
                        <ul class="list-disc pl-5 mb-3">
                            <li>Supporting the President in executing organizational plans</li>
                            <li>Assume the duties of the President in the absence of the ICS President. </li>
                            <li>Assume the office of the President in case of incapacity, resignation, impeachment or permanent vacancy of the latter</li>
                            <li>Overseeing committee operations and special projects</li>
                            <li>Coordinating internal activities and programs</li>
                            <li>Facilitating communication between officers and members</li>
                            <li>Developing leadership within the organization</li>
                        </ul>
                        <p>The Vice President plays a crucial role in maintaining organizational continuity and ensuring that ICS initiatives are implemented effectively.</p>`,

            'SECRETARY': `<p class="mb-3">The Secretary maintains official records and documentation for the Integrated Computer Society (ICS).</p>
                        <p class="mb-3">Key responsibilities include:</p>
                        <ul class="list-disc pl-5 mb-3">
                            <li>Assist both President and Vice President when necessary</li>
                            <li>Overseeing daily and administrative affairs and serving as the official custodian of all ICSO records and documents</li>
                            <li>Recording minutes of all meetings and proceedings</li>
                            <li>Managing the ICSO office and handling all internal and external correspondence</li>
                            <li>Preparing and distributing official documents</li>
                            <li>Coordinating with other offices to maintain an organized and effective filing system</li>
                            <li>Supervising the dissemination of information on ICSO activities, programs, and campaigns through publicity and publications</li>
                            <li>Preparing meeting agendas in consultation with members.</li>
                            <li>Maintaining membership records and attendance</li>
                        </ul>
                        <p>The Secretary ensures that ICS maintains accurate records and effective communication channels, supporting transparent and efficient organizational operations.</p>`,

            'TREASURER': `<p class="mb-3">The Treasurer oversees all financial matters of the Integrated Computer Society (ICS).</p>
                        <p class="mb-3">Key responsibilities include:</p>
                        <ul class="list-disc pl-5 mb-3">
                            <li>Design and implement plans for managing ICSO funds</li>
                            <li>Maintain financial documents for effective monitoring and auditing</li>
                            <li>Prepare financial statements, reports, and records</li>
                            <li>Monitor fund-raising activities under ICSO</li>
                            <li>Review income and expenditure regularly and recommend necessary actions</li>
                            <li>Oversee all receipts and disbursements of funds</li>
                            <li>Ensuring compliance with financial regulations</li>
                        </ul>
                        <p>The Treasurer maintains financial transparency and accountability, ensuring that ICS has the resources needed to fulfill its mission and objectives.</p>`,

            'AUDITOR': `<p class="mb-3">The Auditor ensures financial integrity and accountability within the Integrated Computer Society (ICS).</p>
                        <p class="mb-3">Key responsibilities include:</p>
                        <ul class="list-disc pl-5 mb-3">
                            <li>Prepare financial documents and records for auditing</li>
                            <li>Examine and audit all ICSO revenue, expenses, and disbursements</li>
                            <li>Verifying the accuracy of financial reports</li>
                            <li>Maintain an inventory of all ICSO properties</li>
                            <li>Ensuring compliance with financial policies</li>
                        </ul>
                        <p>The Auditor plays a critical role in maintaining the organization's financial integrity and ensuring that resources are used appropriately.</p>`,

            'BUSINESS MANAGER': `<p class="mb-3">The Business Manager handles the business affairs and external relations of the Integrated Computer Society (ICS).</p>
                        <p class="mb-3">Key responsibilities include:</p>
                        <ul class="list-disc pl-5 mb-3">
                            <li>Ensure the stable and sound financial state of the organization</li>
                            <li>Submit all purchase receipts to the Treasurer</li>
                            <li>Coordinating sponsorships and fundraising activities</li>
                            <li>Managing business operations for events and projects</li>
                            <li>Negotiating contracts and agreements</li>
                            <li>Exploring opportunities for organizational growth</li>
                        </ul>
                        <p>The Business Manager helps ICS build sustainable relationships with external stakeholders and secure resources for organizational initiatives.</p>`,

            'PIO': `<p class="mb-3">The Public Information Officer (PIO) manages the public image and communications of the Integrated Computer Society (ICS).</p>
                        <p class="mb-3">Key responsibilities include:</p>
                        <ul class="list-disc pl-5 mb-3">
                            <li>Creating and distributing promotional materials</li>
                            <li>Managing social media accounts and online presence</li>
                            <li>Documenting organizational activities and events</li>
                            <li>Coordinating with media and publicity channels</li>
                            <li>Developing communication strategies</li>
                        </ul>
                        <p>The PIO ensures that ICS effectively communicates its mission, activities, and achievements to members and the broader community.</p>`,

            'ADVISER': `<p class="mb-3">The Adviser provides guidance and support to the Integrated Computer Society (ICS) from a faculty perspective.</p>
                        <p class="mb-3">Key responsibilities include:</p>
                        <ul class="list-disc pl-5 mb-3">
                            <li>Offering professional advice and mentorship</li>
                            <li>Facilitating connections with academic and industry resources</li>
                            <li>Ensuring alignment with institutional policies</li>
                            <li>Supporting leadership development</li>
                            <li>Providing continuity across academic years</li>
                        </ul>
                        <p>The Adviser helps bridge the gap between student initiatives and professional standards, enhancing the educational value of ICS activities.</p>`
        };

        // Simple Modal Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get modal elements
            const modal = document.getElementById('officerModal');
            const closeModalBtn = document.getElementById('closeModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalOfficer = document.getElementById('modalOfficer');
            const modalDescription = document.getElementById('modalDescription');
            const modalImage = document.getElementById('modalImage');

            // Add click event to all officer images
            document.querySelectorAll('.officer-image').forEach(officer => {
                officer.addEventListener('click', function() {
                    // Get officer details
                    const position = this.querySelector('p').textContent.trim();
                    const name = this.querySelector('h5').textContent.trim();
                    const imageSrc = this.querySelector('img').src;

                    // Set modal content
                    modalTitle.textContent = position;
                    modalOfficer.textContent = name;
                    modalDescription.innerHTML = officerDescriptions[position] ||
                        '<p>Information about this position is currently being updated.</p>';
                    modalImage.src = imageSrc;
                    modalImage.alt = name + ' - ' + position;

                    // Show modal
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling

                    // Add active state to the clicked officer card
                    this.closest('.bg-white').classList.add('ring-2', 'ring-[#c21313]', 'ring-opacity-50');
                });
            });

            // Function to close modal
            function closeModal() {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.style.overflow = ''; // Re-enable scrolling

                // Remove active state from all officer cards
                document.querySelectorAll('.ring-2.ring-[#c21313]').forEach(el => {
                    el.classList.remove('ring-2', 'ring-[#c21313]', 'ring-opacity-50');
                });
            }

            // Close modal when clicking the close button
            closeModalBtn.addEventListener('click', closeModal);

            // Close modal when clicking outside the content
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });

            // Close modal when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('flex')) {
                    closeModal();
                }
            });

            // No reload button needed
        });
    </script>
</x-app-layout>