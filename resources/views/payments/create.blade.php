@extends('layouts.app')

@section('styles')
<style>
    /* Styles for the searchable dropdown */
    #member_search_results {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
    }

    #member_search_results ul li {
        transition: background-color 0.2s;
    }

    #member_search_results ul li:hover {
        background-color: #f3f4f6;
    }

    #selected_member_display {
        transition: all 0.3s ease;
    }

    .search-highlight {
        background-color: #fef3c7;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Record New Payment</h1>
                        <p class="text-gray-600 mt-1">Enter payment details for a member</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ Auth::user()->canManagePayments() ? route('admin.payments.index') : route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Payments
                        </a>
                        @if(Auth::user()->canManagePayments())
                        <a href="{{ route('admin.payment-fees.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-cog mr-2"></i> Manage Fees
                        </a>
                        @endif
                    </div>
                </div>

                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">{{ session('error') }}</p>
                        </div>
                        <button class="ml-auto" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times text-red-500"></i>
                        </button>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ Auth::user()->canManagePayments() ? route('admin.payments.store') : route('client.payments.store') }}" class="space-y-6" enctype="multipart/form-data" id="payment-form">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Payer Selection -->
                        @if(Auth::user()->canManagePayments())
                        <div class="md:col-span-2">
                            <div class="mb-4">
                                <label for="payer_type" class="block text-sm font-medium text-gray-700 mb-1">Payer Type <span class="text-red-500">*</span></label>
                                <select id="payer_type" name="payer_type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required onchange="handlePayerTypeChange(this.value)">
                                    <option value="">Select Payer Type</option>
                                    <option value="ics_member" {{ old('payer_type') == 'ics_member' ? 'selected' : '' }}>ICS Member</option>
                                    <option value="non_ics_member" {{ old('payer_type') == 'non_ics_member' ? 'selected' : '' }}>Non-ICS Member</option>
                                </select>
                                <script>
                                    function handlePayerTypeChange(value) {
                                        const icsMemberFields = document.getElementById('ics_member_fields');
                                        const nonIcsMemberFields = document.getElementById('non_ics_member_fields');
                                        const userEmailSelect = document.getElementById('user_email');

                                        // Hide both first
                                        if (icsMemberFields) icsMemberFields.style.display = 'none';
                                        if (nonIcsMemberFields) nonIcsMemberFields.style.display = 'none';

                                        // Then show the appropriate one
                                        if (value === 'ics_member') {
                                            if (icsMemberFields) {
                                                icsMemberFields.style.display = 'block';

                                                // Make ICS Member fields required
                                                if (userEmailSelect) {
                                                    userEmailSelect.required = true;
                                                    userEmailSelect.setAttribute('required', 'required');
                                                    console.log('Set user_email as required for ICS member');
                                                }

                                                // Focus on the search input
                                                setTimeout(function() {
                                                    const searchInput = document.getElementById('user_email_search');
                                                    if (searchInput) searchInput.focus();
                                                }, 100);
                                            }

                                            // Make non-ICS fields not required
                                            const nonIcsEmail = document.getElementById('non_ics_email');
                                            const nonIcsFullname = document.getElementById('non_ics_fullname');
                                            const courseYearSection = document.getElementById('course_year_section');

                                            if (nonIcsEmail) {
                                                nonIcsEmail.required = false;
                                                nonIcsEmail.removeAttribute('required');
                                            }

                                            if (nonIcsFullname) {
                                                nonIcsFullname.required = false;
                                                nonIcsFullname.removeAttribute('required');
                                            }

                                            if (courseYearSection) {
                                                courseYearSection.required = false;
                                                courseYearSection.removeAttribute('required');
                                            }
                                        } else if (value === 'non_ics_member') {
                                            if (nonIcsMemberFields) {
                                                nonIcsMemberFields.style.display = 'block';

                                                // Make ICS Member fields NOT required
                                                if (userEmailSelect) {
                                                    userEmailSelect.required = false;
                                                    userEmailSelect.removeAttribute('required');
                                                    console.log('Removed required attribute from user_email for Non-ICS member');
                                                }

                                                // Make sure non-ICS fields are required
                                                const nonIcsEmail = document.getElementById('non_ics_email');
                                                const nonIcsFullname = document.getElementById('non_ics_fullname');
                                                const courseYearSection = document.getElementById('course_year_section');

                                                if (nonIcsEmail) {
                                                    nonIcsEmail.required = true;
                                                    nonIcsEmail.setAttribute('required', 'required');
                                                }

                                                if (nonIcsFullname) {
                                                    nonIcsFullname.required = true;
                                                    nonIcsFullname.setAttribute('required', 'required');
                                                }

                                                if (courseYearSection) {
                                                    courseYearSection.required = true;
                                                    courseYearSection.setAttribute('required', 'required');
                                                }

                                                // Focus on the email input
                                                setTimeout(function() {
                                                    const emailInput = document.getElementById('non_ics_email');
                                                    if (emailInput) emailInput.focus();
                                                }, 100);
                                            }
                                        }

                                        // Also call the main toggle function
                                        if (typeof togglePayerFields === 'function') {
                                            togglePayerFields();
                                        }
                                    }

                                    // Call the handler on page load
                                    document.addEventListener('DOMContentLoaded', function() {
                                        setTimeout(function() {
                                            const payerType = document.getElementById('payer_type');
                                            if (payerType) {
                                                handlePayerTypeChange(payerType.value);
                                            }
                                        }, 300);
                                    });
                                </script>
                                @error('payer_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ICS Member Fields -->
                            <div id="ics_member_fields" style="display: none;">
                                <label for="user_email_search" class="block text-sm font-medium text-gray-700 mb-1">Payer <span class="text-red-500">*</span></label>

                                <div class="relative">
                                    <!-- Search input -->
                                    <div class="relative">
                                        <input type="text" id="user_email_search"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            placeholder="Search by name or email..."
                                            autocomplete="off"
                                            onkeyup="filterMembers(this.value)">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                    </div>

                                    <!-- Hidden actual select that will be submitted -->
                                    <select id="user_email" name="user_email" class="hidden" required>
                                        <option value="">Select ICS Member</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->email }}" {{ old('user_email') == $user->email ? 'selected' : '' }}
                                                data-fullname="{{ strtolower($user->fullname) }}"
                                                data-email="{{ strtolower($user->email) }}">
                                                {{ $user->fullname }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Dropdown results -->
                                    <div id="member_search_results" class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg max-h-60 overflow-y-auto hidden">
                                        <ul class="py-1 text-sm text-gray-700">
                                            <!-- Results will be populated here by JavaScript -->
                                        </ul>
                                    </div>
                                </div>

                                <!-- Selected member display -->
                                <div id="selected_member_display" class="mt-2 p-2 bg-gray-50 rounded-lg border border-gray-200 {{ old('user_email') ? '' : 'hidden' }}">
                                    <p class="text-sm font-medium text-gray-700">Selected: <span id="selected_member_name">{{ old('user_email') ? 'Selected member' : '' }}</span></p>
                                </div>

                                <script>
                                    function filterMembers(searchTerm) {
                                        searchTerm = searchTerm.toLowerCase();
                                        const selectElement = document.getElementById('user_email');
                                        const resultsContainer = document.getElementById('member_search_results');
                                        const resultsList = resultsContainer.querySelector('ul');

                                        // Clear previous results
                                        resultsList.innerHTML = '';

                                        if (searchTerm.length < 2) {
                                            resultsContainer.classList.add('hidden');
                                            return;
                                        }

                                        // Get all options from the select
                                        const options = selectElement.options;
                                        let matchFound = false;

                                        // Filter options based on search term
                                        for (let i = 1; i < options.length; i++) { // Start from 1 to skip the placeholder
                                            const option = options[i];
                                            const fullname = option.getAttribute('data-fullname');
                                            const email = option.getAttribute('data-email');

                                            if (fullname.includes(searchTerm) || email.includes(searchTerm)) {
                                                matchFound = true;

                                                // Create a list item for each match
                                                const li = document.createElement('li');
                                                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                                                // Highlight the matching text
                                                const displayText = option.textContent;
                                                const lowerDisplayText = displayText.toLowerCase();
                                                let html = '';

                                                if (lowerDisplayText.includes(searchTerm)) {
                                                    const startIndex = lowerDisplayText.indexOf(searchTerm);
                                                    const endIndex = startIndex + searchTerm.length;

                                                    html = displayText.substring(0, startIndex) +
                                                           '<span class="search-highlight">' +
                                                           displayText.substring(startIndex, endIndex) +
                                                           '</span>' +
                                                           displayText.substring(endIndex);
                                                } else {
                                                    html = displayText;
                                                }

                                                li.innerHTML = html;
                                                li.setAttribute('data-value', option.value);

                                                // Add click event to select this option
                                                li.addEventListener('click', function() {
                                                    selectMember(option.value, option.textContent);
                                                });

                                                resultsList.appendChild(li);
                                            }
                                        }

                                        // Show or hide results container
                                        if (matchFound) {
                                            resultsContainer.classList.remove('hidden');
                                        } else {
                                            // Show "No results" message
                                            const li = document.createElement('li');
                                            li.className = 'px-4 py-2 text-gray-500';
                                            li.textContent = 'No matching members found';
                                            resultsList.appendChild(li);
                                            resultsContainer.classList.remove('hidden');
                                        }
                                    }

                                    function selectMember(value, displayText) {
                                        // Set the value in the hidden select
                                        const selectElement = document.getElementById('user_email');
                                        selectElement.value = value;

                                        // Update the search input with the selected name
                                        const searchInput = document.getElementById('user_email_search');
                                        searchInput.value = displayText;

                                        // Update the selected member display
                                        const selectedDisplay = document.getElementById('selected_member_display');
                                        const selectedName = document.getElementById('selected_member_name');
                                        selectedName.textContent = displayText;
                                        selectedDisplay.classList.remove('hidden');

                                        // Hide the results
                                        const resultsContainer = document.getElementById('member_search_results');
                                        resultsContainer.classList.add('hidden');
                                    }

                                    // Close dropdown when clicking outside
                                    document.addEventListener('click', function(event) {
                                        const resultsContainer = document.getElementById('member_search_results');
                                        const searchInput = document.getElementById('user_email_search');

                                        if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
                                            resultsContainer.classList.add('hidden');
                                        }
                                    });

                                    // Initialize selected member if there's a value
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const selectElement = document.getElementById('user_email');
                                        if (selectElement.value) {
                                            const selectedOption = selectElement.options[selectElement.selectedIndex];
                                            if (selectedOption) {
                                                selectMember(selectedOption.value, selectedOption.textContent);
                                            }
                                        }
                                    });
                                </script>

                                @error('user_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('user_email') }}</p>
                                @enderror
                            </div>

                            <!-- Non-ICS Member Fields -->
                            <div id="non_ics_member_fields" style="display: none;">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="non_ics_email" class="block text-sm font-medium text-gray-700 mb-1">NPC Email <span class="text-red-500">*</span></label>
                                        <input type="email" id="non_ics_email" name="non_ics_email" value="{{ old('non_ics_email') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter NPC email">
                                        @error('non_ics_email')
                                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('non_ics_email') }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="non_ics_fullname" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                        <input type="text" id="non_ics_fullname" name="non_ics_fullname" value="{{ old('non_ics_fullname') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter full name">
                                        @error('non_ics_fullname')
                                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('non_ics_fullname') }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="course_year_section" class="block text-sm font-medium text-gray-700 mb-1">Course, Year & Section <span class="text-red-500">*</span></label>
                                        <input type="text" id="course_year_section" name="course_year_section" value="{{ old('course_year_section') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g., BSBA 2-A">
                                        @error('course_year_section')
                                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('course_year_section') }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="non_ics_mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                                        <input type="tel" id="non_ics_mobile" name="non_ics_mobile" value="{{ old('non_ics_mobile') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="09123456789" pattern="[0-9]{11}" maxlength="11">
                                        <p class="mt-1 text-xs text-gray-500">Enter 11-digit mobile number (optional)</p>
                                        @error('non_ics_mobile')
                                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('non_ics_mobile') }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="md:col-span-2">
                            <label for="user_email" class="block text-sm font-medium text-gray-700 mb-1">Payer</label>
                            <input type="text" id="user_email" name="user_email" value="{{ Auth::user()->email }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" readonly>
                            <p class="mt-1 text-sm text-gray-500">You can only record payments for your own account</p>
                        </div>
                        @endif

                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required onchange="handlePaymentMethodChange(this.value)">
                                <option value="">Select Payment Method</option>
                                <option value="CASH" {{ old('payment_method') == 'CASH' ? 'selected' : '' }}>CASH</option>
                                <option value="GCASH" {{ old('payment_method') == 'GCASH' ? 'selected' : '' }}>GCASH</option>
                            </select>
                            <script>
                                function handlePaymentMethodChange(value) {
                                    if (value === 'GCASH') {
                                        document.getElementById('gcash-fields').style.display = 'block';
                                        document.getElementById('cash-fields').style.display = 'none';
                                        document.getElementById('main-officer-field').style.display = 'none';

                                        // Make receipt control number not required for GCASH
                                        const receiptControlNumber = document.getElementById('receipt_control_number');
                                        if (receiptControlNumber) {
                                            receiptControlNumber.required = false;
                                            receiptControlNumber.removeAttribute('required');
                                        }
                                    } else if (value === 'CASH') {
                                        document.getElementById('gcash-fields').style.display = 'none';
                                        document.getElementById('cash-fields').style.display = 'block';
                                        document.getElementById('main-officer-field').style.display = 'none';

                                        // Make receipt control number required for CASH
                                        const receiptControlNumber = document.getElementById('receipt_control_number');
                                        if (receiptControlNumber) {
                                            receiptControlNumber.required = true;
                                            receiptControlNumber.setAttribute('required', 'required');
                                        }
                                    }
                                }

                                // Call the handler on page load
                                document.addEventListener('DOMContentLoaded', function() {
                                    setTimeout(function() {
                                        const paymentMethod = document.getElementById('payment_method');
                                        if (paymentMethod) {
                                            handlePaymentMethodChange(paymentMethod.value);
                                        }
                                    }, 300);
                                });
                            </script>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
                            <select id="purpose" name="purpose" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="">Select Purpose</option>
                                @foreach($paymentFees as $fee)
                                <option value="{{ $fee->purpose }}" {{ old('purpose') == $fee->purpose ? 'selected' : '' }} data-fee-id="{{ $fee->fee_id }}" data-price="{{ $fee->total_price }}">{{ $fee->purpose }}</option>
                                @endforeach
                            </select>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">Amount (₱)</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" step="0.01" min="0" id="total_price" name="total_price" value="{{ old('total_price') }}" class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50" placeholder="0.00" required readonly>
                                <p class="mt-1 text-xs text-gray-500">Amount is automatically set based on the selected purpose.</p>
                            </div>
                            @error('total_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hidden field for fee_id -->
                        <input type="hidden" id="fee_id" name="fee_id" value="{{ old('fee_id') }}">

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status <span class="text-red-500">*</span></label>
                            <select id="payment_status" name="payment_status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="Paid" {{ old('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Pending" {{ old('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Failed" {{ old('payment_status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                                <option value="Refunded" {{ old('payment_status') == 'Refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                            @error('payment_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Receipt Control Number field removed from here -->

                        <!-- Officer in Charge -->
                        <div id="main-officer-field" style="display: none;">
                            <label for="officer_in_charge" class="block text-sm font-medium text-gray-700 mb-1">
                                Officer in Charge <span class="text-red-500">*</span>
                            </label>
                            <select id="officer_in_charge" name="officer_in_charge" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="">Select Officer</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->fullname }}" {{ old('officer_in_charge') == $officer->fullname || (Auth::user()->canManagePayments() && Auth::user()->id == $officer->id) ? 'selected' : '' }}>
                                        {{ $officer->fullname }}
                                    </option>
                                @endforeach
                            </select>
                            @error('officer_in_charge')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Note -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                            <textarea id="description" name="description" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter payment note (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Cash Payment Details -->
                    <div id="cash-fields" class="space-y-6 bg-gray-50 p-6 rounded-lg border border-gray-200" style="display: none;">
                        <h3 class="text-lg font-medium text-gray-900">Cash Payment Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Officer in Charge -->
                            <div>
                                <label for="officer_in_charge" class="block text-sm font-medium text-gray-700 mb-1">
                                    Officer in Charge <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="officer_in_charge" name="officer_in_charge"
                                    value="{{ old('officer_in_charge', Auth::user()->canManagePayments() ? $adminName : '') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Enter officer's name"
                                    {{ Auth::user()->canManagePayments() ? 'readonly' : '' }}>
                                @if(Auth::user()->canManagePayments())
                                    <p class="mt-1 text-sm text-gray-500">This field is automatically filled with your name</p>
                                @endif
                                @error('officer_in_charge')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Receipt Control Number -->
                            <div>
                                <label for="receipt_control_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    Receipt Control Number <span class="text-red-500">*</span>
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 py-2 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                        2-
                                    </span>
                                    <input type="text" id="receipt_control_number" name="receipt_control_number" value="{{ old('receipt_control_number') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-r-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm" placeholder="0001" pattern="[0-9]{4}" maxlength="4">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Enter 4-digit number (e.g., 0001)</p>
                                @error('receipt_control_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Proof of Payment for CASH -->
                            <div class="md:col-span-2">
                                <label for="cash_proof_of_payment" class="block text-sm font-medium text-gray-700 mb-1">
                                    Proof of Payment <span class="text-red-500">*</span>
                                </label>
                                <input type="file" id="cash_proof_of_payment" name="cash_proof_of_payment"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm"
                                    accept=".jpg,.jpeg">
                                <p class="mt-1 text-xs text-gray-500">Only JPG files are accepted</p>
                                @error('cash_proof_of_payment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                        </div>
                    </div>

                    <!-- GCash Specific Fields -->
                    <div id="gcash-fields" class="space-y-6 bg-gray-50 p-6 rounded-lg border border-gray-200" style="display: none;">
                        <h3 class="text-lg font-medium text-gray-900">GCash Payment Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- GCash Name -->
                            <div>
                                <label for="gcash_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    GCash Account Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="gcash_name" name="gcash_name" value="{{ old('gcash_name') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter GCash account name">
                                @error('gcash_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- GCash Number -->
                            <div>
                                <label for="gcash_num" class="block text-sm font-medium text-gray-700 mb-1">
                                    GCash Mobile Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="gcash_num" name="gcash_num" value="{{ old('gcash_num') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="09123456789" pattern="[0-9]{11}" maxlength="11">
                                <p class="mt-1 text-xs text-gray-500">Enter 11-digit mobile number</p>
                                @error('gcash_num')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reference Number -->
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    Reference Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm" placeholder="Enter GCash reference number" pattern="[0-9]{13}" maxlength="13">
                                <p class="mt-1 text-xs text-gray-500">Enter 13-digit reference number</p>
                                @error('reference_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Proof of Payment for GCASH -->
                            <div class="md:col-span-2">
                                <label for="gcash_proof_of_payment" class="block text-sm font-medium text-gray-700 mb-1">
                                    Proof of Payment <span class="text-red-500">*</span>
                                </label>
                                <input type="file" id="gcash_proof_of_payment" name="gcash_proof_of_payment"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm"
                                    accept=".jpg,.jpeg">
                                <p class="mt-1 text-xs text-gray-500">Only JPG files are accepted</p>
                                @error('gcash_proof_of_payment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ Auth::user()->canManagePayments() ? route('admin.payments.index') : route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>

                        <!-- ICS Member Payment Button (default) -->
                        <button type="submit" id="ics-payment-button" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#c21313] transition transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i> Record ICS Payment
                        </button>

                        <!-- Non-ICS Member Payment Button (hidden by default) -->
                        <button type="submit" id="non-ics-payment-button" name="non_ics_payment" value="1" class="hidden inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i> Record Non-ICS Payment
                        </button>
                    </div>

                    <script>
                        // Function to toggle the payment buttons based on payer type
                        function togglePaymentButtons() {
                            const payerType = document.getElementById('payer_type');
                            const icsPaymentButton = document.getElementById('ics-payment-button');
                            const nonIcsPaymentButton = document.getElementById('non-ics-payment-button');

                            if (!payerType || !icsPaymentButton || !nonIcsPaymentButton) {
                                console.error('One or more button elements not found');
                                return;
                            }

                            if (payerType.value === 'ics_member') {
                                icsPaymentButton.classList.remove('hidden');
                                nonIcsPaymentButton.classList.add('hidden');
                                console.log('Showing ICS payment button');
                            } else if (payerType.value === 'non_ics_member') {
                                icsPaymentButton.classList.add('hidden');
                                nonIcsPaymentButton.classList.remove('hidden');
                                console.log('Showing Non-ICS payment button');
                            } else {
                                // If no payer type is selected, show the default button
                                icsPaymentButton.classList.remove('hidden');
                                nonIcsPaymentButton.classList.add('hidden');
                                console.log('No payer type selected, showing default button');
                            }
                        }

                        // Add event listener to payer type select
                        document.addEventListener('DOMContentLoaded', function() {
                            const payerType = document.getElementById('payer_type');
                            if (payerType) {
                                payerType.addEventListener('change', togglePaymentButtons);

                                // Initial toggle
                                setTimeout(function() {
                                    togglePaymentButtons();
                                }, 300);
                            }
                        });
                    </script>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Form submission handler
    function handleFormSubmit(event) {
        // Get the payer type
        const payerType = document.getElementById('payer_type');
        if (!payerType) {
            return true; // Allow form submission
        }

        // Always make sure user_email is not required for non-ICS members
        if (payerType.value === 'non_ics_member') {
            const userEmailSelect = document.getElementById('user_email');
            if (userEmailSelect) {
                userEmailSelect.required = false;
                userEmailSelect.removeAttribute('required');
                userEmailSelect.value = ''; // Clear the value
                console.log('handleFormSubmit: Removed required attribute from user_email for Non-ICS member');
            }
        }

        console.log('Form submission handler - Payer type:', payerType.value);

        // If non-ICS member is selected, ensure the fields are properly set
        if (payerType.value === 'non_ics_member') {
            const nonIcsEmail = document.getElementById('non_ics_email');
            const nonIcsFullname = document.getElementById('non_ics_fullname');
            const courseYearSection = document.getElementById('course_year_section');

            console.log('Non-ICS Member fields:', {
                email: nonIcsEmail ? nonIcsEmail.value : 'not found',
                fullname: nonIcsFullname ? nonIcsFullname.value : 'not found',
                courseYearSection: courseYearSection ? courseYearSection.value : 'not found'
            });

            // Check if the fields are filled
            if (nonIcsEmail && nonIcsFullname && courseYearSection) {
                if (!nonIcsEmail.value.trim() || !nonIcsFullname.value.trim() || !courseYearSection.value.trim()) {
                    alert('Please fill in all required fields for Non-ICS Member');
                    return false; // Prevent form submission
                }

                // Ensure the fields are not required (to bypass HTML5 validation)
                const userEmailSelect = document.getElementById('user_email');
                if (userEmailSelect) {
                    userEmailSelect.required = false;
                    userEmailSelect.removeAttribute('required');
                    userEmailSelect.value = ''; // Clear the value
                }

                // Make sure non-ICS fields are required
                nonIcsEmail.required = true;
                nonIcsEmail.setAttribute('required', 'required');
                nonIcsFullname.required = true;
                nonIcsFullname.setAttribute('required', 'required');
                courseYearSection.required = true;
                courseYearSection.setAttribute('required', 'required');

                console.log('Non-ICS Member validation passed');
            } else {
                console.error('One or more Non-ICS Member fields not found in the DOM');
                alert('Error: One or more Non-ICS Member fields not found. Please try again or contact support.');
                return false;
            }
        } else if (payerType.value === 'ics_member') {
            // Make sure ICS member fields are required
            const userEmailSelect = document.getElementById('user_email');
            if (userEmailSelect) {
                userEmailSelect.required = true;
                userEmailSelect.setAttribute('required', 'required');
            }

            // Make sure non-ICS fields are not required
            const nonIcsEmail = document.getElementById('non_ics_email');
            const nonIcsFullname = document.getElementById('non_ics_fullname');
            const courseYearSection = document.getElementById('course_year_section');

            if (nonIcsEmail) {
                nonIcsEmail.required = false;
                nonIcsEmail.removeAttribute('required');
                nonIcsEmail.value = ''; // Clear the value
            }

            if (nonIcsFullname) {
                nonIcsFullname.required = false;
                nonIcsFullname.removeAttribute('required');
                nonIcsFullname.value = ''; // Clear the value
            }

            if (courseYearSection) {
                courseYearSection.required = false;
                courseYearSection.removeAttribute('required');
                courseYearSection.value = ''; // Clear the value
            }

            console.log('ICS Member validation passed');
        }

        // Check payment method
        const paymentMethod = document.getElementById('payment_method');
        if (paymentMethod && paymentMethod.value === 'GCASH') {
            // Make sure GCash fields are properly set
            const gcashName = document.getElementById('gcash_name');
            const gcashNum = document.getElementById('gcash_num');
            const referenceNumber = document.getElementById('reference_number');
            const gcashProofOfPayment = document.getElementById('gcash_proof_of_payment');

            // Check if the fields are filled
            if (gcashName && !gcashName.value.trim()) {
                alert('Please enter the GCash Account Name');
                gcashName.focus();
                return false;
            }

            if (gcashNum && !gcashNum.value.trim()) {
                alert('Please enter the GCash Mobile Number');
                gcashNum.focus();
                return false;
            }

            if (referenceNumber && !referenceNumber.value.trim()) {
                alert('Please enter the GCash Reference Number');
                referenceNumber.focus();
                return false;
            }

            if (gcashProofOfPayment && (!gcashProofOfPayment.files || gcashProofOfPayment.files.length === 0)) {
                alert('Please upload a Proof of Payment');
                gcashProofOfPayment.focus();
                return false;
            }
        }

        return true; // Allow form submission
    }

    // Function to toggle payer fields based on payer type
    function togglePayerFields() {
        const payerType = document.getElementById('payer_type');
        const icsMemberFields = document.getElementById('ics_member_fields');
        const nonIcsMemberFields = document.getElementById('non_ics_member_fields');
        const userEmailSelect = document.getElementById('user_email');
        const nonIcsEmail = document.getElementById('non_ics_email');
        const nonIcsFullname = document.getElementById('non_ics_fullname');
        const courseYearSection = document.getElementById('course_year_section');

        if (!payerType) {
            console.error('Payer type element not found');
            return;
        }

        console.log('Toggling payer fields for type:', payerType.value);

        // First, hide all payer-specific fields
        if (icsMemberFields) {
            icsMemberFields.style.display = 'none';
            console.log('ICS member fields hidden');
        } else {
            console.error('ICS member fields container not found');
        }

        if (nonIcsMemberFields) {
            nonIcsMemberFields.style.display = 'none';
            console.log('Non-ICS member fields hidden');
        } else {
            console.error('Non-ICS member fields container not found');
        }

        // Then show only the fields for the selected payer type
        if (payerType.value === 'ics_member') {
            // Show ICS Member fields
            if (icsMemberFields) {
                icsMemberFields.style.display = 'block';
                console.log('ICS member fields shown');
            }

            // Make ICS Member fields required
            if (userEmailSelect) {
                userEmailSelect.required = true;
                userEmailSelect.setAttribute('required', 'required');

                // Also handle the search input
                const userEmailSearch = document.getElementById('user_email_search');
                if (userEmailSearch) {
                    userEmailSearch.setAttribute('required', 'required');
                }
                console.log('ICS member email field set as required');
            } else {
                console.error('User email select element not found');
            }

            // Make Non-ICS Member fields not required
            if (nonIcsEmail) {
                nonIcsEmail.required = false;
                nonIcsEmail.removeAttribute('required');
                nonIcsEmail.value = '';
                console.log('Non-ICS email field cleared and set as not required');
            }

            if (nonIcsFullname) {
                nonIcsFullname.required = false;
                nonIcsFullname.removeAttribute('required');
                nonIcsFullname.value = '';
                console.log('Non-ICS fullname field cleared and set as not required');
            }

            if (courseYearSection) {
                courseYearSection.required = false;
                courseYearSection.removeAttribute('required');
                courseYearSection.value = '';
                console.log('Non-ICS course/year/section field cleared and set as not required');
            }
        } else if (payerType.value === 'non_ics_member') {
            // Show Non-ICS Member fields
            if (nonIcsMemberFields) {
                nonIcsMemberFields.style.display = 'block';
                console.log('Non-ICS member fields shown');
            }

            // Make ICS Member fields not required
            if (userEmailSelect) {
                userEmailSelect.required = false;
                userEmailSelect.removeAttribute('required');
                userEmailSelect.value = '';
                console.log('ICS member email field cleared and set as not required');
            }

            // Make Non-ICS Member fields required
            if (nonIcsEmail) {
                nonIcsEmail.required = true;
                nonIcsEmail.setAttribute('required', 'required');
                console.log('Non-ICS email field set as required');
            } else {
                console.error('Non-ICS email element not found');
            }

            if (nonIcsFullname) {
                nonIcsFullname.required = true;
                nonIcsFullname.setAttribute('required', 'required');
                console.log('Non-ICS fullname field set as required');
            } else {
                console.error('Non-ICS fullname element not found');
            }

            if (courseYearSection) {
                courseYearSection.required = true;
                courseYearSection.setAttribute('required', 'required');
                console.log('Non-ICS course/year/section field set as required');
            } else {
                console.error('Non-ICS course/year/section element not found');
            }
        } else {
            console.log('No payer type selected or unknown payer type');

            // Make all fields not required
            if (userEmailSelect) {
                userEmailSelect.required = false;
                userEmailSelect.removeAttribute('required');
                userEmailSelect.value = '';
            }

            if (nonIcsEmail) {
                nonIcsEmail.required = false;
                nonIcsEmail.removeAttribute('required');
                nonIcsEmail.value = '';
            }

            if (nonIcsFullname) {
                nonIcsFullname.required = false;
                nonIcsFullname.removeAttribute('required');
                nonIcsFullname.value = '';
            }

            if (courseYearSection) {
                courseYearSection.required = false;
                courseYearSection.removeAttribute('required');
                courseYearSection.value = '';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize payer fields toggle
        setTimeout(function() {
            const payerType = document.getElementById('payer_type');
            togglePayerFields();
        }, 100);

        // Add form submission handler
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submission event triggered');

                // Explicitly handle user_email field for non-ICS members
                const payerType = document.getElementById('payer_type');
                const userEmailSelect = document.getElementById('user_email');

                if (payerType && payerType.value === 'non_ics_member' && userEmailSelect) {
                    // Make sure user_email is not required for non-ICS members
                    userEmailSelect.required = false;
                    userEmailSelect.removeAttribute('required');
                    userEmailSelect.value = ''; // Clear the value
                    console.log('Explicitly removed required attribute from user_email before form submission');
                }

                const result = handleFormSubmit(e);
                if (result === false) {
                    e.preventDefault();
                    console.log('Form submission prevented by validation');
                } else {
                    console.log('Form validation passed, submitting form');
                }
            });
        } else {
            console.error('Form element not found');
        }

        const paymentMethod = document.getElementById('payment_method');
        const gcashFieldsContainer = document.getElementById('gcash-fields');
        const cashFieldsContainer = document.getElementById('cash-fields');
        const totalPriceInput = document.getElementById('total_price');
        const officerInChargeSelect = document.getElementById('officer_in_charge');
        const receiptControlNumberInput = document.getElementById('receipt_control_number');

        // Function to toggle payment method fields
        function togglePaymentFields() {
            // Get the main officer in charge field by ID
            const mainOfficerField = document.getElementById('main-officer-field');
            // Get the receipt control number field
            const receiptControlField = document.getElementById('receipt-control-field');

            // Hide all payment-specific fields first
            if (gcashFieldsContainer) {
                gcashFieldsContainer.style.display = 'none';
            }
            if (cashFieldsContainer) {
                cashFieldsContainer.style.display = 'none';
            }
            if (mainOfficerField) {
                mainOfficerField.style.display = 'none';
            }
            if (receiptControlField) {
                receiptControlField.style.display = 'none';
            }

            // Then show only the fields for the selected payment method
            if (paymentMethod.value === 'GCASH') {
                console.log('Showing only GCash fields');

                // Show GCash fields
                if (gcashFieldsContainer) {
                    gcashFieldsContainer.style.display = 'block';
                    console.log('GCash fields display set to:', gcashFieldsContainer.style.display);
                    console.log('GCash fields should now be visible'); // Debug log instead of alert
                } else {
                    console.error('GCash fields container not found!');
                    console.error('ERROR: GCash fields container not found!'); // Debug log instead of alert
                }

                // Make GCash fields required
                const gcashFieldIds = ['gcash_name', 'gcash_num', 'reference_number', 'gcash_proof_of_payment'];
                gcashFieldIds.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    console.log('Setting required for field:', fieldId, field);
                    if (field) {
                        field.required = true;
                        field.setAttribute('required', 'required');
                    } else {
                        console.error(`Field with ID ${fieldId} not found!`);
                    }
                });

                // Remove required from cash fields
                if (officerInChargeSelect) {
                    officerInChargeSelect.required = false;
                    officerInChargeSelect.removeAttribute('required');
                }

                if (receiptControlNumberInput) {
                    receiptControlNumberInput.required = false;
                    receiptControlNumberInput.removeAttribute('required');
                    receiptControlNumberInput.value = '';
                }
            } else if (paymentMethod.value === 'CASH') {
                console.log('Showing only Cash fields');

                // Show Cash fields
                if (cashFieldsContainer) {
                    cashFieldsContainer.style.display = 'block';
                    console.log('Cash fields display set to:', cashFieldsContainer.style.display);
                    console.log('Cash fields should now be visible'); // Debug log instead of alert
                } else {
                    console.error('Cash fields container not found!');
                    console.error('ERROR: Cash fields container not found!'); // Debug log instead of alert
                }

                // Show the receipt control number field
                if (receiptControlField) {
                    receiptControlField.style.display = 'block';
                    console.log('Receipt Control field shown');
                } else {
                    console.error('Receipt Control field not found!');
                }

                // Make cash fields required
                if (officerInChargeSelect) {
                    officerInChargeSelect.required = true;
                    officerInChargeSelect.setAttribute('required', 'required');
                }

                if (receiptControlNumberInput) {
                    receiptControlNumberInput.required = true;
                    receiptControlNumberInput.setAttribute('required', 'required');
                }

                // Make cash proof of payment required
                const cashProofOfPayment = document.getElementById('cash_proof_of_payment');
                if (cashProofOfPayment) {
                    cashProofOfPayment.required = true;
                    cashProofOfPayment.setAttribute('required', 'required');
                }

                // Remove required from GCash fields
                const gcashFieldIds = ['gcash_name', 'gcash_num', 'reference_number', 'gcash_proof_of_payment'];
                gcashFieldIds.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.required = false;
                        field.removeAttribute('required');
                        if (fieldId !== 'gcash_proof_of_payment') {
                            field.value = '';
                        }
                    }
                });
            } else {
                console.log('No payment method selected, hiding all payment-specific fields');

                // Remove required from all fields
                const gcashFieldIds = ['gcash_name', 'gcash_num', 'reference_number', 'gcash_proof_of_payment'];
                gcashFieldIds.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.required = false;
                        field.removeAttribute('required');
                        if (fieldId !== 'gcash_proof_of_payment') {
                            field.value = '';
                        }
                    }
                });

                if (officerInChargeSelect) {
                    officerInChargeSelect.required = false;
                    officerInChargeSelect.removeAttribute('required');
                }

                if (receiptControlNumberInput) {
                    receiptControlNumberInput.required = false;
                    receiptControlNumberInput.removeAttribute('required');
                }
            }

            // Debug display state after toggle
            if (gcashFieldsContainer) {
                console.log('GCash container display after toggle:', gcashFieldsContainer.style.display);
            }
            if (cashFieldsContainer) {
                console.log('Cash container display after toggle:', cashFieldsContainer.style.display);
            }
        }

        // Function to validate GCash amount
        // GCash amount validation function removed since we no longer have the GCash amount field

        // Function to sync receipt control number fields - no longer needed since we only have one field
        function syncReceiptControlNumbers() {
            // This function is now empty as we only have one receipt control number field
        }

        // Initial check
        console.log('Running initial check');
        // Set a small delay for the initial toggle to ensure DOM is fully loaded
        setTimeout(function() {
            togglePaymentFields();
            syncReceiptControlNumbers();
            console.log('Initial toggle completed');
        }, 100);

        // Event listeners
        paymentMethod.addEventListener('change', function(e) {
            console.log('Payment method change event triggered');
            console.log('Selected payment method:', paymentMethod.value);
            console.log('Payment method changed to: ' + paymentMethod.value); // Debug log instead of alert
            togglePaymentFields();
            syncReceiptControlNumbers();
        });

        // Add input event listener as a backup
        paymentMethod.addEventListener('input', function(e) {
            console.log('Payment method input event triggered');
            togglePaymentFields();
            syncReceiptControlNumbers();
        });

        // Add click event listener to test
        paymentMethod.addEventListener('click', function(e) {
            console.log('Payment method clicked');
            // Force toggle on click as well
            setTimeout(function() {
                togglePaymentFields();
                syncReceiptControlNumbers();
            }, 50);
        });

        // Add event listener for payer type change
        const payerTypeSelect = document.getElementById('payer_type');
        if (payerTypeSelect) {
            payerTypeSelect.addEventListener('change', function() {
                togglePayerFields();
            });
        }

        // Add form validation before submission
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            console.log('Form submission attempted, payment method:', paymentMethod.value);

            // Validate payer fields
            const payerType = document.getElementById('payer_type');
            if (payerType) {
                console.log('Validating payer type:', payerType.value);

                if (!payerType.value) {
                    e.preventDefault();
                    alert('Please select a Payer Type');
                    payerType.focus();
                    return;
                }

                if (payerType.value === 'ics_member') {
                    const userEmailSelect = document.getElementById('user_email');
                    const userEmailSearch = document.getElementById('user_email_search');

                    console.log('Validating ICS Member selection:', userEmailSelect ? userEmailSelect.value : 'not found');

                    if (!userEmailSelect || !userEmailSelect.value) {
                        e.preventDefault();
                        alert('Please select an ICS Member');
                        if (userEmailSearch) userEmailSearch.focus();
                        return;
                    }
                } else if (payerType.value === 'non_ics_member') {
                    const nonIcsEmail = document.getElementById('non_ics_email');
                    const courseYearSection = document.getElementById('course_year_section');

                    console.log('Validating Non-ICS Member fields:');
                    console.log('- Email:', nonIcsEmail ? nonIcsEmail.value : 'not found');
                    console.log('- Course/Year/Section:', courseYearSection ? courseYearSection.value : 'not found');

                    if (!nonIcsEmail || !nonIcsEmail.value.trim()) {
                        e.preventDefault();
                        alert('Please enter the NPC Email');
                        if (nonIcsEmail) nonIcsEmail.focus();
                        return;
                    }

                    if (!courseYearSection || !courseYearSection.value.trim()) {
                        e.preventDefault();
                        alert('Please enter the Course, Year & Section');
                        if (courseYearSection) courseYearSection.focus();
                        return;
                    }
                }
            }

            // Sync receipt control numbers before submission
            syncReceiptControlNumbers();

            if (paymentMethod.value === 'CASH') {
                if (!officerInChargeSelect || !officerInChargeSelect.value) {
                    e.preventDefault();
                    alert('Please select an Officer in Charge');
                    if (officerInChargeSelect) officerInChargeSelect.focus();
                    return;
                }
                // Check receipt control number input - only for CASH payments
                if (!receiptControlNumberInput || !receiptControlNumberInput.value) {
                    e.preventDefault();
                    alert('Please enter the Receipt Control Number');
                    if (receiptControlNumberInput) {
                        receiptControlNumberInput.focus();
                    }
                    return;
                }

                // Validate receipt control number format (4 digits)
                if (!/^\d{4}$/.test(receiptControlNumberInput.value)) {
                    e.preventDefault();
                    alert('Receipt Control Number must be exactly 4 digits (e.g., 0001)');
                    receiptControlNumberInput.focus();
                    return;
                }

                // Check cash proof of payment
                const cashProofOfPayment = document.getElementById('cash_proof_of_payment');
                if (!cashProofOfPayment || !cashProofOfPayment.files || cashProofOfPayment.files.length === 0) {
                    e.preventDefault();
                    alert('Please upload a Proof of Payment');
                    if (cashProofOfPayment) cashProofOfPayment.focus();
                    return;
                }

            } else if (paymentMethod.value === 'GCASH') {
                // Validate GCASH fields
                const gcashName = document.getElementById('gcash_name');
                const gcashNum = document.getElementById('gcash_num');
                const referenceNumber = document.getElementById('reference_number');

                if (!gcashName || !gcashName.value.trim()) {
                    e.preventDefault();
                    alert('Please enter the GCash Account Name');
                    if (gcashName) gcashName.focus();
                    return;
                }

                if (!gcashNum || !gcashNum.value.trim()) {
                    e.preventDefault();
                    alert('Please enter the GCash Mobile Number');
                    if (gcashNum) gcashNum.focus();
                    return;
                }

                if (!referenceNumber || !referenceNumber.value.trim()) {
                    e.preventDefault();
                    alert('Please enter the GCash Reference Number');
                    if (referenceNumber) referenceNumber.focus();
                    return;
                }

                // Check gcash proof of payment
                const gcashProofOfPayment = document.getElementById('gcash_proof_of_payment');
                if (!gcashProofOfPayment || !gcashProofOfPayment.files || gcashProofOfPayment.files.length === 0) {
                    e.preventDefault();
                    alert('Please upload a Proof of Payment');
                    if (gcashProofOfPayment) gcashProofOfPayment.focus();
                    return;
                }

                console.log('GCASH validation passed');
            }
        });
    });

    // Direct approach to set display styles
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            // Handle payment method fields
            const paymentMethod = document.getElementById('payment_method');
            if (paymentMethod && paymentMethod.value === 'GCASH') {
                document.getElementById('gcash-fields').style.display = 'block';
                document.getElementById('cash-fields').style.display = 'none';
                document.getElementById('main-officer-field').style.display = 'none';
                console.log('Direct approach: GCASH fields should be visible');
            } else if (paymentMethod && paymentMethod.value === 'CASH') {
                document.getElementById('gcash-fields').style.display = 'none';
                document.getElementById('cash-fields').style.display = 'block';
                document.getElementById('main-officer-field').style.display = 'none';
                console.log('Direct approach: CASH fields should be visible');
            }

            // Handle payer type fields
            const payerType = document.getElementById('payer_type');
            if (payerType) {
                const icsMemberFields = document.getElementById('ics_member_fields');
                const nonIcsMemberFields = document.getElementById('non_ics_member_fields');

                if (payerType.value === 'ics_member') {
                    if (icsMemberFields) icsMemberFields.style.display = 'block';
                    if (nonIcsMemberFields) nonIcsMemberFields.style.display = 'none';
                    console.log('Direct approach: ICS Member fields should be visible');
                } else if (payerType.value === 'non_ics_member') {
                    if (icsMemberFields) icsMemberFields.style.display = 'none';
                    if (nonIcsMemberFields) nonIcsMemberFields.style.display = 'block';
                    console.log('Direct approach: Non-ICS Member fields should be visible');
                } else {
                    if (icsMemberFields) icsMemberFields.style.display = 'none';
                    if (nonIcsMemberFields) nonIcsMemberFields.style.display = 'none';
                    console.log('Direct approach: No payer type selected, hiding all payer fields');
                }
            }
        }, 500);
    });

    // Additional script to ensure fields are properly displayed on window load
    window.addEventListener('load', function() {
        console.log('Window loaded - checking all fields');

        // Check payment method fields
        const paymentMethod = document.getElementById('payment_method');
        const gcashFieldsContainer = document.getElementById('gcash-fields');
        const cashFieldsContainer = document.getElementById('cash-fields');
        const mainOfficerField = document.getElementById('main-officer-field');

        console.log('Window load - Payment method value:', paymentMethod ? paymentMethod.value : 'not found');
        console.log('Window load - GCash fields container:', gcashFieldsContainer ? 'found' : 'not found');
        console.log('Window load - Cash fields container:', cashFieldsContainer ? 'found' : 'not found');
        console.log('Window load - Main Officer field found:', mainOfficerField ? 'found' : 'not found');

        // Handle payment method fields
        if (paymentMethod && paymentMethod.value === 'GCASH') {
            if (gcashFieldsContainer) gcashFieldsContainer.style.display = 'block';
            if (cashFieldsContainer) cashFieldsContainer.style.display = 'none';
            if (mainOfficerField) mainOfficerField.style.display = 'none';
        } else if (paymentMethod && paymentMethod.value === 'CASH') {
            if (gcashFieldsContainer) gcashFieldsContainer.style.display = 'none';
            if (cashFieldsContainer) cashFieldsContainer.style.display = 'block';
            if (mainOfficerField) mainOfficerField.style.display = 'none';
        } else {
            if (gcashFieldsContainer) gcashFieldsContainer.style.display = 'none';
            if (cashFieldsContainer) cashFieldsContainer.style.display = 'none';
            if (mainOfficerField) mainOfficerField.style.display = 'none';
        }

        // Check payer type fields
        const payerType = document.getElementById('payer_type');
        const icsMemberFields = document.getElementById('ics_member_fields');
        const nonIcsMemberFields = document.getElementById('non_ics_member_fields');

        console.log('Window load - Payer type value:', payerType ? payerType.value : 'not found');
        console.log('Window load - ICS Member fields container:', icsMemberFields ? 'found' : 'not found');
        console.log('Window load - Non-ICS Member fields container:', nonIcsMemberFields ? 'found' : 'not found');

        // Handle payer type fields
        if (payerType && payerType.value === 'ics_member') {
            if (icsMemberFields) icsMemberFields.style.display = 'block';
            if (nonIcsMemberFields) nonIcsMemberFields.style.display = 'none';
        } else if (payerType && payerType.value === 'non_ics_member') {
            if (icsMemberFields) icsMemberFields.style.display = 'none';
            if (nonIcsMemberFields) nonIcsMemberFields.style.display = 'block';
        } else {
            if (icsMemberFields) icsMemberFields.style.display = 'none';
            if (nonIcsMemberFields) nonIcsMemberFields.style.display = 'none';
        }

        // Then show only the fields for the selected payment method
        if (paymentMethod && paymentMethod.value === 'GCASH' && gcashFieldsContainer) {
            console.log('Setting GCASH fields visible on window load');
            gcashFieldsContainer.style.display = 'block';
            console.log('GCash fields display set to:', gcashFieldsContainer.style.display);
            console.log('Window load: GCash fields should now be visible'); // Debug log instead of alert
        } else if (paymentMethod && paymentMethod.value === 'CASH' && cashFieldsContainer) {
            console.log('Setting CASH fields visible on window load');
            cashFieldsContainer.style.display = 'block';
            console.log('Cash fields display set to:', cashFieldsContainer.style.display);
            console.log('Window load: Cash fields should now be visible'); // Debug log instead of alert
        } else {
            console.log('No valid payment method selected on window load');
            console.log('Window load: No valid payment method selected'); // Debug log instead of alert
        }
    });
