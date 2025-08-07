@extends('layouts.app')

@section('title', 'Staff Dashboard')

@push('styles')
<style>
    .appointment-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .appointment-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.1);
    }
    .status-badge.scheduled { background-color: #fef3c7; color: #92400e; }
    .status-badge.confirmed { background-color: #d1fae5; color: #065f46; }
    .status-badge.completed { background-color: #dbeafe; color: #1e40af; }
    .status-badge.cancelled { background-color: #fee2e2; color: #991b1b; }
    .location-indicator.open { color: #10b981; }
    .location-indicator.closed { color: #ef4444; }
    .performance-metric {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Staff Dashboard</h1>
                    <p class="text-gray-600 mt-1">Your schedule and performance overview</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Appointment
                    </a>
                    <a href="{{ route('dashboard.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Main Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Performance Metrics (for staff users) -->
        @if(!empty($staffAnalytics))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="performance-metric rounded-lg p-6 text-center">
                <div class="text-3xl font-bold">{{ $staffAnalytics['total_appointments'] }}</div>
                <div class="text-sm opacity-90">Total Appointments</div>
            </div>
            <div class="performance-metric rounded-lg p-6 text-center">
                <div class="text-3xl font-bold">{{ $staffAnalytics['completed_appointments'] }}</div>
                <div class="text-sm opacity-90">Completed</div>
            </div>
            <div class="performance-metric rounded-lg p-6 text-center">
                <div class="text-3xl font-bold">${{ number_format($staffAnalytics['revenue_generated'], 2) }}</div>
                <div class="text-sm opacity-90">Revenue Generated</div>
            </div>
            <div class="performance-metric rounded-lg p-6 text-center">
                <div class="text-3xl font-bold">{{ $staffAnalytics['completion_rate'] }}%</div>
                <div class="text-sm opacity-90">Completion Rate</div>
            </div>
        </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Today's Schedule -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Today's Appointments -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Today's Schedule</h3>
                        <span class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</span>
                    </div>
                    
                    @if($todayAppointments->count() > 0)
                        <div class="space-y-4">
                            @foreach($todayAppointments as $appointment)
                            <div class="appointment-card border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-3 h-3 rounded-full bg-{{ $appointment->status === 'confirmed' ? 'green' : ($appointment->status === 'scheduled' ? 'yellow' : 'gray') }}-400"></div>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $appointment->customer->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $appointment->service->name }}</p>
                                                @if($appointment->location)
                                                <p class="text-xs text-gray-500">📍 {{ $appointment->location->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">{{ $appointment->appointment_time }}</p>
                                            <p class="text-xs text-gray-500">{{ $appointment->service->duration }}min</p>
                                        </div>
                                        <span class="status-badge {{ $appointment->status }} px-2 py-1 text-xs font-medium rounded-full">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="text-xs text-gray-500">
                                        📞 {{ $appointment->customer->phone }} • 📧 {{ $appointment->customer->email }}
                                    </div>
                                    <div class="flex space-x-2">
                                        @if($appointment->status === 'scheduled')
                                        <button onclick="confirmAppointment({{ $appointment->id }})" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200">
                                            Confirm
                                        </button>
                                        @endif
                                        @if($appointment->status === 'confirmed')
                                        <button onclick="completeAppointment({{ $appointment->id }})" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200">
                                            Complete
                                        </button>
                                        @endif
                                        @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                                        <button onclick="cancelAppointment({{ $appointment->id }})" class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200">
                                            Cancel
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-6xl mb-4">📅</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No appointments today</h3>
                            <p class="text-gray-500">You have a free day! Enjoy some well-deserved rest.</p>
                        </div>
                    @endif
                </div>

                <!-- Upcoming Appointments -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Upcoming Appointments</h3>
                        <a href="{{ route('appointments.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            View All →
                        </a>
                    </div>
                    
                    @if($upcomingAppointments->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingAppointments->take(5) as $appointment)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $appointment->customer->name }}</h4>
                                    <p class="text-xs text-gray-600">{{ $appointment->service->name }}</p>
                                    @if($appointment->location)
                                    <p class="text-xs text-gray-500">📍 {{ $appointment->location->name }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $appointment->appointment_date->format('M j') }}</p>
                                    <p class="text-xs text-gray-500">{{ $appointment->appointment_time }}</p>
                                    <span class="status-badge {{ $appointment->status }} px-1 py-0.5 text-xs font-medium rounded">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No upcoming appointments</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Location Status & Tools -->
            <div class="space-y-8">
                <!-- Location Availability -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Location Status</h3>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('locations.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Manage →
                        </a>
                        @endif
                    </div>
                    <div class="space-y-3">
                        @forelse($locations as $location)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 rounded-full {{ $location->is_open ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $location->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $location->appointment_count }} today</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="location-indicator {{ $location->is_open ? 'open' : 'closed' }} text-xs font-medium">
                                    {{ $location->is_open ? 'Open' : 'Closed' }}
                                </span>
                                @if(!empty($location->availability_slots))
                                <p class="text-xs text-gray-400">{{ count($location->availability_slots) }} slots</p>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No locations available</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Calendar Sync Status -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Calendar Sync</h3>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('calendar.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Settings →
                        </a>
                        @endif
                    </div>
                    <div class="space-y-3">
                        @forelse($calendarIntegrations as $integration)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($integration->provider === 'google')
                                        <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">G</span>
                                        </div>
                                    @elseif($integration->provider === 'outlook')
                                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">O</span>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 bg-gray-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">C</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $integration->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $integration->last_sync_at ? $integration->last_sync_at->diffForHumans() : 'Never synced' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($integration->is_active)
                                    @if($integration->needsSync())
                                        <span class="text-xs font-medium text-yellow-600">Pending</span>
                                    @else
                                        <span class="text-xs font-medium text-green-600">Synced</span>
                                    @endif
                                @else
                                    <span class="text-xs font-medium text-red-600">Inactive</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No calendar integrations</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('appointments.create') }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                            📅 New Appointment
                        </a>
                        <a href="{{ route('customers.index') }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                            👥 View Customers
                        </a>
                        <a href="{{ route('appointments.today') }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                            📋 Today's Schedule
                        </a>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('analytics.index') }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                            📊 Analytics
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Daily Summary -->
                @if($todayAppointments->count() > 0)
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 text-white">
                    <h3 class="text-lg font-medium mb-4">Today's Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Total Appointments:</span>
                            <span class="font-medium">{{ $todayAppointments->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Confirmed:</span>
                            <span class="font-medium">{{ $todayAppointments->where('status', 'confirmed')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Completed:</span>
                            <span class="font-medium">{{ $todayAppointments->where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Revenue:</span>
                            <span class="font-medium">${{ number_format($todayAppointments->where('status', 'completed')->sum(function($apt) { return $apt->service->price ?? 0; }), 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmAppointment(appointmentId) {
    if (confirm('Confirm this appointment?')) {
        updateAppointmentStatus(appointmentId, 'confirm');
    }
}

function completeAppointment(appointmentId) {
    if (confirm('Mark this appointment as completed?')) {
        updateAppointmentStatus(appointmentId, 'complete');
    }
}

function cancelAppointment(appointmentId) {
    if (confirm('Cancel this appointment?')) {
        updateAppointmentStatus(appointmentId, 'cancel');
    }
}

function updateAppointmentStatus(appointmentId, action) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/appointments/${appointmentId}/${action}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || response.ok) {
            location.reload();
        } else {
            alert('Error updating appointment status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating appointment status');
    });
}
</script>
@endpush
