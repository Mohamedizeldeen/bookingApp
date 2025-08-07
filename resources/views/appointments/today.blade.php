<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Today's Appointments - BookingApp</title>
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
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="ml-2 text-xl font-bold text-gray-900">BookingApp</span>
                    <span class="ml-4 text-sm text-gray-500">{{ auth()->user()->company->company_name }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                    <a href="{{ route('appointments.index') }}" class="text-gray-500 hover:text-gray-700">All Appointments</a>
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->role === 'admin')
                        <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded">Admin</span>
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
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Today's Schedule</h1>
                    <p class="text-gray-600">{{ now()->format('l, F j, Y') }} - {{ $todayAppointments->count() }} appointments</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('appointments.create') }}" 
                       class="bg-purple-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        Book Appointment
                    </a>
                    <a href="{{ route('appointments.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        View All
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Today</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $todayAppointments->count() }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Confirmed</dt>
                                <dd class="text-lg font-medium text-green-600">{{ $todayAppointments->where('status', 'confirmed')->count() }}</dd>
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
                                <dd class="text-lg font-medium text-yellow-600">{{ $todayAppointments->where('status', 'scheduled')->count() }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                <dd class="text-lg font-medium text-blue-600">{{ $todayAppointments->where('status', 'completed')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments List -->
        @if($todayAppointments->count() > 0)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Today's Appointments</h3>
                    
                    <div class="space-y-4">
                        @foreach($todayAppointments->sortBy('appointment_date') as $appointment)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                    <span class="text-purple-600 font-semibold text-sm">
                                                        {{ substr($appointment->customer->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-900">{{ $appointment->customer->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $appointment->service->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $appointment->customer->email }} • {{ $appointment->customer->phone }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-gray-900">{{ $appointment->appointment_date->format('g:i A') }}</p>
                                                <p class="text-xs text-gray-500">{{ $appointment->service->duration }} min • ${{ number_format($appointment->service->price, 2) }}</p>
                                                @if($appointment->assignedUser)
                                                    <p class="text-xs text-gray-500">Assigned: {{ $appointment->assignedUser->name }}</p>
                                                @endif
                                            </div>
                                            <div class="flex flex-col space-y-2">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($appointment->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                                    @elseif($appointment->status === 'scheduled') bg-yellow-100 text-yellow-800
                                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        @if($appointment->notes)
                                            <div class="mt-2 text-sm text-gray-600">
                                                <strong>Notes:</strong> {{ $appointment->notes }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="ml-4 flex space-x-2">
                                        @if(auth()->user()->isAdmin() && in_array($appointment->status, ['scheduled', 'confirmed']))
                                            <button onclick="showAssignModal({{ $appointment->id }}, '{{ $appointment->customer->name }}', '{{ $appointment->assignedUser ? $appointment->assignedUser->name : 'Unassigned' }}')" 
                                                    class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                                Assign
                                            </button>
                                        @endif
                                        
                                        {{-- Staff can only manage appointments assigned to them, Admins can manage all --}}
                                        @php
                                            $canManageAppointment = auth()->user()->isAdmin() || 
                                                                  $appointment->assigned_user_id === auth()->user()->id;
                                        @endphp
                                        
                                        @if($canManageAppointment && $appointment->status === 'scheduled')
                                            <form action="{{ route('appointments.confirm', $appointment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    Confirm
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($canManageAppointment && $appointment->status === 'confirmed')
                                            <form action="{{ route('appointments.complete', $appointment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                    Complete
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($canManageAppointment && in_array($appointment->status, ['scheduled', 'confirmed']))
                                            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?')" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                        
                                        {{-- Show message if staff can't manage this appointment --}}
                                        @if(!$canManageAppointment && !auth()->user()->isAdmin())
                                            <span class="text-xs text-gray-400 italic">
                                                Not assigned to you
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments today</h3>
                <p class="mt-1 text-sm text-gray-500">You have a free day! No appointments scheduled for today.</p>
                <div class="mt-6">
                    <a href="{{ route('appointments.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Book New Appointment
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Assign Staff Modal -->
    <div id="assignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Staff to Appointment</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        Customer: <span id="assignCustomerName" class="font-medium"></span>
                    </p>
                    <p class="text-sm text-gray-500 mb-4">
                        Currently assigned: <span id="assignCurrentStaff" class="font-medium"></span>
                    </p>
                    
                    <form id="assignForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label for="assigned_user_id" class="block text-sm font-medium text-gray-700 mb-2">Select Staff Member:</label>
                            <select name="assigned_user_id" id="assigned_user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="">Unassigned</option>
                                @foreach(\App\Models\User::where('company_id', auth()->user()->company_id)
                                    ->whereIn('role', ['admin', 'staff', 'employee'])
                                    ->orderBy('name')
                                    ->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="items-center px-4 py-3">
                            <button type="submit" class="px-4 py-2 bg-purple-500 text-white text-base font-medium rounded-md w-24 hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-300 mr-2">
                                Assign
                            </button>
                            <button type="button" onclick="hideAssignModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showAssignModal(appointmentId, customerName, currentStaff) {
            document.getElementById('assignCustomerName').textContent = customerName;
            document.getElementById('assignCurrentStaff').textContent = currentStaff;
            document.getElementById('assignForm').action = '/appointments/' + appointmentId + '/assign';
            document.getElementById('assignModal').style.display = 'block';
        }

        function hideAssignModal() {
            document.getElementById('assignModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('assignModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideAssignModal();
            }
        });

        // Handle form submission
        document.getElementById('assignForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'An error occurred while assigning staff.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while assigning staff.');
            });
        });
    </script>
</body>
</html>
