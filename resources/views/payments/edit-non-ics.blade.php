@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit Non-ICS Member</h1>
                        <p class="text-gray-600 mt-1">Update member information</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Details
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

                <form action="{{ route('admin.non-ics-members.update', $nonIcsMember->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email', $nonIcsMember->email) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alternative Email -->
                            <div>
                                <label for="alternative_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Alternative Email
                                </label>
                                <input type="email" id="alternative_email" name="alternative_email" value="{{ old('alternative_email', $nonIcsMember->alternative_email ?? '') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter alternative email (optional)">
                            </div>

                            <!-- Full Name -->
                            <div>
                                <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="fullname" name="fullname" value="{{ old('fullname', $nonIcsMember->fullname) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                @error('fullname')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Student ID -->
                            <div>
                                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Student ID
                                </label>
                                <input type="text" id="student_id" name="student_id" value="{{ old('student_id', $nonIcsMember->student_id ?? '') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter student ID (optional)">
                            </div>

                            <!-- Course, Year & Section -->
                            <div>
                                <label for="course_year_section" class="block text-sm font-medium text-gray-700 mb-1">
                                    Course, Year & Section <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="course_year_section" name="course_year_section" value="{{ old('course_year_section', $nonIcsMember->course_year_section) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                @error('course_year_section')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Department/College -->
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-1">
                                    Department/College
                                </label>
                                <input type="text" id="department" name="department" value="{{ old('department', $nonIcsMember->department ?? '') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter department or college">
                            </div>

                            <!-- Mobile Number -->
                            <div>
                                <label for="mobile_no" class="block text-sm font-medium text-gray-700 mb-1">
                                    Mobile Number
                                </label>
                                <input type="text" id="mobile_no" name="mobile_no" value="{{ old('mobile_no', $nonIcsMember->mobile_no) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter 11-digit mobile number (optional)">
                                <p class="mt-1 text-xs text-gray-500">Enter 11-digit mobile number (optional)</p>
                                @error('mobile_no')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Address
                                </label>
                                <textarea id="address" name="address" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter address (optional)">{{ old('address', $nonIcsMember->address ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Membership Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Membership Type -->
                            <div>
                                <label for="membership_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Membership Type
                                </label>
                                <select id="membership_type" name="membership_type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select Membership Type</option>
                                    <option value="Regular" {{ old('membership_type', $nonIcsMember->membership_type ?? '') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="Associate" {{ old('membership_type', $nonIcsMember->membership_type ?? '') == 'Associate' ? 'selected' : '' }}>Associate</option>
                                    <option value="Honorary" {{ old('membership_type', $nonIcsMember->membership_type ?? '') == 'Honorary' ? 'selected' : '' }}>Honorary</option>
                                </select>
                            </div>

                            <!-- Membership Expiry Date -->
                            <div>
                                <label for="membership_expiry" class="block text-sm font-medium text-gray-700 mb-1">
                                    Membership Expiry Date
                                </label>
                                <input type="date" id="membership_expiry" name="membership_expiry" value="{{ old('membership_expiry', $nonIcsMember->membership_expiry ?? '') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <!-- Purpose -->
                            <div>
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">
                                    Purpose <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="purpose" name="purpose" value="{{ old('purpose', $nonIcsMember->purpose) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                @error('purpose')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">
                                    Amount (₱) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="total_price" name="total_price" value="{{ old('total_price', $nonIcsMember->total_price) }}" class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50" placeholder="0.00" required readonly>
                                    <p class="mt-1 text-xs text-gray-500">Amount is automatically set based on the selected purpose.</p>
                                </div>
                                @error('total_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Status -->
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Payment Status
                                </label>
                                <select id="payment_status" name="payment_status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="Pending" {{ old('payment_status', $nonIcsMember->payment_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Paid" {{ old('payment_status', $nonIcsMember->payment_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                                @error('payment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Notes
                                </label>
                                <textarea id="notes" name="notes" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter any additional notes (optional)">{{ old('notes', $nonIcsMember->notes ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-save mr-2"></i> Update Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submission handling
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Submit the form
            this.submit();
        });
    });
</script>
@endpush

@endsection
