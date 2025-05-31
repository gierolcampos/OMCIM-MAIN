@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit Payment</h1>
                        <p class="text-gray-600 mt-1">Modify payment details</p>
                    </div>
                    <div>
                        @if(auth()->user()->canManagePayments())
                            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Payments
                            </a>
                        @else
                            <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-arrow-left mr-2"></i> Back to My Payments
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

                <form method="POST" action="{{ auth()->user()->canManagePayments() ? route('admin.payments.update', $payment->id) : route('client.payments.update', $payment->id) }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                        <!-- Transaction Details -->
                        <div class="px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Transaction ID</p>
                                    <p class="mt-1 text-sm text-gray-900">#{{ $payment->id }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Payment Status</p>
                                    <select id="payment_status" name="payment_status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="Paid" {{ $payment->payment_status == 'Paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="Pending" {{ $payment->payment_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Failed" {{ $payment->payment_status == 'Failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="Refunded" {{ $payment->payment_status == 'Refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                    @error('payment_status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Payment Method</p>
                                    <select id="payment_method" name="payment_method" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="CASH" {{ $payment->method == 'CASH' ? 'selected' : '' }}>CASH</option>
                                        <option value="GCASH" {{ $payment->method == 'GCASH' ? 'selected' : '' }}>GCASH</option>
                                    </select>
                                    @error('payment_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date & Time</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($payment->placed_on)->format('M d, Y h:i A') }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Purpose</p>
                                    <input type="text" id="purpose" name="purpose" value="{{ old('purpose', $payment->purpose) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter purpose">
                                    @error('purpose')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Amount</p>
                                    <div class="relative mt-1 rounded-lg shadow-sm">
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

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Member</p>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if(isset($payment->user) && $payment->user)
                                            {{ $payment->user->firstname }} {{ $payment->user->lastname }}
                                        @elseif(get_class($payment) === 'App\Models\NonIcsMember')
                                            {{ $payment->fullname }} (Non-ICS)
                                        @else
                                            Guest
                                        @endif
                                    </p>
                                </div>

                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-500">Note</p>
                                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter payment note (optional)">{{ old('description', $payment->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Cash Payment Details -->
                        <div id="cash-fields" class="px-6 py-4 border-t border-gray-200" style="display: none;">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Cash Payment Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Officer in Charge -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Officer in Charge</p>
                                    <input type="text" id="officer_in_charge" name="officer_in_charge" value="{{ old('officer_in_charge', $payment->officer_in_charge) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter officer's name">
                                    @error('officer_in_charge')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Receipt Control Number -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Receipt Control Number</p>
                                    <input type="text" id="receipt_control_number" name="receipt_control_number" value="{{ old('receipt_control_number', $payment->receipt_control_number) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter receipt control number">
                                    @error('receipt_control_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Cash Proof Upload -->
                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-500">Proof of Payment</p>
                                    <input type="file" id="cash_proof_of_payment" name="cash_proof_of_payment" accept=".jpg,.jpeg" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <p class="mt-1 text-xs text-gray-500">Upload a new image to replace the current one (JPG only).</p>
                                    @error('cash_proof_of_payment')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if(isset($payment->cash_proof_path) && $payment->cash_proof_path)
                                <div class="md:col-span-2 mt-4">
                                    <p class="text-sm font-medium text-gray-500 mb-2">Current Proof of Payment</p>
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        @php
                                            $proofPath = $payment->cash_proof_path;
                                            // Check if it's a base64 file
                                            if (strpos($proofPath, 'base64/') === 0 && file_exists(public_path($proofPath))) {
                                                $base64Content = file_get_contents(public_path($proofPath));
                                                $src = $base64Content;
                                            } else {
                                                $src = asset($proofPath);
                                            }
                                        @endphp
                                        <img src="{{ $src }}" alt="Cash Payment Proof" class="w-full max-w-md h-auto">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- GCash Specific Fields -->
                        <div id="gcash-fields" class="px-6 py-4 border-t border-gray-200" style="display: none;">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">GCash Payment Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- GCash Name -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">GCash Account Name</p>
                                    <input type="text" id="gcash_name" name="gcash_name" value="{{ old('gcash_name', $payment->gcash_name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter GCash account name">
                                    @error('gcash_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- GCash Number -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">GCash Mobile Number</p>
                                    <input type="tel" id="gcash_num" name="gcash_num" value="{{ old('gcash_num', $payment->gcash_num) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="09123456789" pattern="[0-9]{11}">
                                    @error('gcash_num')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Reference Number -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Reference Number</p>
                                    <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number', $payment->reference_number) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter GCash reference number">
                                    @error('reference_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- GCash Proof Upload -->
                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-500">Proof of Payment</p>
                                    <input type="file" id="gcash_proof_of_payment" name="gcash_proof_of_payment" accept=".jpg,.jpeg" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <p class="mt-1 text-xs text-gray-500">Upload a new image to replace the current one (JPG only).</p>
                                    @error('gcash_proof_of_payment')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if(isset($payment->gcash_proof_path) && $payment->gcash_proof_path)
                                <div class="md:col-span-2 mt-4">
                                    <p class="text-sm font-medium text-gray-500 mb-2">Current Proof of Payment</p>
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        @php
                                            $proofPath = $payment->gcash_proof_path;
                                            // Check if it's a base64 file
                                            if (strpos($proofPath, 'base64/') === 0 && file_exists(public_path($proofPath))) {
                                                $base64Content = file_get_contents(public_path($proofPath));
                                                $src = $base64Content;
                                            } else {
                                                $src = asset($proofPath);
                                            }
                                        @endphp
                                        <img src="{{ $src }}" alt="GCash Payment Proof" class="w-full max-w-md h-auto">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        @if(auth()->user()->canManagePayments())
                            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                        @else
                            <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                        @endif
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
        const paymentMethod = document.getElementById('payment_method');
        const gcashFields = document.getElementById('gcash-fields');
        const cashFields = document.getElementById('cash-fields');
        const totalPriceInput = document.getElementById('total_price');
        const gcashAmountInput = document.getElementById('gcash_amount');

        // Function to toggle payment method fields
        function togglePaymentFields() {
            if (paymentMethod.value === 'GCASH') {
                gcashFields.style.display = 'block';
                cashFields.style.display = 'none';
                // Make GCash fields required
                document.getElementById('gcash_name').required = true;
                document.getElementById('gcash_num').required = true;
                document.getElementById('reference_number').required = true;
                // Remove required from cash fields
                document.getElementById('officer_in_charge').required = false;
                document.getElementById('receipt_control_number').required = false;
            } else if (paymentMethod.value === 'CASH') {
                gcashFields.style.display = 'none';
                cashFields.style.display = 'block';
                // Make cash fields required
                document.getElementById('officer_in_charge').required = true;
                document.getElementById('receipt_control_number').required = true;
                // Remove required from GCash fields
                document.getElementById('gcash_name').required = false;
                document.getElementById('gcash_num').required = false;
                document.getElementById('reference_number').required = false;
            } else {
                gcashFields.style.display = 'none';
                cashFields.style.display = 'none';
                // Remove required from all fields
                document.getElementById('gcash_name').required = false;
                document.getElementById('gcash_num').required = false;
                document.getElementById('reference_number').required = false;
                document.getElementById('officer_in_charge').required = false;
                document.getElementById('receipt_control_number').required = false;
            }
        }

        // Initial check
        togglePaymentFields();

        // Event listeners
        paymentMethod.addEventListener('change', togglePaymentFields);

        // Show the appropriate fields based on current payment method
        if (paymentMethod.value) {
            togglePaymentFields();
        }
    });
</script>
@endsection