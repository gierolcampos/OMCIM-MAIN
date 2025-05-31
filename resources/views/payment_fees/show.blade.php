@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Payment Fee Details</h1>
                        <p class="text-gray-600 mt-1">View payment fee information</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-3">
                        <a href="{{ route('admin.payment-fees.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Payment Fees
                        </a>
                        <a href="{{ route('admin.payment-fees.edit', $paymentFee->fee_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 transition">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Purpose</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $paymentFee->purpose }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Amount</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format($paymentFee->total_price, 2) }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="mt-1 text-gray-900">{{ $paymentFee->description ?? 'No description provided.' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentFee->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $paymentFee->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Created At</p>
                            <p class="mt-1 text-gray-900">{{ $paymentFee->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Last Updated</p>
                            <p class="mt-1 text-gray-900">{{ $paymentFee->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-between">
                    <form action="{{ route('admin.payment-fees.toggle-active', $paymentFee->fee_id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas {{ $paymentFee->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }} mr-2"></i>
                            {{ $paymentFee->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.payment-fees.destroy', $paymentFee->fee_id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg shadow-sm text-red-700 bg-white hover:bg-red-50 transition">
                            <i class="fas fa-trash mr-2"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete confirmation
        const deleteForm = document.querySelector('.delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this payment fee? This action cannot be undone.')) {
                    this.submit();
                }
            });
        }
    });
</script>
@endsection
