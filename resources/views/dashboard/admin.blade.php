@extends('layouts.app')

@section('title', 'Admin Dashboard - Enterprise Management')

@push('styles')
<style>
    .metric-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .health-indicator.healthy { color: #10b981; }
    .health-indicator.warning { color: #f59e0b; }
    .health-indicator.error { color: #ef4444; }
    .performance-bar {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        height: 8px;
        border-radius: 4px;
    }
    .chart-container { height: 400px; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                    <p class="text-gray-600 mt-1">Enterprise management and system monitoring</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('dashboard.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Main Dashboard
                    </a>
                    <button onclick="generateReport()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>

        <!-- System Health Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Analytics Records</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($systemHealth['database_health']['analytics_records'] ?? 0) }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Locations with GPS</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $systemHealth['location_health']['locations_with_gps'] ?? 0 }}/{{ $systemHealth['location_health']['total_locations'] ?? 0 }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Synced Appointments</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $systemHealth['calendar_sync_health']['synced_appointments'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="metric-card bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($systemHealth['database_health']['total_customers'] ?? 0) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Analytics & Performance -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Advanced Analytics Chart -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Revenue Analytics</h3>
                        <div class="flex space-x-2">
                            <select onchange="updateAnalyticsChart(this.value)" class="border border-gray-300 rounded-md px-3 py-1 text-sm">
                                <option value="revenue">Revenue</option>
                                <option value="appointments">Appointments</option>
                                <option value="customers">Customers</option>
                            </select>
                            <a href="{{ route('analytics.export') }}?format=csv" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                Export
                            </a>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="analyticsChart"></canvas>
                    </div>
                </div>

                <!-- Staff Performance -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Staff Performance</h3>
                        <a href="{{ route('team.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Manage Team →
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($staffPerformance as $staffId => $performance)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">{{ $performance['name'] }}</h4>
                                <span class="text-sm text-gray-500">${{ number_format($performance['revenue'], 2) }} revenue</span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                <span>{{ $performance['appointments'] }} appointments</span>
                                <span>{{ $performance['appointments'] > 0 ? number_format($performance['revenue'] / $performance['appointments'], 2) : '0.00' }} avg/appointment</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="performance-bar" style="width: {{ min(100, ($performance['revenue'] / max(1, collect($staffPerformance)->max('revenue'))) * 100) }}%"></div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500">No staff performance data available</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column - Management Tools -->
            <div class="space-y-8">
                <!-- Location Performance -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Location Performance</h3>
                        <a href="{{ route('locations.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Manage →
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($locationPerformance as $locationId => $data)
                        @php $location = $data['location']; $analytics = $data['analytics']; $capacity = $data['capacity']; @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">{{ $location->name }}</h4>
                                <span class="text-xs px-2 py-1 rounded-full {{ $location->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $location->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-2">
                                <div>{{ $analytics['total_appointments'] }} appointments</div>
                                <div>${{ number_format($analytics['revenue'], 2) }}</div>
                                <div>{{ $capacity['capacity_percentage'] }}% capacity</div>
                                <div>{{ $capacity['available_slots'] }} slots left</div>
                            </div>
                            @if($location->latitude && $location->longitude)
                            <div class="text-xs text-gray-400">📍 GPS: {{ number_format($location->latitude, 4) }}, {{ number_format($location->longitude, 4) }}</div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No locations configured</p>
                            <a href="{{ route('locations.create') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                Add Location →
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Calendar Integration Health -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Calendar Health</h3>
                        <a href="{{ route('calendar.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Manage →
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($calendarHealth as $integrationId => $health)
                        @php $integration = $health['integration']; @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    @if($integration->provider === 'google')
                                        <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-white text-xs font-bold">G</span>
                                        </div>
                                    @elseif($integration->provider === 'outlook')
                                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-white text-xs font-bold">O</span>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 bg-gray-500 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-white text-xs font-bold">C</span>
                                        </div>
                                    @endif
                                    <h4 class="font-medium text-gray-900">{{ $integration->name }}</h4>
                                </div>
                                @php
                                    $healthStatus = $integration->is_active ? ($health['needs_sync'] ? 'warning' : 'healthy') : 'error';
                                    $healthText = $integration->is_active ? ($health['needs_sync'] ? 'Needs Sync' : 'Healthy') : 'Inactive';
                                @endphp
                                <span class="health-indicator {{ $healthStatus }} text-xs font-medium">{{ $healthText }}</span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div>Last sync: {{ $health['last_sync'] ? $health['last_sync']->diffForHumans() : 'Never' }}</div>
                                <div>Sync errors: {{ $health['sync_errors'] }}</div>
                                <div>Provider: {{ ucfirst($integration->provider) }}</div>
                            </div>
                            @if($integration->is_active && $health['needs_sync'])
                            <button onclick="syncCalendar({{ $integration->id }})" class="mt-2 w-full text-xs bg-yellow-100 text-yellow-800 py-1 px-2 rounded hover:bg-yellow-200">
                                Sync Now
                            </button>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No calendar integrations</p>
                            <a href="{{ route('calendar.create') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                Add Integration →
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button onclick="generateAnalytics()" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                            🔄 Generate Analytics
                        </button>
                        <button onclick="syncAllCalendars()" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                            📅 Sync All Calendars
                        </button>
                        <a href="{{ route('analytics.export') }}?format=csv" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                            📊 Export Analytics
                        </a>
                        <a href="{{ route('analytics.report') }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                            📈 Generate Report
                        </a>
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
let analyticsChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeAnalyticsChart();
});

function initializeAnalyticsChart() {
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    analyticsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($analytics['revenue']['trend']->toArray() ?? [])) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode(array_values($analytics['revenue']['trend']->toArray() ?? [])) !!},
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
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
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
}

function updateAnalyticsChart(type) {
    fetch(`{{ route('analytics.chart-data') }}?type=${type}&days=30`)
        .then(response => response.json())
        .then(data => {
            if (data.success && analyticsChart) {
                analyticsChart.data.labels = data.data.labels;
                analyticsChart.data.datasets[0].data = data.data.datasets[0].data;
                analyticsChart.data.datasets[0].label = type.charAt(0).toUpperCase() + type.slice(1);
                analyticsChart.update();
            }
        })
        .catch(error => console.error('Error updating chart:', error));
}

function generateAnalytics() {
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
            alert('Analytics generated successfully!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error generating analytics:', error);
        alert('Error generating analytics');
    });
}

function syncCalendar(integrationId) {
    fetch(`/calendar/${integrationId}/sync`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Calendar synced successfully!');
            location.reload();
        } else {
            alert('Error syncing calendar');
        }
    })
    .catch(error => {
        console.error('Error syncing calendar:', error);
        alert('Error syncing calendar');
    });
}

function syncAllCalendars() {
    const integrations = {!! json_encode(array_keys($calendarHealth ?? [])) !!};
    let promises = [];
    
    integrations.forEach(id => {
        promises.push(
            fetch(`/calendar/${id}/sync`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
        );
    });
    
    Promise.all(promises)
        .then(() => {
            alert('All calendars synced successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error syncing calendars:', error);
            alert('Error syncing some calendars');
        });
}

function generateReport() {
    window.open('{{ route('analytics.report') }}', '_blank');
}
</script>
@endpush
