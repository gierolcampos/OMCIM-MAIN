@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit GCash Payment</h1>
                        <p class="text-gray-600 mt-1">Update GCash payment details</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
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

                <form action="{{ route('admin.gcash-payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                        <!-- Transaction Information -->
                        <div class="px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Transaction ID (Read-only) -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Transaction ID</p>
                                    <p class="mt-1 text-sm text-gray-900">#{{ $payment->id }}</p>
                                </div>

                                <!-- Payment Status -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Payment Status</p>
                                    <select id="payment_status" name="payment_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md @error('payment_status') border-red-500 @enderror" required>
                                        <option value="Pending" {{ $payment->payment_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Paid" {{ $payment->payment_status == 'Paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="Refunded" {{ $payment->payment_status == 'Refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                    @error('payment_status')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Payment Method (Read-only) -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Payment Method</p>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            GCASH
                                        </span>
                                    </p>
                                </div>

                                <!-- Date & Time (Read-only) -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date & Time</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($payment->placed_on)->format('M d, Y h:i A') }}</p>
                                </div>

                                <!-- Amount -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Amount</p>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">₱</span>
                                        </div>
                                        <input type="number" step="0.01" min="0" name="total_price" id="total_price" value="{{ old('total_price', $payment->total_price) }}" class="focus:ring-red-500 focus:border-red-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md bg-gray-50 @error('total_price') border-red-500 @enderror" placeholder="0.00" required readonly>
                                        <p class="mt-1 text-xs text-gray-500">Amount is automatically set based on the selected purpose.</p>
                                    </div>
                                    @error('total_price')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Purpose -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Purpose</p>
                                    <select id="purpose" name="purpose" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md @error('purpose') border-red-500 @enderror" required>
                                        <option value="">Select Purpose</option>
                                        <option value="Membership Fee" {{ $payment->purpose == 'Membership Fee' ? 'selected' : '' }}>Membership Fee</option>
                                        <option value="Event Fee" {{ $payment->purpose == 'Event Fee' ? 'selected' : '' }}>Event Fee</option>
                                        <option value="ICS Merch" {{ $payment->purpose == 'ICS Merch' ? 'selected' : '' }}>ICS Merch</option>
                                        <option value="Other" {{ $payment->purpose == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('purpose')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Member (Read-only) -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Member</p>
                                    @php
                                        $memberUser = $users->where('id', $payment->user_id)->first();
                                        $memberName = $memberUser ? $memberUser->fullname : 'Unknown';
                                    @endphp
                                    <p class="mt-1 text-sm text-gray-900">{{ $memberName }}</p>
                                    <input type="hidden" name="user_id" value="{{ $payment->user_id }}" readonly>
                                    @error('user_id')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Receipt Control Number (Hidden) -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Receipt Control Number</p>
                                    <p class="mt-1 text-sm text-gray-900">N/A</p>
                                    <input type="hidden" name="receipt_control_number" value="{{ $payment->receipt_control_number }}">
                                </div>

                                <!-- Officer in-charge (Hidden) -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Officer in-charge</p>
                                    <p class="mt-1 text-sm text-gray-900">N/A</p>
                                    <input type="hidden" name="officer_in_charge" value="{{ $payment->officer_in_charge }}">
                                </div>

                                <!-- Description -->
                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-500">Description</p>
                                    <textarea name="description" id="description" rows="2" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('description') border-red-500 @enderror">{{ old('description', $payment->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- GCash Payment Details -->
                        <div class="px-6 py-4 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">GCash Payment Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- GCash Name -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">GCash Account Name</p>
                                    <input type="text" name="gcash_name" id="gcash_name" value="{{ old('gcash_name', $payment->gcash_name) }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('gcash_name') border-red-500 @enderror" required>
                                    @error('gcash_name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- GCash Number -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">GCash Number</p>
                                    <input type="text" name="gcash_num" id="gcash_num" value="{{ old('gcash_num', $payment->gcash_num) }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('gcash_num') border-red-500 @enderror" required>
                                    @error('gcash_num')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Reference Number -->
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Reference Number</p>
                                    <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $payment->reference_number) }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('reference_number') border-red-500 @enderror" required>
                                    @error('reference_number')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- GCash Proof -->
                                <div class="md:col-span-2 mt-2">
                                    <p class="text-sm font-medium text-gray-500 mb-2">Proof of Payment</p>
                                    @if($payment->gcash_proof_path)
                                        <div class="border border-gray-200 rounded-lg overflow-hidden mb-2">
                                            @php
                                                $proofPath = $payment->gcash_proof_path;
                                                // Check if it's a base64 file
                                                if (strpos($proofPath, 'base64/') === 0 && file_exists(public_path($proofPath))) {
                                                    $base64Content = file_get_contents(public_path($proofPath));
                                                    $src = $base64Content;
                                                    $viewUrl = $base64Content;
                                                } else {
                                                    $src = asset($proofPath);
                                                    $viewUrl = asset($proofPath);
                                                }
                                            @endphp
                                            <img src="{{ $src }}" alt="GCash Payment Proof" class="w-full max-w-md h-auto">
                                        </div>
                                        <div class="mb-2">
                                            <a href="{{ $viewUrl }}" target="_blank" class="text-sm text-[#c21313] hover:text-red-800">
                                                <i class="fas fa-external-link-alt mr-1"></i> View Full Image
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" name="gcash_proof_of_payment" id="gcash_proof_of_payment" accept="image/jpeg" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('gcash_proof_of_payment') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Upload a new image to replace the current one (JPG only)</p>
                                    @error('gcash_proof_of_payment')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
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
