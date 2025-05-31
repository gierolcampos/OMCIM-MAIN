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

        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Payments</h1>
                        <p class="text-gray-600 mt-1">Manage all payment transactions</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('payment.types.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#c21313] transition transform hover:scale-105">
                            <i class="fas fa-plus-circle mr-2"></i> Record Payment
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

                <!-- Search & Filter -->
                <div class="mb-6 bg-gray-50 p-5 rounded-xl shadow-sm border border-gray-100">
                    <form method="GET" action="{{ route('payment.types.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" id="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search by ID, name, or email..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status" name="status" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="Refunded" {{ request('status') == 'Refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div class="md:col-span-3 flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-800 transition">
                                <i class="fas fa-filter mr-2"></i> Apply Filters
                            </button>
                            <a href="{{ route('payment.types.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-[#c21313] bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-redo mr-2"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button id="tab-cash" onclick="showTab('cash')" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Cash Payments
                        </button>
                        <button id="tab-gcash" onclick="showTab('gcash')" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            GCash Payments
                        </button>
                    </nav>
                </div>

                <!-- Cash Payments Table -->
                <div id="table-cash" class="overflow-x-auto border border-gray-200 rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($cashPayments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $payment->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ optional($payment->user)->firstname }} {{ optional($payment->user)->lastname }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱{{ number_format($payment->total_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->purpose }}
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
                                            <a href="{{ route('payment.types.cash.show', $payment->id) }}" class="text-[#c21313] hover:text-red-800" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->payment_status === 'Pending')
                                                <form action="{{ route('payment.types.cash.approve', $payment->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Approve Payment">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                                <!-- Reject button removed as requested -->
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <div class="bg-gray-100 rounded-full p-4 mb-4">
                                            <i class="fas fa-money-bill-wave text-3xl text-gray-400"></i>
                                        </div>
                                        <p class="text-lg font-medium mb-1">No cash payments found</p>
                                        <p class="text-sm mb-3">No cash payment records available.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- GCash Payments Table -->
                <div id="table-gcash" class="overflow-x-auto border border-gray-200 rounded-lg shadow hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($gcashPayments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $payment->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ optional($payment->user)->firstname }} {{ optional($payment->user)->lastname }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱{{ number_format($payment->total_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->purpose }}
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
                                            <a href="{{ route('payment.types.gcash.show', $payment->id) }}" class="text-[#c21313] hover:text-red-800" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->payment_status === 'Pending')
                                                <form action="{{ route('payment.types.gcash.approve', $payment->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Approve Payment">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                                <!-- Reject button removed as requested -->
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <div class="bg-gray-100 rounded-full p-4 mb-4">
                                            <i class="fas fa-mobile-alt text-3xl text-gray-400"></i>
                                        </div>
                                        <p class="text-lg font-medium mb-1">No GCash payments found</p>
                                        <p class="text-sm mb-3">No GCash payment records available.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <script>
                    function showTab(tabName) {
                        // Hide all tables
                        document.getElementById('table-cash').classList.add('hidden');
                        document.getElementById('table-gcash').classList.add('hidden');

                        // Hide all pagination
                        document.getElementById('pagination-cash').classList.add('hidden');
                        document.getElementById('pagination-gcash').classList.add('hidden');

                        // Remove active class from all tabs
                        document.getElementById('tab-cash').classList.remove('border-indigo-500', 'text-indigo-600');
                        document.getElementById('tab-cash').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

                        document.getElementById('tab-gcash').classList.remove('border-indigo-500', 'text-indigo-600');
                        document.getElementById('tab-gcash').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

                        // Show selected table and activate tab
                        if (tabName === 'cash') {
                            document.getElementById('table-cash').classList.remove('hidden');
                            document.getElementById('pagination-cash').classList.remove('hidden');
                            document.getElementById('tab-cash').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                            document.getElementById('tab-cash').classList.add('border-indigo-500', 'text-indigo-600');
                        } else if (tabName === 'gcash') {
                            document.getElementById('table-gcash').classList.remove('hidden');
                            document.getElementById('pagination-gcash').classList.remove('hidden');
                            document.getElementById('tab-gcash').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                            document.getElementById('tab-gcash').classList.add('border-indigo-500', 'text-indigo-600');
                        }
                    }
                </script>

                <!-- Pagination -->
                <div class="mt-6">
                    <div id="pagination-cash">
                        {{ $cashPayments->appends(['gcash_page' => $gcashPayments->currentPage()])->links() }}
                    </div>
                    <div id="pagination-gcash" class="hidden">
                        {{ $gcashPayments->appends(['cash_page' => $cashPayments->currentPage()])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
