@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        GCash Payment Details
                    </h2>
                    <div class="flex space-x-2">
                        <a href="{{ Auth::user()->canManagePayments() ? route('admin.payments.index') : route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 transition shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Payments
                        </a>
                        @if(Auth::user()->canManagePayments())
                            <a href="{{ route('admin.gcash-payments.edit', $payment->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition shadow-sm">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </a>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Transaction ID</p>
                                        <p class="text-base font-medium text-gray-900">#{{ $payment->id }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Amount</p>
                                        <p class="text-base font-medium text-gray-900">₱{{ number_format($payment->total_price, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Purpose</p>
                                        <p class="text-base font-medium text-gray-900">{{ $payment->purpose }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Payment Status</p>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $payment->payment_status === 'Paid' ? 'bg-green-100 text-green-800' :
                                               ($payment->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $payment->payment_status }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Date</p>
                                        <p class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($payment->placed_on)->format('M d, Y h:i A') }}</p>
                                    </div>
                                    @if($payment->description)
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Description</p>
                                            <p class="text-base font-medium text-gray-900">{{ $payment->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">GCash Payment Details</h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">GCash Name</p>
                                        <p class="text-base font-medium text-gray-900">{{ $payment->gcash_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">GCash Number</p>
                                        <p class="text-base font-medium text-gray-900">{{ $payment->gcash_num }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Reference Number</p>
                                        <p class="text-base font-medium text-gray-900">{{ $payment->reference_number }}</p>
                                    </div>
                                    @if($payment->gcash_proof_path)
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-2">Proof of Payment</p>
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
                                            <a href="{{ $src }}" target="_blank" class="block">
                                                <img src="{{ $src }}" alt="Proof of Payment" class="max-w-full h-auto rounded-lg border border-gray-200 shadow-sm" style="max-height: 300px;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(Auth::user()->canManagePayments() && $payment->payment_status === 'Pending')
                    <div class="mt-6 flex space-x-4">
                        <form action="{{ route('admin.gcash-payments.approve', $payment->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition shadow-sm">
                                <i class="fas fa-check-circle mr-2"></i> Approve Payment
                            </button>
                        </form>
                        <!-- Reject button removed as requested -->
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
