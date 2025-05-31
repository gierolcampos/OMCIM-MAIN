@extends('layouts.app')
@section('content')
<style>
    /* Custom Dashboard Styles - Member Stats */
    .dashboard-header {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,.075)' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.3;
    }

    .stat-card {
        transition: all 0.3s ease;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .card-title {
        position: relative;
        display: inline-block;
        padding-bottom: 0.5rem;
    }

    .card-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40%;
        height: 3px;
        background: linear-gradient(to right, #c21313, rgba(194, 19, 19, 0.5));
        border-radius: 3px;
    }

    .gradient-red {
        background: linear-gradient(135deg, #c21313 0%, #e65758 100%);
    }

    .member-table {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .member-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
    }

    .table-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        background-color: #e5e7eb;
        color: #4b5563;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Dashboard Header -->
        <div class="dashboard-header gradient-red shadow-xl mb-8 overflow-hidden">
            <div class="px-6 py-10 md:px-10 md:py-14">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Member Statistics</h2>
                        <p class="text-red-100 text-lg">Detailed analytics about organization members</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <div class="px-4 py-3 bg-white bg-opacity-10 backdrop-blur-sm rounded-lg text-white">
                            <div class="text-xs uppercase tracking-wider text-red-200 mb-1">Total Members</div>
                            <div class="text-2xl font-bold">{{ $users->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Stats Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 fade-in">
            <h3 class="card-title text-xl font-bold text-gray-800 mb-8">Member Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="stat-card bg-gradient-to-br from-green-50 to-white p-6 rounded-xl border border-green-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">Total Members</h4>
                            <p class="text-3xl font-bold text-green-600">{{ $users->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl border border-blue-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">Active Members</h4>
                            <p class="text-3xl font-bold text-blue-600">{{ $users->where('status', 'active')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-gradient-to-br from-red-50 to-white p-6 rounded-xl border border-red-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 rounded-full bg-red-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm uppercase tracking-wider text-gray-500 font-medium mb-1">Admins</h4>
                            <p class="text-3xl font-bold text-red-600">{{ $users->whereIn('user_role', ['superadmin', 'officer'])->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member List -->
        <div class="bg-white rounded-xl shadow-sm p-6 fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h3 class="card-title text-xl font-bold text-gray-800">Member List</h3>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('members.index') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                        View All Members
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 member-table">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($users->take(10) as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="table-avatar shadow-sm">
                                                {{ substr($user->firstname, 0, 1) }}{{ substr($user->lastname, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $user->firstname }} {{ $user->lastname }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $user->studentnumber }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $user->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $user->isAdmin() ? 'Admin' : 'Member' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-{{ $user->status === 'active' ? 'green' : 'gray' }}-100 text-{{ $user->status === 'active' ? 'green' : 'gray' }}-800">
                                        {{ ucfirst($user->status ?? 'inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection