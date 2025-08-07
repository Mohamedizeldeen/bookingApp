<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BookingApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        
        /* Notification Styles */
        .notification-dropdown {
            transform: translateY(-10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease-in-out;
        }
        .notification-dropdown.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }
        .notification-item:hover {
            background-color: #f3f4f6;
        }
        .notification-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
    <script>
        function copyBookingLink() {
            const linkInput = document.querySelector('input[readonly]');
            linkInput.select();
            linkInput.setSelectionRange(0, 99999); // For mobile devices
            
            navigator.clipboard.writeText(linkInput.value).then(function() {
                // Show success message
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('bg-green-600');
                button.classList.remove('bg-purple-600');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-purple-600');
                }, 2000);
            });
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="ml-2 text-xl font-bold text-gray-900">BookingApp</span>
                    <span class="ml-4 text-sm text-gray-500">{{ auth()->user()->company->company_name }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button type="button" id="notificationButton" class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span id="notificationBadge" class="absolute -top-0.5 -right-0.5 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center notification-badge hidden">0</span>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div id="notificationDropdown" class="notification-dropdown absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                    <button type="button" id="markAllRead" class="text-xs text-purple-600 hover:text-purple-800">Mark all read</button>
                                </div>
                                <div id="notificationList" class="space-y-2 max-h-64 overflow-y-auto">
                                    <p class="text-sm text-gray-500 text-center py-4">No new notifications</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <span class="text-gray-700">Welcome, {{ auth()->user()->name }}</span>
                    @if(auth()->user()->role === 'admin')
                        <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded">Admin</span>
                    @else
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Staff</span>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 font-medium">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Success/Error Messages -->
        @if(session('status'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Dashboard Header (responsive) -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">Manage your bookings and business operations</p>
        </div>

        <!-- Stats Cards (enhanced responsive grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            @if(auth()->user()->role === 'admin')
                <!-- Admin Stats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ auth()->user()->company->customers->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Staff Stats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Assigned</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ auth()->user()->company->appointments()->where('assigned_user_id', auth()->user()->id)->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    @if(auth()->user()->role === 'admin')
                                        Today's Appointments
                                    @else
                                        My Today's Appointments
                                    @endif
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    @if(auth()->user()->role === 'admin')
                                        {{ auth()->user()->company->appointments()->whereDate('appointment_date', today())->count() }}
                                    @else
                                        {{ auth()->user()->company->appointments()->whereDate('appointment_date', today())->where('assigned_user_id', auth()->user()->id)->count() }}
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->role === 'admin')
                <!-- Admin Stats Continue -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Services</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ auth()->user()->company->services->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Team Members</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ auth()->user()->company->users->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Staff Stats Continue -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ auth()->user()->company->appointments()->where('assigned_user_id', auth()->user()->id)->where('status', 'scheduled')->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ auth()->user()->company->appointments()->where('assigned_user_id', auth()->user()->id)->where('status', 'completed')->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Appointments -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            @if(auth()->user()->role === 'admin')
                                Recent Appointments
                            @else
                                My Recent Appointments
                            @endif
                        </h3>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        @if(auth()->user()->role === 'admin')
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        if(auth()->user()->role === 'admin') {
                                            $recentAppointments = auth()->user()->company->appointments()
                                                ->with(['customer', 'service', 'assignedUser'])
                                                ->latest()
                                                ->take(5)
                                                ->get();
                                        } else {
                                            $recentAppointments = auth()->user()->company->appointments()
                                                ->with(['customer', 'service', 'assignedUser'])
                                                ->where('assigned_user_id', auth()->user()->id)
                                                ->latest()
                                                ->take(5)
                                                ->get();
                                        }
                                    @endphp
                                    
                                    @forelse($recentAppointments as $appointment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $appointment->customer->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $appointment->service->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $appointment->appointment_date->format('M j, Y g:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($appointment->status === 'completed') bg-green-100 text-green-800
                                                @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                                @elseif($appointment->status === 'scheduled') bg-yellow-100 text-yellow-800
                                                @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->role === 'admin')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $appointment->assignedUser ? $appointment->assignedUser->name : 'Unassigned' }}
                                            </td>
                                        @endif
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->role === 'admin' ? '5' : '4' }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            @if(auth()->user()->role === 'admin')
                                                No appointments found
                                            @else
                                                No appointments assigned to you yet
                                            @endif
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Panel -->
            @if(auth()->user()->role === 'admin')
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Admin Panel</h3>
                        
                        <!-- Unique Booking Link -->
                        <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                            <h4 class="text-sm font-semibold text-purple-900 mb-2">📅 Your Booking Link</h4>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <input type="text" readonly 
                                           value="{{ auth()->user()->company->getShareableBookingUrl() }}"
                                           class="flex-1 px-2 py-1 text-xs bg-white border border-purple-300 rounded text-purple-800">
                                    <button onclick="copyBookingLink()" 
                                            class="px-3 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700">
                                        Copy
                                    </button>
                                </div>
                                <p class="text-xs text-purple-700">Share this link on social media for easy customer booking!</p>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mb-6 grid grid-cols-2 gap-2">
                            <a href="{{ route('services.index') }}" 
                               class="text-center px-3 py-2 bg-blue-100 text-blue-800 text-xs rounded hover:bg-blue-200">
                                Manage Services
                            </a>
                            <a href="{{ route('appointments.today') }}" 
                               class="text-center px-3 py-2 bg-green-100 text-green-800 text-xs rounded hover:bg-green-200">
                                Today's Schedule
                            </a>
                            <a href="{{ route('customers.index') }}" 
                               class="text-center px-3 py-2 bg-purple-100 text-purple-800 text-xs rounded hover:bg-purple-200">
                                Manage Customers
                            </a>
                            <a href="{{ route('team.index') }}" 
                               class="text-center px-3 py-2 bg-blue-100 text-blue-800 text-xs rounded hover:bg-blue-200">
                                Manage Team
                            </a>
                        </div>
                        
                        <!-- Add New Staff Member Form -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Add Staff Member</h4>
                            <p class="text-xs text-gray-500 mb-3">New users will be automatically assigned as staff members with appointment management access.</p>
                            <form action="{{ route('add.user') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <input type="text" name="name" placeholder="Full Name" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <input type="email" name="email" placeholder="Email Address" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <input type="password" name="password" placeholder="Password" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <button type="submit" 
                                        class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    Add Staff Member
                                </button>
                            </form>
                        </div>

                        <!-- Current Team Members -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Team Members ({{ auth()->user()->company->users->count() }})</h4>
                            <div class="space-y-2">
                                @foreach(auth()->user()->company->users as $user)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded 
                                        @if($user->role === 'admin') bg-purple-100 text-purple-800 
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $user->role === 'admin' ? 'Admin' : 'Staff' }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Staff Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Staff Dashboard</h3>
                        
                        <!-- Quick Actions for Staff -->
                        <div class="space-y-3">
                            <a href="{{ route('appointments.my') }}" 
                               class="block w-full text-center px-4 py-3 bg-purple-100 text-purple-800 rounded-lg hover:bg-purple-200">
                                <div class="font-semibold">My Appointments</div>
                                <div class="text-sm">{{ auth()->user()->company->appointments()->where('assigned_user_id', auth()->user()->id)->count() }} assigned to you</div>
                            </a>
                            
                            <a href="{{ route('appointments.today') }}" 
                               class="block w-full text-center px-4 py-3 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200">
                                <div class="font-semibold">Today's Schedule</div>
                                <div class="text-sm">{{ auth()->user()->company->appointments()->whereDate('appointment_date', today())->where('assigned_user_id', auth()->user()->id)->count() }} today</div>
                            </a>
                            
                            <a href="{{ route('appointments.create') }}" 
                               class="block w-full text-center px-4 py-3 bg-green-100 text-green-800 rounded-lg hover:bg-green-200">
                                <div class="font-semibold">Create Appointment</div>
                                <div class="text-sm">Book for customers</div>
                            </a>
                            
                            <a href="{{ route('appointments.index') }}" 
                               class="block w-full text-center px-4 py-3 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200">
                                <div class="font-semibold">All Appointments</div>
                                <div class="text-sm">View company schedule</div>
                            </a>
                        </div>
                        
                        <!-- Staff Info -->
                        <div class="mt-6 p-3 bg-gray-50 rounded">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Your Role</h4>
                            <p class="text-xs text-gray-600">As a staff member, you can view and confirm appointments, create new bookings for customers, and manage your daily schedule.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Notification JavaScript -->
    <script>
        let notificationInterval;
        let isDropdownOpen = false;

        // Initialize notifications
        document.addEventListener('DOMContentLoaded', function() {
            fetchNotifications();
            startNotificationPolling();
            
            // Notification dropdown toggle
            const notificationButton = document.getElementById('notificationButton');
            const notificationDropdown = document.getElementById('notificationDropdown');
            
            notificationButton.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleNotificationDropdown();
            });
            
            // Mark all as read
            document.getElementById('markAllRead').addEventListener('click', function() {
                markAllNotificationsAsRead();
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationDropdown.contains(e.target) && !notificationButton.contains(e.target)) {
                    closeNotificationDropdown();
                }
            });
        });

        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            if (isDropdownOpen) {
                closeNotificationDropdown();
            } else {
                dropdown.classList.add('show');
                isDropdownOpen = true;
                fetchNotifications(); // Refresh when opening
            }
        }

        // Mobile notification handler
        function handleMobileNotifications() {
            // For mobile, we'll show a simple alert with notification count
            // In a more advanced implementation, you could show a modal or redirect to a notifications page
            fetch('/notifications/unread', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    const message = `You have ${data.count} new notification${data.count > 1 ? 's' : ''}`;
                    if (confirm(message + '. Would you like to mark all as read?')) {
                        markAllNotificationsAsRead();
                    }
                } else {
                    alert('No new notifications');
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                alert('Error loading notifications');
            });
        }

        function closeNotificationDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.remove('show');
            isDropdownOpen = false;
        }

        function startNotificationPolling() {
            // Check for new notifications every 30 seconds
            notificationInterval = setInterval(fetchNotifications, 30000);
        }

        function fetchNotifications() {
            fetch('/notifications/unread', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationUI(data.notifications, data.count);
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
        }

        function updateNotificationUI(notifications, count) {
            const badge = document.getElementById('notificationBadge');
            const mobileBadge = document.getElementById('mobileNotificationBadge');
            const notificationList = document.getElementById('notificationList');
            
            // Update both desktop and mobile badges
            if (count > 0) {
                const displayCount = count > 99 ? '99+' : count;
                if (badge) {
                    badge.textContent = displayCount;
                    badge.classList.remove('hidden');
                }
                if (mobileBadge) {
                    mobileBadge.textContent = displayCount;
                    mobileBadge.classList.remove('hidden');
                    mobileBadge.classList.add('flex');
                }
            } else {
                if (badge) {
                    badge.classList.add('hidden');
                }
                if (mobileBadge) {
                    mobileBadge.classList.add('hidden');
                    mobileBadge.classList.remove('flex');
                }
            }
            
            // Update notification list
            if (notifications.length === 0) {
                notificationList.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">No new notifications</p>';
            } else {
                notificationList.innerHTML = notifications.map(notification => 
                    createNotificationHTML(notification)
                ).join('');
            }
        }

        function createNotificationHTML(notification) {
            const timeAgo = getTimeAgo(new Date(notification.created_at));
            const iconClass = getNotificationIcon(notification.event);
            
            return `
                <div class="notification-item p-3 border-l-4 border-purple-500 bg-purple-50 rounded cursor-pointer" 
                     onclick="markNotificationAsRead(${notification.id})">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${iconClass}
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                            <p class="text-sm text-gray-700">${notification.message}</p>
                            <p class="text-xs text-gray-500 mt-1">${timeAgo}</p>
                        </div>
                    </div>
                </div>
            `;
        }

        function getNotificationIcon(event) {
            switch(event) {
                case 'appointment_assigned':
                    return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>';
                case 'appointment_confirmed':
                    return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                case 'appointment_completed':
                    return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                case 'appointment_cancelled':
                    return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                default:
                    return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            }
        }

        function getTimeAgo(date) {
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
            return `${Math.floor(diffInSeconds / 86400)}d ago`;
        }

        function markNotificationAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchNotifications(); // Refresh the list
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        function markAllNotificationsAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchNotifications(); // Refresh the list
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        }

        // Clear interval when page is unloaded
        window.addEventListener('beforeunload', function() {
            if (notificationInterval) {
                clearInterval(notificationInterval);
            }
        });
    </script>
</body>
</html>