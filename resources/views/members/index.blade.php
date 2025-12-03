<x-app-layout>
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('css/members.css') }}">
        <style>
            /* Ensure navigation is visible */
            nav.bg-white {
                display: block !important;
            }
            .hidden.space-x-8.sm\:flex {
                display: flex !important;
            }
            .hidden.sm\:flex.sm\:items-center {
                display: flex !important;
            }

            /* Officer section styles */
            .officer-section {
                margin-top: 5px;
            }

            .dropdown-header {
                padding: 0.5rem 1rem;
                font-size: 0.75rem;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
            }
        </style>
    </x-slot>

    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get all dropdown buttons
                const dropdownButtons = document.querySelectorAll('.dropdown-button');

                // Add click event to each dropdown button
                dropdownButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        // Toggle the 'show' class on the parent dropdown
                        this.parentElement.classList.toggle('show');

                        // Close other dropdowns
                        dropdownButtons.forEach(otherButton => {
                            if (otherButton !== button) {
                                otherButton.parentElement.classList.remove('show');
                            }
                        });
                    });
                });

                // Close dropdowns when clicking outside
                document.addEventListener('click', function(event) {
                    if (!event.target.closest('.table-dropdown')) {
                        dropdownButtons.forEach(button => {
                            button.parentElement.classList.remove('show');
                        });
                    }
                });
            });
        </script>
    </x-slot>
    <div class="min-h-screen bg-gray-50">
        <div class="admin-container">
            @if($isAdmin)
            <!-- Admin Header -->
            <div class="admin-header">
                <h1 class="admin-title">User Management</h1>
                <p class="admin-subtitle">Manage users, roles, and permissions</p>
            </div>

            <!-- Stats Cards -->
            <div class="admin-stats">
                <div class="admin-stat-card blue">
                    <div>
                        <p class="admin-stat-label">Total Users</p>
                        <h3 class="admin-stat-value">{{ $totalUsers }}</h3>
                    </div>
                    <div class="admin-stat-icon admin-stat-icon-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <div class="admin-stat-card purple">
                    <div>
                        <p class="admin-stat-label">Superadmins</p>
                        <h3 class="admin-stat-value">{{ $totalAdmins }}</h3>
                    </div>
                    <div class="admin-stat-icon admin-stat-icon-purple">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>

                <div class="admin-stat-card cyan">
                    <div>
                        <p class="admin-stat-label">Officers</p>
                        <h3 class="admin-stat-value">{{ $totalOfficers }}</h3>
                    </div>
                    <div class="admin-stat-icon admin-stat-icon-cyan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>

                <div class="admin-stat-card green">
                    <div>
                        <p class="admin-stat-label">Members</p>
                        <h3 class="admin-stat-value">{{ $totalMembers }}</h3>
                    </div>
                    <div class="admin-stat-icon admin-stat-icon-green">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>

                <div class="admin-stat-card red">
                    <div>
                        <p class="admin-stat-label">Active Users</p>
                        <h3 class="admin-stat-value">{{ $activeUsers }}</h3>
                    </div>
                    <div class="admin-stat-icon admin-stat-icon-red">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            @else
            <!-- Regular User Header -->
            <div class="admin-header">
                <h1 class="admin-title">Club Members</h1>
                <p class="admin-subtitle">Manage your membership database</p>
            </div>
            @endif

            <!-- Search and filters -->
            <div class="admin-search-bar">
                <form action="{{ route('members.index') }}" method="GET" class="w-full flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[240px]">
                        <input type="text" name="search" id="search" placeholder="Search by name, email, ID..." value="{{ request('search') }}"
                            class="admin-search-input w-full">
                    </div>

                    @if($isAdmin)
                    <div class="min-w-[120px]">
                        <select name="role" class="admin-select w-full">
                            <option value="" {{ !request('role') ? 'selected' : '' }}>All Roles</option>
                            <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="Secretary" {{ request('role') == 'Secretary' ? 'selected' : '' }}>Secretary</option>
                            <option value="Treasurer" {{ request('role') == 'Treasurer' ? 'selected' : '' }}>Treasurer</option>
                            <option value="Auditor" {{ request('role') == 'Auditor' ? 'selected' : '' }}>Auditor</option>
                            <option value="PIO" {{ request('role') == 'PIO' ? 'selected' : '' }}>PIO</option>
                            <option value="BM" {{ request('role') == 'BM' ? 'selected' : '' }}>Business Manager</option>
                            <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member</option>
                        </select>
                    </div>
                    @endif

                    <div class="min-w-[120px]">
                        <select name="status" class="admin-select w-full">
                            <option value="" {{ !request('status') ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="min-w-[120px]">
                        <select name="per_page" class="admin-select w-full">
                            <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>

                    <button type="submit" class="admin-filter-button whitespace-nowrap">
                        Apply Filters
                    </button>
                </form>
            </div>

            @if(session('success'))
                <div class="border-l-4 border-green-500 bg-green-50 p-4 mb-6 rounded-md animate-fadeIn" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="admin-table-container">
                @if($members->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Member</th>
                                    <th>Role</th>
                                    <th class="text-center">Active Status</th>
                                    <th>Status</th>
                                    <th>Email Verified</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>{{ $member->studentnumber ?? $member->id }}</td>
                                        <td>
                                            <div class="admin-user-info">
                                                <div class="admin-avatar">
                                                    {{ strtoupper(substr($member->firstname ?? $member->name, 0, 1)) }}
                                                </div>
                                                <div class="admin-user-details">
                                                    <div class="admin-user-name">
                                                        @if($member->firstname && $member->lastname)
                                                            {{ $member->firstname }} {{ $member->lastname }}
                                                        @else
                                                            {{ $member->name }}
                                                        @endif
                                                    </div>
                                                    <div class="admin-user-email">{{ $member->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-dropdown">
                                                <button type="button" class="dropdown-button
                                                    {{ in_array(strtolower($member->user_role), ['super_admin','superadmin']) ? 'admin' : (in_array($member->user_role, ['Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM']) ? 'officer' : 'member') }}">
                                                    @if(in_array(strtolower($member->user_role), ['super_admin','superadmin']))
                                                        Super Admin
                                                    @elseif(in_array($member->user_role, ['Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM']))
                                                        {{ $member->user_role === 'BM' ? 'Business Manager' : $member->user_role }}
                                                    @else
                                                        Member
                                                    @endif
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-content">
                                                    @if(!in_array(strtolower($member->user_role), ['super_admin','superadmin']))
                                                    <a href="#" class="admin-option" onclick="event.preventDefault(); document.getElementById('role-superadmin-{{ $member->id }}').submit();">Super Admin</a>
                                                    @endif

                                                    @if(!in_array($member->user_role, ['Secretary', 'Treasurer', 'Auditor', 'PIO', 'BM']))
                                                    <div class="officer-section">
                                                        <div class="dropdown-divider"></div>
                                                        <div class="dropdown-header">Make Officer As:</div>
                                                        <a href="#" onclick="event.preventDefault(); document.getElementById('role-secretary-{{ $member->id }}').submit();">Secretary</a>
                                                        <a href="#" onclick="event.preventDefault(); document.getElementById('role-pio-{{ $member->id }}').submit();">PIO</a>
                                                        <a href="#" onclick="event.preventDefault(); document.getElementById('role-treasurer-{{ $member->id }}').submit();">Treasurer</a>
                                                        <a href="#" onclick="event.preventDefault(); document.getElementById('role-auditor-{{ $member->id }}').submit();">Auditor</a>
                                                        <a href="#" onclick="event.preventDefault(); document.getElementById('role-bm-{{ $member->id }}').submit();">Business Manager</a>
                                                    </div>
                                                    @endif

                                                    @if($member->user_role !== 'member')
                                                    <a href="#" class="member-option" onclick="event.preventDefault(); document.getElementById('role-member-{{ $member->id }}').submit();">Member</a>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Role Forms -->
                                            <form action="{{ route('members.updateRole', $member) }}" method="POST" id="role-superadmin-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" value="super_admin">
                                            </form>

                                            <!-- Officer role forms -->
                                            <form action="{{ route('members.updateRole', $member) }}" method="POST" id="role-secretary-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" value="Secretary">
                                            </form>

                                            <form action="{{ route('members.updateRole', $member) }}" method="POST" id="role-pio-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" value="PIO">
                                            </form>

                                            <form action="{{ route('members.updateRole', $member) }}" method="POST" id="role-treasurer-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" value="Treasurer">
                                            </form>

                                            <form action="{{ route('members.updateRole', $member) }}" method="POST" id="role-auditor-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" value="Auditor">
                                            </form>

                                            <form action="{{ route('members.updateRole', $member) }}" method="POST" id="role-bm-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" value="BM">
                                            </form>

                                            <form action="{{ route('members.updateRole', $member) }}" method="POST" id="role-member-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" value="member">
                                            </form>


                                        </td>
                                        <td class="text-center">
                                            <span class="status-dot {{ strtolower($member->status) }}"></span>
                                        </td>
                                        <td>
                                            <div class="table-dropdown">
                                                <button type="button" class="dropdown-button">
                                                    {{ ucfirst($member->status) }} <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-content">
                                                    <a href="#" onclick="event.preventDefault(); document.getElementById('status-pending-{{ $member->id }}').submit();">Pending</a>
                                                    <a href="#" onclick="event.preventDefault(); document.getElementById('status-active-{{ $member->id }}').submit();">Active</a>
                                                    <a href="#" onclick="event.preventDefault(); document.getElementById('status-rejected-{{ $member->id }}').submit();">Rejected</a>
                                                </div>
                                            </div>

                                            <form action="{{ route('members.updateStatus', $member) }}" method="POST" id="status-pending-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="pending">
                                            </form>

                                            <form action="{{ route('members.updateStatus', $member) }}" method="POST" id="status-active-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="active">
                                            </form>

                                            <form action="{{ route('members.updateStatus', $member) }}" method="POST" id="status-rejected-{{ $member->id }}" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                            </form>
                                        </td>
                                        <td>
                                            @if($member->email_verified_at)
                                                <span class="text-xs text-green-600">{{ $member->email_verified_at->format('M d, Y g:i A') }}</span>
                                            @else
                                                <span class="text-xs text-gray-500">Not verified</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center justify-center space-x-4">
                                                <a href="{{ route('members.show', $member) }}" class="admin-action-button view" title="View Details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('members.edit', $member) }}" class="admin-action-button edit" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('members.destroy', $member) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="admin-action-button delete" title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100 bg-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            @if ($members->hasPages())
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Showing <span class="font-medium">{{ $members->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $members->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $members->total() }}</span> results
                                    </p>
                                </div>

                                <div>
                                    <nav class="flex items-center space-x-1" aria-label="Pagination">
                                        {{-- Previous Page Link --}}
                                        @if ($members->onFirstPage())
                                            <span class="px-3 py-2 rounded text-sm text-gray-300 bg-gray-50 cursor-not-allowed">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @else
                                            <a href="{{ $members->appends(request()->except('page'))->previousPageUrl() }}" rel="prev" class="px-3 py-2 rounded text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach ($members->getUrlRange(max($members->currentPage() - 2, 1), min($members->currentPage() + 2, $members->lastPage())) as $page => $url)
                                            @if ($page == $members->currentPage())
                                                <span aria-current="page" class="px-3 py-2 rounded text-sm font-medium bg-red-600 text-white">
                                                    {{ $page }}
                                                </span>
                                            @else
                                                <a href="{{ $members->appends(request()->except('page'))->url($page) }}" class="px-3 py-2 rounded text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                                    {{ $page }}
                                                </a>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        @if ($members->hasMorePages())
                                            <a href="{{ $members->appends(request()->except('page'))->nextPageUrl() }}" rel="next" class="px-3 py-2 rounded text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="px-3 py-2 rounded text-sm text-gray-300 bg-gray-50 cursor-not-allowed">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </nav>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="py-16 px-4 text-center">
                        <div class="bg-gray-50 inline-flex p-6 rounded-full mb-4">
                            <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">No users found</h3>
                        <p class="mt-2 text-gray-500 max-w-md mx-auto">No users match your current search criteria. Try adjusting your filters or add a new user to get started.</p>
                        <div class="mt-8">
                            <a href="{{ route('members.create') }}" class="inline-flex items-center px-5 py-3 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition duration-200">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add New User
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>