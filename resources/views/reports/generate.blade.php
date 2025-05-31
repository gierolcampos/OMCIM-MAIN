@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Generate Reports</h1>
                        <p class="text-gray-600 mt-1">Create and download various reports</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Reports
                        </a>
                    </div>
                </div>

                <!-- Report Types -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <!-- Attendance Report Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-6">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-500 mb-4">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Attendance Report</h3>
                        <p class="text-gray-600 mb-4">Generate attendance reports for events and activities</p>
                        <button type="button" onclick="showReportForm('attendance')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 transition">
                            <i class="fas fa-file-alt mr-2"></i> Generate Report
                        </button>
                    </div>

                    <!-- Payment Report Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-6">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-500 mb-4">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Payment Report</h3>
                        <p class="text-gray-600 mb-4">Generate reports on payments and financial transactions</p>
                        <button type="button" onclick="showReportForm('payment')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 transition">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> Generate Report
                        </button>
                    </div>

                    <!-- Membership Report Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-6">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-500 mb-4">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Membership Report</h3>
                        <p class="text-gray-600 mb-4">Generate reports on member status and demographics</p>
                        <button type="button" onclick="showReportForm('membership')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 transition">
                            <i class="fas fa-id-card mr-2"></i> Generate Report
                        </button>
                    </div>

                    <!-- Evaluation Report Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-6">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 text-yellow-500 mb-4">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Evaluation Report</h3>
                        <p class="text-gray-600 mb-4">Generate reports on event feedback and ratings</p>
                        <button type="button" onclick="showReportForm('evaluation')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 transition">
                            <i class="fas fa-chart-bar mr-2"></i> Generate Report
                        </button>
                    </div>
                </div>

                <!-- Report Forms (Hidden by default) -->
                <div id="report-forms" class="hidden">
                    <!-- Attendance Report Form -->
                    <div id="attendance-form" class="hidden">
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Generate Attendance Report</h3>
                            <form action="{{ route('admin.reports.generate') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="report_type" value="attendance">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="event_id" class="block text-sm font-medium text-gray-700 mb-1">Select Event <span class="text-red-500">*</span></label>
                                        <select id="event_id" name="event_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm" required>
                                            <option value="">Select an event</option>
                                            <!-- This will be populated with events from the database -->
                                            @foreach($events ?? [] as $event)
                                                <option value="{{ $event->id }}">{{ $event->title }} ({{ date('M d, Y', strtotime($event->start_date_time)) }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="attendance_status" class="block text-sm font-medium text-gray-700 mb-1">Attendance Status</label>
                                        <select id="attendance_status" name="attendance_status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                            <option value="all">All Statuses</option>
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="excused">Excused</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Export Format</label>
                                    <div class="flex space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="export_format" value="pdf" class="form-radio text-[#c21313]" checked>
                                            <span class="ml-2">PDF</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="export_format" value="csv" class="form-radio text-[#c21313]">
                                            <span class="ml-2">CSV</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3 pt-4">
                                    <button type="button" onclick="hideReportForms()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                                        Cancel
                                    </button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 transition">
                                        <i class="fas fa-file-download mr-2"></i> Generate Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Payment Report Form -->
                    <div id="payment-form" class="hidden">
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Generate Payment Report</h3>
                            <form action="{{ route('admin.reports.generate') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="report_type" value="payment">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="payment_date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                                        <input type="date" id="payment_date_from" name="date_from" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                    </div>

                                    <div>
                                        <label for="payment_date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                                        <input type="date" id="payment_date_to" name="date_to" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                        <select id="payment_method" name="payment_method" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                            <option value="all">All Methods</option>
                                            <option value="cash">Cash</option>
                                            <option value="gcash">GCash</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                                        <select id="payment_status" name="payment_status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                            <option value="all">All Statuses</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Pending">Pending</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="payment_purpose" class="block text-sm font-medium text-gray-700 mb-1">Payment Purpose</label>
                                    <select id="payment_purpose" name="purpose" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                        <option value="all">All Purposes</option>
                                        @foreach($paymentFees ?? [] as $fee)
                                            <option value="{{ $fee->purpose }}">{{ $fee->purpose }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Export Format</label>
                                    <div class="flex space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="export_format" value="pdf" class="form-radio text-[#c21313]" checked>
                                            <span class="ml-2">PDF</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="export_format" value="csv" class="form-radio text-[#c21313]">
                                            <span class="ml-2">CSV</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3 pt-4">
                                    <button type="button" onclick="hideReportForms()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                                        Cancel
                                    </button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 transition">
                                        <i class="fas fa-file-download mr-2"></i> Generate Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Membership Report Form -->
                    <div id="membership-form" class="hidden">
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Generate Membership Report</h3>
                            <form action="{{ route('admin.reports.generate') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="report_type" value="membership">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="member_status" class="block text-sm font-medium text-gray-700 mb-1">Member Status</label>
                                        <select id="member_status" name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                            <option value="all">All Statuses</option>
                                            <option value="active">Active</option>
                                            <option value="pending">Pending</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="member_role" class="block text-sm font-medium text-gray-700 mb-1">Member Role</label>
                                        <select id="member_role" name="role" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                            <option value="all">All Roles</option>
                                            <option value="member">Member</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="join_date_range" class="block text-sm font-medium text-gray-700 mb-1">Join Date Range</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <input type="date" id="join_date_from" name="join_date_from" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                        <input type="date" id="join_date_to" name="join_date_to" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Export Format</label>
                                    <div class="flex space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="export_format" value="pdf" class="form-radio text-[#c21313]" checked>
                                            <span class="ml-2">PDF</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="export_format" value="csv" class="form-radio text-[#c21313]">
                                            <span class="ml-2">CSV</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3 pt-4">
                                    <button type="button" onclick="hideReportForms()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                                        Cancel
                                    </button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 transition">
                                        <i class="fas fa-file-download mr-2"></i> Generate Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Evaluation Report Form -->
                    <div id="evaluation-form" class="hidden">
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Generate Event Evaluation Report</h3>
                            <form action="{{ route('admin.reports.generate') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="report_type" value="evaluation">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="evaluation_event_id" class="block text-sm font-medium text-gray-700 mb-1">Select Event <span class="text-red-500">*</span></label>
                                        <select id="evaluation_event_id" name="event_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm" required>
                                            <option value="">Select an event</option>
                                            <!-- This will be populated with events from the database -->
                                            @foreach($events ?? [] as $event)
                                                <option value="{{ $event->id }}">{{ $event->title }} ({{ date('M d, Y', strtotime($event->start_date_time)) }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="rating_type" class="block text-sm font-medium text-gray-700 mb-1">Rating Filter</label>
                                        <select id="rating_type" name="rating_type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c21313] focus:border-[#c21313] sm:text-sm">
                                            <option value="all">All Ratings</option>
                                            <option value="positive">Positive (4-5 stars)</option>
                                            <option value="neutral">Neutral (3 stars)</option>
                                            <option value="negative">Negative (1-2 stars)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Export Format</label>
                                    <div class="flex space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="export_format" value="pdf" class="form-radio text-[#c21313]" checked>
                                            <span class="ml-2">PDF</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="export_format" value="csv" class="form-radio text-[#c21313]">
                                            <span class="ml-2">CSV</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3 pt-4">
                                    <button type="button" onclick="hideReportForms()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                                        Cancel
                                    </button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-700 transition">
                                        <i class="fas fa-file-download mr-2"></i> Generate Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showReportForm(type) {
        // Hide all forms first
        document.getElementById('attendance-form').classList.add('hidden');
        document.getElementById('payment-form').classList.add('hidden');
        document.getElementById('membership-form').classList.add('hidden');
        document.getElementById('evaluation-form').classList.add('hidden');

        // Show the report forms container
        document.getElementById('report-forms').classList.remove('hidden');

        // Show the selected form
        document.getElementById(type + '-form').classList.remove('hidden');

        // Scroll to the form
        document.getElementById(type + '-form').scrollIntoView({ behavior: 'smooth' });
    }

    function hideReportForms() {
        document.getElementById('report-forms').classList.add('hidden');
    }
</script>
@endsection
