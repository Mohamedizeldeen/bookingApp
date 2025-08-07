@extends('layouts.app')

@section('title', 'Enterprise Dashboard')

@push('styles')
<style>
    .metric-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .chart-container {
        height: 300px;
    }
    .location-indicator.open {
        color: #10b981;
    }
    .location-indicator.closed {
        color: #ef4444;
    }
    .sync-status.synced {
        color: #10b981;
    }
    .sync-status.pending {
        color: #f59e0b;
    }
    .sync-status.error {
        color: #ef4444;
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
                    <h1 class="text-3xl font-bold text-gray-900">Enterprise Dashboard</h1>
                    <p class="text-gray-600 mt-1">Advanced analytics, multi-location support, and calendar sync</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button onclick="refreshDashboard()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard.admin') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Admin Dashboard
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Today's Appointments</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $quickStats['today_appointments'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Today's Revenue</dt>
                                <dd class="text-lg font-medium text-gray-900">${{ number_format($quickStats['today_revenue'] ?? 0, 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Locations</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $quickStats['active_locations'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Calendar Syncs</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $quickStats['calendar_integrations'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Analytics -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Appointments Chart -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Appointment Trends</h3>
                        <select onchange="updateChart('appointments', this.value)" class="border border-gray-300 rounded-md px-3 py-1 text-sm">
                            <option value="7">Last 7 days</option>
                            <option value="30" selected>Last 30 days</option>
                            <option value="90">Last 90 days</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="appointmentsChart"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $analytics['appointments']['total'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Total</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ $analytics['appointments']['completed'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Completed</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600">{{ $analytics['appointments']['cancelled'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Cancelled</div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Revenue Analytics</h3>
                        <a href="{{ route('analytics.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            View Details →
                        </a>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-green-600">${{ number_format($analytics['revenue']['total'] ?? 0, 2) }}</div>
                            <div class="text-sm text-gray-500">Total Revenue</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">${{ number_format($analytics['revenue']['average_per_appointment'] ?? 0, 2) }}</div>
                            <div class="text-sm text-gray-500">Avg per Appointment</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Locations & Calendar -->
            <div class="space-y-8">
                <!-- Location Status -->
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
                                    <p class="text-xs text-gray-500">{{ $location->appointment_count }} appointments today</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="location-indicator {{ $location->is_open ? 'open' : 'closed' }} text-xs font-medium">
                                    {{ $location->is_open ? 'Open' : 'Closed' }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No locations configured</p>
                            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('locations.create') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                Add Location →
                            </a>
                            @endif
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Calendar Integrations -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Calendar Sync</h3>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('calendar.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Manage →
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
                                    <p class="text-xs text-gray-500">{{ $calendarSyncStatus[$integration->id]['last_sync'] ?? 'Never' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $status = $calendarSyncStatus[$integration->id] ?? [];
                                    $statusClass = $status['is_active'] ?? false ? ($status['needs_sync'] ?? false ? 'pending' : 'synced') : 'error';
                                    $statusText = $status['is_active'] ?? false ? ($status['needs_sync'] ?? false ? 'Pending' : 'Synced') : 'Inactive';
                                @endphp
                                <span class="sync-status {{ $statusClass }} text-xs font-medium">{{ $statusText }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No calendar integrations</p>
                            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('calendar.create') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                Add Integration →
                            </a>
                            @endif
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Appointments -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Appointments</h3>
                        <a href="{{ route('appointments.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            View All →
                        </a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentAppointments as $appointment)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $appointment->customer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $appointment->service->name }}</p>
                                @if($appointment->location)
                                <p class="text-xs text-gray-400">📍 {{ $appointment->location->name }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ $appointment->appointment_date->format('M j') }}</p>
                                <p class="text-xs text-gray-500">{{ $appointment->appointment_time }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No recent appointments</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts
let appointmentsChart, revenueChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Appointments Chart
    const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
    appointmentsChart = new Chart(appointmentsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($analytics['appointments']['trend']->toArray() ?? [])) !!},
            datasets: [{
                label: 'Appointments',
                data: {!! json_encode(array_values($analytics['appointments']['trend']->toArray() ?? [])) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($analytics['revenue']['trend']->toArray() ?? [])) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode(array_values($analytics['revenue']['trend']->toArray() ?? [])) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function updateChart(type, days) {
    fetch(`{{ route('analytics.chart-data') }}?type=${type}&days=${days}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (type === 'appointments' && appointmentsChart) {
                    appointmentsChart.data.labels = data.data.labels;
                    appointmentsChart.data.datasets[0].data = data.data.datasets[0].data;
                    appointmentsChart.update();
                } else if (type === 'revenue' && revenueChart) {
                    revenueChart.data.labels = data.data.labels;
                    revenueChart.data.datasets[0].data = data.data.datasets[0].data;
                    revenueChart.update();
                }
            }
        })
        .catch(error => console.error('Error updating chart:', error));
}

function refreshDashboard() {
    fetch('{{ route('dashboard.refresh') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error refreshing dashboard:', error));
}
</script>
@endpush
