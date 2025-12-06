@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Paid -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Paid</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">₱{{ number_format($totalPayments, 2) }}</p>
                        <div class="mt-2 flex items-center">
                            <span class="text-xs text-gray-500">All time</span>
                        </div>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full border border-green-200">
                        <i class="fas fa-check-circle text-xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">This Month</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">₱{{ number_format($thisMonthPayments, 2) }}</p>
                        <div class="mt-2 flex items-center">
                            <span class="text-xs text-gray-500">Current month</span>
                        </div>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full border border-blue-200">
                        <i class="fas fa-calendar-check text-xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pending</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">₱{{ number_format($pendingPayments, 2) }}</p>
                        <div class="mt-2 flex items-center">
                            <span class="text-xs text-gray-500">Awaiting approval</span>
                        </div>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full border border-yellow-200">
                        <i class="fas fa-clock text-xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">My Payments</h1>
                        <p class="text-gray-600 mt-1">View your payment history and pending payments</p>
                        @if(isset($currentCalendar))
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $currentCalendar->school_calendar_desc }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <!-- Submit Payment button removed for members -->
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

                <!-- Search & Filter -->
                <div class="mb-6 bg-gray-50 p-5 rounded-xl shadow-sm border border-gray-100">
                    <form method="GET" action="{{ route('client.payments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" id="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search by transaction ID..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm">
                                <option value="">All Methods</option>
                                <option value="CASH" {{ request('payment_method') == 'CASH' ? 'selected' : '' }}>Cash</option>
                                <option value="GCASH" {{ request('payment_method') == 'GCASH' ? 'selected' : '' }}>GCash</option>
                            </select>
                        </div>
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="payment_status" name="payment_status" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Paid" {{ request('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Rejected" {{ request('payment_status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        @if(isset($schoolCalendars) && count($schoolCalendars) > 0)
                        <div>
                            <label for="school_calendar_id" class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                            <select id="school_calendar_id" name="school_calendar_id" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm">
                                <option value="">All Academic Years</option>
                                @foreach($schoolCalendars as $calendar)
                                    <option value="{{ $calendar->id }}" {{ request('school_calendar_id') == $calendar->id ? 'selected' : '' }}>
                                        {{ $calendar->school_calendar_short_desc }}
                                        @if($calendar->is_selected) (Current) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="md:col-span-3 flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-800 transition">
                                <i class="fas fa-filter mr-2"></i> Apply Filters
                            </button>
                            <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-redo mr-2"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Payments Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $hasPayments = false; @endphp

                            <!-- We no longer use the Order table, so we don't need to display $payments -->
                            @php $hasPayments = false; @endphp

                            <!-- Cash payments from cash_payments table -->
                            @foreach($cashPayments as $payment)
                                @php $hasPayments = true; @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $payment->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱{{ number_format($payment->total_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            CASH
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($payment->placed_on)->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $payment->payment_status === 'Paid' ? 'bg-green-100 text-green-800' :
                                               ($payment->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $payment->payment_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('client.cash-payments.show', $payment->id) }}" class="text-[#c21313] hover:text-red-800" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->payment_status === 'Pending')
                                                <a href="{{ route('client.cash-payments.edit', $payment->id) }}" class="text-[#c21313] hover:text-red-800" title="Edit Payment">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <!-- GCash payments from gcash_payments table -->
                            @foreach($gcashPayments as $payment)
                                @php $hasPayments = true; @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $payment->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱{{ number_format($payment->total_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            GCASH
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($payment->placed_on)->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $payment->payment_status === 'Paid' ? 'bg-green-100 text-green-800' :
                                               ($payment->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $payment->payment_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('client.gcash-payments.show', $payment->id) }}" class="text-[#c21313] hover:text-red-800" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->payment_status === 'Pending')
                                                <a href="{{ route('client.gcash-payments.edit', $payment->id) }}" class="text-[#c21313] hover:text-red-800" title="Edit Payment">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if(!$hasPayments)
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <div class="bg-gray-100 rounded-full p-4 mb-4">
                                            <i class="fas fa-credit-card text-3xl text-gray-400"></i>
                                        </div>
                                        <p class="text-lg font-medium mb-1">No payments found</p>
                                        <p class="text-sm mb-3">No payment records available.</p>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between">
                    <div class="text-sm text-gray-700 font-medium mb-4 sm:mb-0">
                        @php
                            // Calculate total count from cash and gcash payments
                            $totalCount = $cashPayments->count() + $gcashPayments->count();
                        @endphp

                        @if($totalCount > 0)
                            Showing <span class="font-bold text-[#c21313]">{{ $totalCount }}</span> payments
                        @else
                            No payments found
                        @endif
                    </div>
                    <div class="flex space-x-4">
                        <!-- We're not using the $payments collection anymore -->
                        <!-- Instead, we're displaying cash and gcash payments separately -->
                        <!-- No pagination needed here as we're showing all payments -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection