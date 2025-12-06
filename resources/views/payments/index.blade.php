@php
use Illuminate\Support\Facades\Auth;
@endphp

@extends('layouts.app')

@section('content')
@if(Auth::user()->isAdminRole())
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Paid -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase">TOTAL PAID</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($totalPayments, 2) }}</p>
                        <div class="mt-1">
                            <span class="text-xs text-gray-500">All time</span>
                        </div>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase">THIS MONTH</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($thisMonthPayments, 2) }}</p>
                        <div class="mt-1">
                            <span class="text-xs text-gray-500">Current month</span>
                        </div>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase">PENDING</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($pendingPayments, 2) }}</p>
                        <div class="mt-1">
                            <span class="text-xs text-gray-500">Awaiting approval</span>
                        </div>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">My Payments</h1>
                        <p class="text-gray-600 text-sm mt-1">Manage and track all member payments</p>
                        @if(isset($currentCalendar))
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $currentCalendar->school_calendar_desc }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="flex flex-wrap mt-4 md:mt-0 space-x-3">
                        @if(Auth::user()->canManagePayments())
                            <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-[#c21313] hover:bg-red-800 transition">
                                <i class="fas fa-plus mr-2"></i> Record Payment
                            </a>
                            <a href="{{ route('admin.payment-fees.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-cog mr-2"></i> Manage Fees
                            </a>
                        @endif
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

                <!-- Search & Filter -->
                <div class="mb-6 flex flex-col md:flex-row justify-between items-center">
                    <div class="w-full md:w-auto mb-4 md:mb-0">
                        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex items-center space-x-2">
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" id="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm" placeholder="Search by member name, email, or transaction ID..." value="{{ request('search') }}">
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-[#c21313] hover:bg-red-800 transition">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-redo"></i>
                            </a>
                        </form>
                    </div>
                    <div class="w-full md:w-auto flex space-x-2">
                        <select id="payment_method" name="payment_method" class="block pl-3 pr-10 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-1 focus:ring-[#c21313] focus:border-[#c21313] rounded-md shadow-sm" onchange="this.form.submit()">
                            <option value="">All Methods</option>
                            <option value="CASH" {{ request('payment_method') == 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="GCASH" {{ request('payment_method') == 'GCASH' ? 'selected' : '' }}>GCash</option>
                        </select>
                        <select id="status" name="status" class="block pl-3 pr-10 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-1 focus:ring-[#c21313] focus:border-[#c21313] rounded-md shadow-sm" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @if(isset($schoolCalendars) && count($schoolCalendars) > 0)
                        <select id="school_calendar_id" name="school_calendar_id" class="block pl-3 pr-10 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-1 focus:ring-[#c21313] focus:border-[#c21313] rounded-md shadow-sm" onchange="this.form.submit()">
                            <option value="">All Academic Years</option>
                            @foreach($schoolCalendars as $calendar)
                                <option value="{{ $calendar->id }}" {{ request('school_calendar_id') == $calendar->id ? 'selected' : '' }}>
                                    {{ $calendar->school_calendar_short_desc }}
                                    @if($calendar->is_selected) (Current) @endif
                                </option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                </div>

                <script>
                    // Add event listeners to dropdowns for auto-submit
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('payment_method').addEventListener('change', function() {
                            let url = '{{ route('admin.payments.index') }}?payment_method=' + this.value +
                                '&status=' + document.getElementById('status').value +
                                '&search=' + document.getElementById('search').value;

                            // Add school_calendar_id if it exists
                            if (document.getElementById('school_calendar_id')) {
                                url += '&school_calendar_id=' + document.getElementById('school_calendar_id').value;
                            }

                            window.location.href = url;
                        });

                        document.getElementById('status').addEventListener('change', function() {
                            let url = '{{ route('admin.payments.index') }}?status=' + this.value +
                                '&payment_method=' + document.getElementById('payment_method').value +
                                '&search=' + document.getElementById('search').value;

                            // Add school_calendar_id if it exists
                            if (document.getElementById('school_calendar_id')) {
                                url += '&school_calendar_id=' + document.getElementById('school_calendar_id').value;
                            }

                            window.location.href = url;
                        });

                        // Add event listener for school_calendar_id if it exists
                        if (document.getElementById('school_calendar_id')) {
                            document.getElementById('school_calendar_id').addEventListener('change', function() {
                                window.location.href = '{{ route('admin.payments.index') }}?school_calendar_id=' + this.value +
                                    '&payment_method=' + document.getElementById('payment_method').value +
                                    '&status=' + document.getElementById('status').value +
                                    '&search=' + document.getElementById('search').value;
                            });
                        }
                    });
                </script>

                <!-- Payment Type Tabs -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button id="tab-ics-members" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm active-tab" onclick="showTab('ics-members')">
                                ICS Members
                            </button>
                            <button id="tab-non-ics-members" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" onclick="showTab('non-ics-members')">
                                Non-ICS Members
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- ICS Members Payments Table -->
                <div id="table-ics-members" class="overflow-x-auto border border-gray-200 rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $hasIcsMembers = false; @endphp

                            <!-- We no longer use the Order table, so we don't need to display $payments -->
                            @php $hasIcsMembers = false; @endphp

                            <!-- Cash payments from cash_payments table -->
                            @foreach($cashPayments as $payment)
                                @php $hasIcsMembers = true; @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $payment->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ optional($payment->user)->firstname }} {{ optional($payment->user)->lastname }}</div>
                                                <div class="text-gray-500">{{ $payment->email }}</div>
                                            </div>
                                        </div>
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
                                            <a href="{{ route('admin.cash-payments.show', $payment->id) }}" class="text-[#c21313] hover:text-red-800" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->payment_status === 'Pending')
                                                @if(Auth::user()->canManagePayments())
                                                    <form action="{{ route('admin.cash-payments.approve', $payment->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-800" title="Approve Payment">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                    <!-- Reject button removed as requested -->
                                                @endif
                                            @elseif($payment->payment_status === 'Paid')
                                                @if(Auth::user()->canManagePayments())
                                                    <a href="{{ route('admin.cash-payments.edit', $payment->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit Payment">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <!-- GCash payments from gcash_payments table -->
                            @foreach($gcashPayments as $payment)
                                @php $hasIcsMembers = true; @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $payment->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ optional($payment->user)->firstname }} {{ optional($payment->user)->lastname }}</div>
                                                <div class="text-gray-500">{{ $payment->email }}</div>
                                            </div>
                                        </div>
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
                                            <a href="{{ route('admin.gcash-payments.show', $payment->id) }}" class="text-[#c21313] hover:text-red-800" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->payment_status === 'Pending')
                                                @if(Auth::user()->canManagePayments())
                                                    <form action="{{ route('admin.gcash-payments.approve', $payment->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-800" title="Approve Payment">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                    <!-- Reject button removed as requested -->
                                                @endif
                                            @elseif($payment->payment_status === 'Paid')
                                                @if(Auth::user()->canManagePayments())
                                                    <a href="{{ route('admin.gcash-payments.edit', $payment->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit Payment">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if(!$hasIcsMembers && $payments->isEmpty() && $cashPayments->isEmpty() && $gcashPayments->isEmpty())
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <div class="bg-gray-100 rounded-full p-4 mb-4">
                                                <i class="fas fa-credit-card text-3xl text-gray-400"></i>
                                            </div>
                                            <p class="text-lg font-medium mb-1">No ICS member payments found</p>
                                            <p class="text-sm mb-3">Start recording ICS member payments to track financial transactions.</p>
                                            @if(Auth::user()->canManagePayments())
                                                <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                                                    <i class="fas fa-plus mr-2"></i> RECORD A PAYMENT
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Non-ICS Members Payments Table -->
                <div id="table-non-ics-members" class="overflow-x-auto border border-gray-200 rounded-lg shadow hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Non-ICS Member</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course/Year/Section</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $hasNonIcsMembers = false; @endphp
                            @forelse($nonIcsMembers as $member)
                                @php $hasNonIcsMembers = true; @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $member->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $member->fullname }}</div>
                                                <div class="text-gray-500">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $member->course_year_section }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱{{ number_format($member->total_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $member->method === 'CASH' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $member->method }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $member->placed_on ? \Carbon\Carbon::parse($member->placed_on)->format('M d, Y h:i A') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $member->payment_status === 'Paid' ? 'bg-green-100 text-green-800' :
                                               ($member->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $member->payment_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('admin.payments.show-non-ics', $member->id) }}" class="text-[#c21313] hover:text-red-800" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($member->payment_status === 'Pending')
                                                @if(Auth::user()->canManagePayments())
                                                    <form method="POST" action="{{ route('admin.payments.approve-non-ics', $member->id) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-800" title="Approve Payment">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                    <!-- Reject button removed as requested -->
                                                @endif
                                            @elseif($member->payment_status === 'Paid')
                                                @if(Auth::user()->canManagePayments())
                                                    <a href="{{ route('admin.non-ics-members.edit', $member->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit Payment">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                @php $hasNonIcsMembers = false; @endphp
                            @endforelse

                            @if(!$hasNonIcsMembers)
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <div class="bg-gray-100 rounded-full p-4 mb-4">
                                                <i class="fas fa-credit-card text-3xl text-gray-400"></i>
                                            </div>
                                            <p class="text-lg font-medium mb-1">No non-ICS member payments found</p>
                                            <p class="text-sm mb-3">Start recording non-ICS member payments to track financial transactions.</p>
                                            @if(Auth::user()->canManagePayments())
                                                <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                                                    <i class="fas fa-plus mr-2"></i> RECORD A PAYMENT
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>


                <script>
                    function showTab(tabName) {
                        // Hide all tables
                        document.getElementById('table-ics-members').classList.add('hidden');
                        document.getElementById('table-non-ics-members').classList.add('hidden');

                        // Hide all pagination
                        document.getElementById('pagination-ics-members').classList.add('hidden');
                        document.getElementById('pagination-non-ics-members').classList.add('hidden');

                        // Remove active class from all tabs
                        document.getElementById('tab-ics-members').classList.remove('border-indigo-500', 'text-indigo-600');
                        document.getElementById('tab-ics-members').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

                        document.getElementById('tab-non-ics-members').classList.remove('border-indigo-500', 'text-indigo-600');
                        document.getElementById('tab-non-ics-members').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

                        // Show selected table and activate tab
                        if (tabName === 'ics-members') {
                            document.getElementById('table-ics-members').classList.remove('hidden');
                            document.getElementById('pagination-ics-members').classList.remove('hidden');
                            document.getElementById('tab-ics-members').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                            document.getElementById('tab-ics-members').classList.add('border-indigo-500', 'text-indigo-600');
                        } else if (tabName === 'non-ics-members') {
                            document.getElementById('table-non-ics-members').classList.remove('hidden');
                            document.getElementById('pagination-non-ics-members').classList.remove('hidden');
                            document.getElementById('tab-non-ics-members').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                            document.getElementById('tab-non-ics-members').classList.add('border-indigo-500', 'text-indigo-600');
                        }
                    }
                </script>

                <!-- Pagination -->
                <div class="mt-6">
                    <div id="pagination-ics-members">
                        <!-- We're not using the $payments collection anymore -->
                        <!-- Instead, we're displaying cash and gcash payments separately -->
                        <!-- No pagination needed here as we're showing all payments -->
                    </div>
                    <div id="pagination-non-ics-members" class="hidden">
                        @if(method_exists($nonIcsMembers, 'links') && method_exists($nonIcsMembers, 'appends'))
                            {{ $nonIcsMembers->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="p-6">
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="bg-gray-100 rounded-full p-6 mb-4">
                            <i class="fas fa-lock text-4xl text-gray-400"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Access Restricted</h2>
                        <p class="text-gray-600">You don't have permission to access this page.</p>
                        <a href="{{ route('client.payments.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i> Go to My Payments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection