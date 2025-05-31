@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit GCash Payment</h1>
                        <p class="text-gray-600 mt-1">Update your GCash payment details</p>
                    </div>
                    <div>
                        <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Payments
                        </a>
                    </div>
                </div>

                @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('client.gcash-payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Member Information (Read-only) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                            <input type="text" value="{{ $memberName }} ({{ $user->email }})" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" readonly>
                            <p class="mt-1 text-sm text-gray-500">This payment is recorded for your account</p>
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">
                                Purpose <span class="text-red-500">*</span>
                            </label>
                            <select id="purpose" name="purpose" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required onchange="if(typeof updatePrice === 'function') updatePrice();">
                                <option value="">Select Purpose</option>
                                @foreach($paymentFees as $fee)
                                <option value="{{ $fee->purpose }}" {{ old('purpose', $payment->purpose) == $fee->purpose ? 'selected' : '' }} data-fee-id="{{ $fee->fee_id }}" data-price="{{ $fee->total_price }}">{{ $fee->purpose }}</option>
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

                        <!-- Total Price -->
                        <div>
                            <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">
                                Payment Amount (PHP) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="total_price" name="total_price" value="{{ old('total_price', $payment->total_price) }}" min="1" step="0.01" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50" placeholder="0.00" required readonly>
                            <p class="mt-1 text-xs text-gray-500">Amount is automatically set based on the selected purpose.</p>
                            @error('total_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- GCash Name -->
                        <div>
                            <label for="gcash_name" class="block text-sm font-medium text-gray-700 mb-1">
                                GCash Account Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="gcash_name" name="gcash_name" value="{{ old('gcash_name', $payment->gcash_name) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter GCash account name" required>
                            @error('gcash_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- GCash Number -->
                        <div>
                            <label for="gcash_num" class="block text-sm font-medium text-gray-700 mb-1">
                                GCash Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="gcash_num" name="gcash_num" value="{{ old('gcash_num', $payment->gcash_num) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="09123456789" pattern="[0-9]{11}" maxlength="11" required>
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
                            <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number', $payment->reference_number) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter GCash reference number" pattern="[0-9]{13}" maxlength="13" required>
                            <p class="mt-1 text-xs text-gray-500">Enter 13-digit reference number</p>
                            @error('reference_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Note -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Note
                            </label>
                            <textarea id="description" name="description" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Additional details about this payment">{{ old('description', $payment->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- GCash Proof of Payment -->
                        <div class="md:col-span-2">
                            <label for="gcash_proof_of_payment" class="block text-sm font-medium text-gray-700 mb-1">
                                GCash Proof of Payment (JPG only)
                            </label>

                            @if($payment->gcash_proof_path)
                            <div class="mb-2">
                                <p class="text-sm text-gray-600">Current proof of payment:</p>
                                @php
                                    $proofPath = $payment->gcash_proof_path;
                                    // Check if it's a base64 file
                                    if (strpos($proofPath, 'base64/') === 0 && file_exists(public_path($proofPath))) {
                                        $base64Content = file_get_contents(public_path($proofPath));
                                        $viewUrl = $base64Content;
                                    } else {
                                        $viewUrl = asset($proofPath);
                                    }
                                @endphp
                                <img src="{{ $viewUrl }}" alt="GCash Proof" class="mt-2 max-w-xs rounded-lg border border-gray-200">
                            </div>
                            @endif

                            <input type="file" id="gcash_proof_of_payment" name="gcash_proof_of_payment" accept=".jpg,.jpeg" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Upload a new image to replace the current one (JPG format only, max 2MB)</p>
                            @error('gcash_proof_of_payment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                        <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-[#a11010] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i> Update Payment
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

        if (!purposeSelect || !totalPriceInput) return;

        const selectedOption = purposeSelect.options[purposeSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            totalPriceInput.value = '';
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
            // Don't clear the value for existing payments
        } else if (price) {
            totalPriceInput.value = price;
            totalPriceInput.readOnly = true;
            totalPriceInput.classList.add('bg-gray-50');
            console.log('Price set to:', price);
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const purposeSelect = document.getElementById('purpose');
        const totalPriceInput = document.getElementById('total_price');

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
                // Don't clear the value for existing payments
            } else if (price) {
                // Set price from data attribute
                totalPriceInput.value = price;
                totalPriceInput.readOnly = true;
                totalPriceInput.classList.add('bg-gray-50');

                console.log('Price set to:', price);
            } else {
                // If no price attribute, try to get it from the server
                console.log('No price attribute, fetching from server...');

                fetch(`/omcms/payments/fees/by-purpose?purpose=${encodeURIComponent(purpose)}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Server response:', data);

                        if (data.success && data.data) {
                            if (purpose !== 'Other') {
                                totalPriceInput.value = data.data.total_price;
                                totalPriceInput.readOnly = true;
                                totalPriceInput.classList.add('bg-gray-50');

                                console.log('Price set from server to:', data.data.total_price);
                            } else {
                                totalPriceInput.readOnly = false;
                                totalPriceInput.classList.remove('bg-gray-50');
                            }
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