</script>

<!-- Inline script as a last resort -->
<script>
    // This script will run immediately when the page loads
    setTimeout(function() {
        const paymentMethod = document.getElementById('payment_method');
        if (paymentMethod && paymentMethod.value === 'GCASH') {
            document.getElementById('gcash-fields').style.display = 'block';
            document.getElementById('cash-fields').style.display = 'none';
            document.getElementById('main-officer-field').style.display = 'none';
            console.log('Inline script: GCASH fields should be visible');
        } else if (paymentMethod && paymentMethod.value === 'CASH') {
            document.getElementById('gcash-fields').style.display = 'none';
            document.getElementById('cash-fields').style.display = 'block';
            document.getElementById('main-officer-field').style.display = 'none';
            console.log('Inline script: CASH fields should be visible');
        }
    }, 1000);
</script>

<!-- Payment Fee Lookup Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const purposeSelect = document.getElementById('purpose');
        const totalPriceInput = document.getElementById('total_price');
        const feeIdInput = document.getElementById('fee_id');

        // Function to set payment fee from selected option
        function setPaymentFeeFromOption(selectElement) {
            if (!selectElement) return;

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                totalPriceInput.value = '';
                feeIdInput.value = '';
                totalPriceInput.readOnly = false;
                return;
            }

            const feeId = selectedOption.getAttribute('data-fee-id');
            const price = selectedOption.getAttribute('data-price');
            const purpose = selectedOption.value;

            if (feeId && price) {
                totalPriceInput.value = price;
                feeIdInput.value = feeId;

                // If purpose is "Other", allow manual price entry and remove bg-gray-50 class
                if (purpose === 'Other') {
                    totalPriceInput.readOnly = false;
                    totalPriceInput.classList.remove('bg-gray-50');
                    totalPriceInput.value = ''; // Clear the value for manual entry
                } else {
                    totalPriceInput.readOnly = true;
                    totalPriceInput.classList.add('bg-gray-50');
                }
            } else {
                console.error('Selected option does not have fee data');
                totalPriceInput.value = '';
                feeIdInput.value = '';

                // If no data, allow manual entry
                totalPriceInput.readOnly = false;
                totalPriceInput.classList.remove('bg-gray-50');
            }
        }

        // If purpose is already selected (e.g., from old input), set the fee
        if (purposeSelect && purposeSelect.value) {
            setPaymentFeeFromOption(purposeSelect);
        }

        // Listen for changes to the purpose select
        if (purposeSelect) {
            purposeSelect.addEventListener('change', function() {
                setPaymentFeeFromOption(this);
            });
        }
    });
</script>
@endsection