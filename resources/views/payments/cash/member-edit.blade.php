@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit Cash Payment</h1>
                        <p class="text-gray-600 mt-1">Update your cash payment details</p>
                    </div>
                    <div>
                        <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
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

                <form method="POST" action="{{ route('client.cash-payments.update', $payment->id) }}" class="space-y-6" enctype="multipart/form-data">
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
                            <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
                            <select id="purpose" name="purpose" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="">Select Purpose</option>
                                <option value="Membership Fee" {{ old('purpose', $payment->purpose) == 'Membership Fee' ? 'selected' : '' }}>Membership Fee</option>
                                <option value="Event Fees" {{ old('purpose', $payment->purpose) == 'Event Fees' ? 'selected' : '' }}>Event Fees</option>
                                <option value="ICS Merch" {{ old('purpose', $payment->purpose) == 'ICS Merch' ? 'selected' : '' }}>ICS Merch</option>
                                <option value="Other" {{ old('purpose', $payment->purpose) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
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
                                <input type="number" step="0.01" min="0" id="total_price" name="total_price" value="{{ old('total_price', $payment->total_price) }}" class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50" placeholder="0.00" required readonly>
                                <p class="mt-1 text-xs text-gray-500">Amount is automatically set based on the selected purpose.</p>
                            </div>
                            @error('total_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Status Information -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <input type="text" value="{{ $payment->payment_status }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" readonly>
                            <p class="mt-1 text-sm text-gray-500">Your payment will be reviewed by an administrator</p>
                        </div>

                        <!-- Officer in Charge -->
                        <div>
                            <label for="officer_in_charge" class="block text-sm font-medium text-gray-700 mb-1">
                                Officer in Charge <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="officer_in_charge" name="officer_in_charge"
                                value="{{ old('officer_in_charge', $payment->officer_in_charge) }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Enter officer's name" required>
                            @error('officer_in_charge')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Receipt Control Number -->
                        <div>
                            <label for="receipt_control_number" class="block text-sm font-medium text-gray-700 mb-1">
                                Receipt Control Number <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    2-
                                </span>
                                <input type="text" id="receipt_control_number" name="receipt_control_number"
                                    value="{{ old('receipt_control_number', str_replace('2-', '', $payment->receipt_control_number)) }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-r-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="0000" pattern="[0-9]{4}" required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter 4 digits (e.g., 0001)</p>
                            @error('receipt_control_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter payment description (optional)">{{ old('description', $payment->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Proof of Payment -->
                        <div class="md:col-span-2">
                            <label for="cash_proof_of_payment" class="block text-sm font-medium text-gray-700 mb-1">
                                Proof of Payment <span class="text-red-500">*</span>
                            </label>
                            @if($payment->cash_proof_path)
                                <div class="mb-2">
                                    <p class="text-sm text-gray-600">Current proof of payment:</p>
                                    @php
                                        $proofPath = $payment->cash_proof_path;
                                        // Check if it's a base64 file
                                        if (strpos($proofPath, 'base64/') === 0 && file_exists(public_path($proofPath))) {
                                            $base64Content = file_get_contents(public_path($proofPath));
                                            $viewUrl = $base64Content;
                                        } else {
                                            $viewUrl = asset($proofPath);
                                        }
                                    @endphp
                                    <a href="{{ $viewUrl }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                        <i class="fas fa-file-image mr-1"></i> View current proof of payment
                                    </a>
                                    <div class="mt-2 border border-gray-200 rounded-lg overflow-hidden" style="max-width: 300px;">
                                        <img src="{{ $viewUrl }}" alt="Cash Payment Proof" class="w-full h-auto">
                                    </div>
                                </div>
                            @endif
                            <input type="file" id="cash_proof_of_payment" name="cash_proof_of_payment"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                accept=".jpg,.jpeg">
                            <p class="mt-1 text-xs text-gray-500">Only JPG files are accepted. Leave empty to keep the current file.</p>
                            @error('cash_proof_of_payment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition transform hover:scale-105">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Handle purpose selection to update total price
        const purposeSelect = document.getElementById('purpose');
        const totalPriceInput = document.getElementById('total_price');

        // Function to update total price based on purpose
        function updateTotalPrice() {
            const purpose = purposeSelect.value;

            // Set default prices based on purpose
            switch(purpose) {
                case 'Membership Fee':
                    totalPriceInput.value = '300.00';
                    break;
                case 'Event Fees':
                    totalPriceInput.value = '150.00';
                    break;
                case 'ICS Merch':
                    totalPriceInput.value = '250.00';
                    break;
                case 'Other':
                    totalPriceInput.value = '0.00';
                    break;
                default:
                    totalPriceInput.value = '0.00';
            }
        }

        // Add event listener to purpose select
        purposeSelect.addEventListener('change', updateTotalPrice);

        // Initialize total price on page load
        updateTotalPrice();
    });
</script>
@endsection
