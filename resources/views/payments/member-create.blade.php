@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Submit New Payment</h1>
                        <p class="text-gray-600 mt-1">Enter your payment details</p>
                    </div>
                    <div>
                        <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Payments
                        </a>
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

                <form method="POST" action="{{ route('client.payments.store') }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Member Information (Read-only) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                            <input type="text" value="{{ $memberName }} ({{ $user->email }})" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" readonly>
                            <p class="mt-1 text-sm text-gray-500">This payment will be recorded for your account</p>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                            <select id="payment_method" name="payment_method" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm" required onchange="handlePaymentMethodChange(this.value)">
                                <option value="">Select Payment Method</option>
                                <option value="CASH" {{ old('payment_method') == 'CASH' ? 'selected' : '' }}>CASH</option>
                                <option value="GCASH" {{ old('payment_method') == 'GCASH' ? 'selected' : '' }}>GCASH</option>
                            </select>
                            <script>
                                function handlePaymentMethodChange(value) {
                                    console.log('Payment method changed to:', value);
                                    if (value === 'GCASH') {
                                        document.getElementById('gcash-fields').style.display = 'block';
                                        document.getElementById('cash-fields').style.display = 'none';
                                    } else if (value === 'CASH') {
                                        document.getElementById('gcash-fields').style.display = 'none';
                                        document.getElementById('cash-fields').style.display = 'block';
                                    } else {
                                        document.getElementById('gcash-fields').style.display = 'none';
                                        document.getElementById('cash-fields').style.display = 'none';
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
                            <select id="purpose" name="purpose" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm" required onchange="if(typeof updatePrice === 'function') updatePrice();">
                                <option value="">Select Purpose</option>
                                @foreach($paymentFees as $fee)
                                <option value="{{ $fee->purpose }}" {{ old('purpose') == $fee->purpose ? 'selected' : '' }} data-fee-id="{{ $fee->fee_id }}" data-price="{{ $fee->total_price }}">{{ $fee->purpose }}</option>
                                @endforeach
                            </select>
                            <script>
                                // Immediate script to update price on page load
                                document.addEventListener('DOMContentLoaded', function() {
                                    setTimeout(function() {
                                        if (typeof updatePrice === 'function') {
                                            console.log('Calling updatePrice from inline script');
                                            updatePrice();
                                        }
                                    }, 100);
                                });
                            </script>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">Amount (₱) <span class="text-red-500">*</span></label>
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

                        <!-- Payment Status Information -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <input type="text" value="Pending" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" readonly>
                            <p class="mt-1 text-sm text-gray-500">Your payment will be reviewed by an administrator</p>
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
                                    value="{{ old('officer_in_charge') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Enter officer's name">
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
                        <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#c21313] transition transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i> Submit Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Global function to update price based on selected purpose
    function updatePrice() {
        const purposeSelect = document.getElementById('purpose');
        const totalPriceInput = document.getElementById('total_price');
        const feeIdInput = document.getElementById('fee_id');

        if (!purposeSelect || !totalPriceInput) return;

        const selectedOption = purposeSelect.options[purposeSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            totalPriceInput.value = '';
            if (feeIdInput) feeIdInput.value = '';
            return;
        }

        const purpose = selectedOption.value;
        const price = selectedOption.getAttribute('data-price');
        const feeId = selectedOption.getAttribute('data-fee-id');

        console.log('Global updatePrice called');
        console.log('Selected purpose:', purpose);
        console.log('Price attribute:', price);

        if (purpose === 'Other') {
            totalPriceInput.readOnly = false;
            totalPriceInput.classList.remove('bg-gray-50');
            totalPriceInput.value = '';
            if (feeIdInput) feeIdInput.value = feeId || '';
        } else if (price) {
            totalPriceInput.value = price;
            totalPriceInput.readOnly = true;
            totalPriceInput.classList.add('bg-gray-50');
            if (feeIdInput) feeIdInput.value = feeId || '';
            console.log('Price set to:', price);
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethod = document.getElementById('payment_method');
        const gcashFieldsContainer = document.getElementById('gcash-fields');
        const cashFieldsContainer = document.getElementById('cash-fields');
        const totalPriceInput = document.getElementById('total_price');
        const receiptControlNumberInput = document.getElementById('receipt_control_number');

        // Get the purpose select element
        const purposeSelect = document.getElementById('purpose');

        // Function to toggle payment method fields
        function togglePaymentFields() {
            // Hide all payment-specific fields first
            if (gcashFieldsContainer) {
                gcashFieldsContainer.style.display = 'none';
            }
            if (cashFieldsContainer) {
                cashFieldsContainer.style.display = 'none';
            }

            // Then show only the fields for the selected payment method
            if (paymentMethod.value === 'GCASH') {
                // Show GCash fields
                if (gcashFieldsContainer) {
                    gcashFieldsContainer.style.display = 'block';
                }

                // Make GCash fields required
                const gcashFieldIds = ['gcash_name', 'gcash_num', 'reference_number', 'gcash_proof_of_payment'];
                gcashFieldIds.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.required = true;
                        field.setAttribute('required', 'required');
                    }
                });

                // Remove required from cash fields
                if (receiptControlNumberInput) {
                    receiptControlNumberInput.required = false;
                    receiptControlNumberInput.removeAttribute('required');
                    receiptControlNumberInput.value = '';
                }

                const cashProofOfPayment = document.getElementById('cash_proof_of_payment');
                if (cashProofOfPayment) {
                    cashProofOfPayment.required = false;
                    cashProofOfPayment.removeAttribute('required');
                }
            } else if (paymentMethod.value === 'CASH') {
                // Show Cash fields
                if (cashFieldsContainer) {
                    cashFieldsContainer.style.display = 'block';
                }

                // Make cash fields required
                if (receiptControlNumberInput) {
                    receiptControlNumberInput.required = true;
                    receiptControlNumberInput.setAttribute('required', 'required');
                }

                // Make officer in charge required
                const officerInChargeInput = document.getElementById('officer_in_charge');
                if (officerInChargeInput) {
                    officerInChargeInput.required = true;
                    officerInChargeInput.setAttribute('required', 'required');
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
                        field.value = '';
                    }
                });
            }
        }



        // Initial check
        setTimeout(function() {
            togglePaymentFields();
        }, 100);

        // Event listeners
        paymentMethod.addEventListener('change', function() {
            togglePaymentFields();
        });

        // Add form validation before submission
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            if (paymentMethod.value === 'CASH') {
                // Check officer in charge - only for CASH payments
                const officerInChargeInput = document.getElementById('officer_in_charge');
                if (!officerInChargeInput || !officerInChargeInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter the Officer in Charge');
                    if (officerInChargeInput) {
                        officerInChargeInput.focus();
                    }
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




            }
        });

        // Direct approach for payment fee handling
        const feeIdInput = document.getElementById('fee_id');

        // Function to update price based on selected purpose
        function updatePrice() {
            if (!purposeSelect) return;

            const selectedOption = purposeSelect.options[purposeSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                totalPriceInput.value = '';
                return;
            }

            const purpose = selectedOption.value;
            const price = selectedOption.getAttribute('data-price');
            const feeId = selectedOption.getAttribute('data-fee-id');

            // For debugging
            console.log('Selected purpose:', purpose);
            console.log('Price attribute:', price);
            console.log('Fee ID attribute:', feeId);

            if (purpose === 'Other') {
                // For "Other" purpose, allow manual entry
                totalPriceInput.readOnly = false;
                totalPriceInput.classList.remove('bg-gray-50');
                totalPriceInput.value = '';
                if (feeIdInput) feeIdInput.value = feeId || '';
            } else if (price) {
                // Set price from data attribute
                totalPriceInput.value = price;
                totalPriceInput.readOnly = true;
                totalPriceInput.classList.add('bg-gray-50');
                if (feeIdInput) feeIdInput.value = feeId || '';

                console.log('Price set to:', price);
            } else {
                // If no price attribute, try to get it from the server
                console.log('No price attribute, fetching from server...');

                fetch(`/omcms/payments/fees/by-purpose?purpose=${encodeURIComponent(purpose)}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Server response:', data);

                        if (data.success && data.data) {
                            totalPriceInput.value = data.data.total_price;
                            totalPriceInput.readOnly = true;
                            totalPriceInput.classList.add('bg-gray-50');
                            if (feeIdInput) feeIdInput.value = data.data.fee_id || '';

                            console.log('Price set from server to:', data.data.total_price);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching price:', error);
                    });
            }
        }

        // Add change event listener to purpose select
        if (purposeSelect) {
            purposeSelect.addEventListener('change', updatePrice);

            // Also update price on page load
            if (purposeSelect.value) {
                console.log('Purpose already selected on page load:', purposeSelect.value);
                updatePrice();
            }

            // Force a change event to initialize the price
            setTimeout(() => {
                console.log('Forcing price update...');
                updatePrice();
            }, 500);
        } else {
            console.error('Purpose select element not found!');
        }
    });
</script>
@endsection
