<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Appointments - BookingApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-xl font-bold text-gray-900">BookingApp</span>
                    </a>
                    <span class="ml-4 text-sm text-gray-500">{{ auth()->user()->company->company_name }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('appointments.index') }}" class="text-gray-600 hover:text-gray-900">All Appointments</a>
                    <a href="{{ route('appointments.today') }}" class="text-gray-600 hover:text-gray-900">Today</a>
                    <a href="{{ route('appointments.my') }}" class="text-purple-600 font-medium">My Appointments</a>
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
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

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Appointments</h1>
                <p class="text-gray-600">Appointments assigned specifically to you</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('appointments.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Appointment
                </a>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Assigned</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $myAppointments->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

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
                                <dt class="text-sm font-medium text-gray-500 truncate">Scheduled</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ auth()->user()->company->appointments()->where('assigned_user_id', auth()->user()->id)->where('status', 'scheduled')->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Confirmed</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ auth()->user()->company->appointments()->where('assigned_user_id', auth()->user()->id)->where('status', 'confirmed')->count() }}
                                </dd>
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
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ auth()->user()->company->appointments()->where('assigned_user_id', auth()->user()->id)->where('status', 'completed')->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments List -->
        @if($myAppointments->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Your Assigned Appointments</h3>
                    
                    <div class="space-y-6">
                        @foreach($myAppointments as $appointment)
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4 mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $appointment->customer->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $appointment->service->name }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Date & Time</p>
                                                <p class="text-sm text-gray-900">{{ $appointment->appointment_date->format('M j, Y g:i A') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Status</p>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($appointment->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                                    @elseif($appointment->status === 'scheduled') bg-yellow-100 text-yellow-800
                                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        @if($appointment->customer->phone || $appointment->customer->email)
                                            <div class="mb-4">
                                                <p class="text-sm font-medium text-gray-500 mb-1">Contact Information</p>
                                                @if($appointment->customer->phone)
                                                    <p class="text-sm text-gray-900">📞 {{ $appointment->customer->phone }}</p>
                                                @endif
                                                @if($appointment->customer->email)
                                                    <p class="text-sm text-gray-900">✉️ {{ $appointment->customer->email }}</p>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        @if($appointment->notes)
                                            <div class="mb-4">
                                                <p class="text-sm font-medium text-gray-500 mb-1">Notes</p>
                                                <p class="text-sm text-gray-900">{{ $appointment->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="ml-6 flex flex-col space-y-2">
                                        @if($appointment->status === 'scheduled')
                                            <form action="{{ route('appointments.confirm', $appointment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-blue-700">
                                                    Confirm
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($appointment->status === 'confirmed')
                                            <form action="{{ route('appointments.complete', $appointment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full bg-green-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-green-700">
                                                    Complete
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                                            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?')" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full bg-red-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-red-700">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($myAppointments->hasPages())
                        <div class="mt-6">
                            {{ $myAppointments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments assigned</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any appointments assigned to you yet. Check back later or contact your admin.</p>
                <div class="mt-6">
                    <a href="{{ route('appointments.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        View All Appointments
                    </a>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
