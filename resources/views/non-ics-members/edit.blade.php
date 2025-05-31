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
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Payments
                        </a>
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
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.non-ics-members.update', $nonIcsMember->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $nonIcsMember->email) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter email address" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alternative Email -->
                        <div>
                            <label for="alternative_email" class="block text-sm font-medium text-gray-700 mb-1">Alternative Email</label>
                            <input type="email" id="alternative_email" name="alternative_email" value="{{ old('alternative_email', $nonIcsMember->alternative_email) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter alternative email (optional)">
                            @error('alternative_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Full Name -->
                        <div>
                            <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" id="fullname" name="fullname" value="{{ old('fullname', $nonIcsMember->fullname) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter full name" required>
                            @error('fullname')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Student ID -->
                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                            <input type="text" id="student_id" name="student_id" value="{{ old('student_id', $nonIcsMember->student_id) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter student ID (optional)">
                            @error('student_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Course, Year & Section -->
                        <div>
                            <label for="course_year_section" class="block text-sm font-medium text-gray-700 mb-1">Course, Year & Section <span class="text-red-500">*</span></label>
                            <input type="text" id="course_year_section" name="course_year_section" value="{{ old('course_year_section', $nonIcsMember->course_year_section) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g., BSBA 2-A" required>
                            @error('course_year_section')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department/College</label>
                            <input type="text" id="department" name="department" value="{{ old('department', $nonIcsMember->department) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter department or college">
                            @error('department')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label for="mobile_no" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                            <input type="tel" id="mobile_no" name="mobile_no" value="{{ old('mobile_no', $nonIcsMember->mobile_no) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="09123456789" pattern="[0-9]{11}">
                            <p class="mt-1 text-xs text-gray-500">Enter 11-digit mobile number (optional)</p>
                            @error('mobile_no')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea id="address" name="address" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter address (optional)">{{ old('address', $nonIcsMember->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Membership Information -->
                        <div class="md:col-span-2 pt-4">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Membership Information</h2>
                        </div>

                        <!-- Membership Type -->
                        <div>
                            <label for="membership_type" class="block text-sm font-medium text-gray-700 mb-1">Membership Type</label>
                            <select id="membership_type" name="membership_type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Membership Type</option>
                                <option value="Regular" {{ old('membership_type', $nonIcsMember->membership_type) == 'Regular' ? 'selected' : '' }}>Regular</option>
                                <option value="Associate" {{ old('membership_type', $nonIcsMember->membership_type) == 'Associate' ? 'selected' : '' }}>Associate</option>
                                <option value="Honorary" {{ old('membership_type', $nonIcsMember->membership_type) == 'Honorary' ? 'selected' : '' }}>Honorary</option>
                            </select>
                            @error('membership_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Membership Expiry -->
                        <div>
                            <label for="membership_expiry" class="block text-sm font-medium text-gray-700 mb-1">Membership Expiry Date</label>
                            <input type="date" id="membership_expiry" name="membership_expiry" value="{{ old('membership_expiry', $nonIcsMember->membership_expiry ? date('Y-m-d', strtotime($nonIcsMember->membership_expiry)) : '') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('membership_expiry')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <select id="payment_status" name="payment_status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="None" {{ old('payment_status', $nonIcsMember->payment_status) == 'None' ? 'selected' : '' }}>None</option>
                                <option value="Pending" {{ old('payment_status', $nonIcsMember->payment_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Paid" {{ old('payment_status', $nonIcsMember->payment_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                            @error('payment_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea id="notes" name="notes" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter any additional notes (optional)">{{ old('notes', $nonIcsMember->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-white bg-[#c21313] hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i> Update Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
