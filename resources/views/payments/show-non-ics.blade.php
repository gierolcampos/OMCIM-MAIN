@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Non-ICS Member Payment Details</h1>
                        <p class="text-gray-600 mt-1">View payment information for non-ICS member</p>
                    </div>
                    <div class="flex flex-wrap mt-4 md:mt-0 gap-3">
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Payments
                        </a>
                    </div>
                </div>

                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                        <button class="ml-auto" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times text-green-500"></i>
                        </button>
                    </div>
                </div>
                @endif

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

                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-800">Payment #{{ $nonIcsMember->id }}</h2>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $nonIcsMember->payment_status === 'Paid' ? 'bg-green-100 text-green-800' :
                                   ($nonIcsMember->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $nonIcsMember->payment_status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Payer Information -->
                            <div>
                                <h3 class="text-md font-semibold text-gray-700 mb-3">Payer Information</h3>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">Full Name</p>
                                        <p class="font-medium">{{ $nonIcsMember->fullname }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">Email</p>
                                        <p class="font-medium">{{ $nonIcsMember->email }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">Course/Year/Section</p>
                                        <p class="font-medium">{{ $nonIcsMember->course_year_section }}</p>
                                    </div>
                                    @if($nonIcsMember->mobile_no)
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">Mobile Number</p>
                                        <p class="font-medium">{{ $nonIcsMember->mobile_no }}</p>
                                    </div>
                                    @endif
                                    @if($nonIcsMember->student_id)
                                    <div>
                                        <p class="text-sm text-gray-500">Student ID</p>
                                        <p class="font-medium">{{ $nonIcsMember->student_id }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Payment Details -->
                            <div>
                                <h3 class="text-md font-semibold text-gray-700 mb-3">Payment Details</h3>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">Amount</p>
                                        <p class="font-medium">₱{{ number_format($nonIcsMember->total_price, 2) }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">Payment Method</p>
                                        <p class="font-medium">{{ $nonIcsMember->method }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">Purpose</p>
                                        <p class="font-medium">{{ $nonIcsMember->purpose ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">Date</p>
                                        <p class="font-medium">{{ $nonIcsMember->placed_on ? \Carbon\Carbon::parse($nonIcsMember->placed_on)->format('M d, Y h:i A') : 'N/A' }}</p>
                                    </div>
                                    @if($nonIcsMember->description)
                                    <div>
                                        <p class="text-sm text-gray-500">Description</p>
                                        <p class="font-medium">{{ $nonIcsMember->description }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Method Specific Details -->
                            @if($nonIcsMember->method === 'GCASH')
                            <div>
                                <h3 class="text-md font-semibold text-gray-700 mb-3">GCash Details</h3>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">GCash Name</p>
                                        <p class="font-medium">{{ $nonIcsMember->gcash_name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">GCash Number</p>
                                        <p class="font-medium">{{ $nonIcsMember->gcash_num ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Reference Number</p>
                                        <p class="font-medium">{{ $nonIcsMember->reference_number ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            @elseif($nonIcsMember->method === 'CASH')
                            <div>
                                <h3 class="text-md font-semibold text-gray-700 mb-3">Cash Details</h3>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500">Receipt Control Number</p>
                                        <p class="font-medium">{{ $nonIcsMember->receipt_control_number ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Officer in Charge</p>
                                        <p class="font-medium">{{ $nonIcsMember->officer_in_charge ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Proof of Payment -->
                            <div>
                                <h3 class="text-md font-semibold text-gray-700 mb-3">Proof of Payment</h3>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    @if($nonIcsMember->method === 'GCASH' && $nonIcsMember->gcash_proof_path)
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500 mb-2">GCash Receipt</p>
                                        <a href="{{ asset($nonIcsMember->gcash_proof_path) }}" target="_blank" class="block">
                                            <img src="{{ asset($nonIcsMember->gcash_proof_path) }}" alt="GCash Receipt" class="max-w-full h-auto rounded-lg border border-gray-300 shadow-sm">
                                        </a>
                                    </div>
                                    @elseif($nonIcsMember->method === 'CASH' && $nonIcsMember->cash_proof_path)
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500 mb-2">Cash Receipt</p>
                                        <a href="{{ asset($nonIcsMember->cash_proof_path) }}" target="_blank" class="block">
                                            <img src="{{ asset($nonIcsMember->cash_proof_path) }}" alt="Cash Receipt" class="max-w-full h-auto rounded-lg border border-gray-300 shadow-sm">
                                        </a>
                                    </div>
                                    @else
                                    <p class="text-gray-500 italic">No proof of payment uploaded.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if($nonIcsMember->payment_status === 'Pending')
                        <div class="mt-8 flex justify-end space-x-4">
                            <form method="POST" action="{{ route('admin.payments.approve-non-ics', $nonIcsMember->id) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 transition">
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
    </div>
</div>
@endsection
