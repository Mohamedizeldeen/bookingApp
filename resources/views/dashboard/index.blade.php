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

        <!-- Performance Alerts Section -->
        @if(isset($performanceAlerts) && count($performanceAlerts) > 0)
        <div class="mb-8">
            <div class="bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-orange-800">Performance Alerts</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($performanceAlerts as $alert)
                    <div class="bg-white rounded-lg p-4 border border-orange-200">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $alert['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                       ($alert['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($alert['priority']) }}
                                </span>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $alert['title'] ?? 'Alert' }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $alert['message'] ?? 'Performance issue detected' }}</p>
                                @if(isset($alert['action']))
                                <p class="text-xs text-gray-500 mt-2">Recommended: {{ $alert['action'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Real-Time Analytics Dashboard -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Live Metrics -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Live Metrics</h3>
                    <div class="flex items-center text-sm text-green-600">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                        Real-time
                    </div>
                </div>
                <div class="space-y-4">
                    @if(isset($liveMetrics['current_hour_appointments']))
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">This Hour</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $liveMetrics['current_hour_appointments'] }} appointments</span>
                    </div>
                    @endif
                    @if(isset($realTimeMetrics['appointments_today']))
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Today Total</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $realTimeMetrics['appointments_today'] }} appointments</span>
                    </div>
                    @endif
                    @if(isset($realTimeMetrics['revenue_today']))
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Today Revenue</span>
                        <span class="text-lg font-semibold text-green-600">${{ number_format($realTimeMetrics['revenue_today'], 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Hourly Trends -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hourly Trends</h3>
                <div class="space-y-3">
                    @if(isset($hourlyTrends) && is_array($hourlyTrends))
                        @foreach(array_slice($hourlyTrends, -6) as $data)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $data['hour'] ?? '00:00' }}</span>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm font-medium text-gray-900">{{ $data['appointments'] ?? 0 }} appointments</span>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, ($data['appointments'] ?? 0) * 10) }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">No hourly data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Enhanced Location Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Multi-Location Performance</h3>
                    <div class="space-y-4">
                        @foreach($locations as $location)
                        @php
                            $locationData = $locationAnalytics[$location->id] ?? [];
                            $analytics = $locationData['analytics'] ?? [];
                            $capacity = $locationData['capacity'] ?? [];
                            $utilization = $locationData['utilization'] ?? 0;
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3 {{ $location->is_open ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                    <h4 class="font-medium text-gray-900">{{ $location->name }}</h4>
                                    <span class="ml-2 text-sm text-gray-500">{{ $location->address }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if(isset($capacity['utilization_percentage']))
                                    <span class="text-sm font-medium text-gray-600">{{ $capacity['utilization_percentage'] }}% utilized</span>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Total Appointments</span>
                                    <div class="font-medium">{{ $analytics['total_appointments'] ?? 0 }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Revenue</span>
                                    <div class="font-medium">${{ number_format($analytics['revenue'] ?? 0, 2) }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Completion Rate</span>
                                    <div class="font-medium">{{ $analytics['completion_rate'] ?? 0 }}%</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Available Slots</span>
                                    <div class="font-medium">{{ $capacity['available_slots'] ?? 0 }}</div>
                                </div>
                            </div>
                            @if(isset($capacity['utilization_percentage']))
                            <div class="mt-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, $capacity['utilization_percentage']) }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Location Recommendations -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Recommendations</h3>
                <div class="space-y-4">
                    @if(isset($locationRecommendations) && count($locationRecommendations) > 0)
                        @foreach(array_slice($locationRecommendations, 0, 3) as $recommendation)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">{{ $recommendation['location']->name }}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $recommendation['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                       ($recommendation['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($recommendation['priority']) }}
                                </span>
                            </div>
                            @foreach($recommendation['suggestions'] as $suggestion)
                            <div class="mb-3 last:mb-0">
                                <h5 class="text-sm font-medium text-gray-700">{{ $suggestion['title'] ?? $suggestion['type'] }}</h5>
                                <p class="text-xs text-gray-600 mt-1">{{ $suggestion['message'] }}</p>
                                @if(isset($suggestion['action']))
                                <p class="text-xs text-blue-600 mt-1">→ {{ $suggestion['action'] }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">All locations are performing optimally!</p>
                    @endif
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
                            <div class="text-2xl font-bold text-green-600">${{ number_format($analytics['revenue'] ?? 0, 2) }}</div>
                            <div class="text-sm text-gray-500">Total Revenue</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">${{ number_format(($analytics['total_appointments'] > 0 ? $analytics['revenue'] / $analytics['total_appointments'] : 0), 2) }}</div>
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

                <!-- Enhanced Calendar Integrations -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Advanced Calendar Sync</h3>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('calendar.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Manage →
                        </a>
                        @endif
                    </div>
                    <div class="space-y-3">
                        @forelse($calendarIntegrations as $integration)
                        @php
                            $status = $calendarSyncStatus[$integration->id] ?? [];
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($integration->provider === 'google')
                                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">G</span>
                                            </div>
                                        @elseif($integration->provider === 'outlook')
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">O</span>
                                            </div>
                                        @elseif($integration->provider === 'apple')
                                            <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">A</span>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">C</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $integration->name }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($integration->provider) }} Calendar</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($status['is_active'] ?? false)
                                        @if($status['needs_token_refresh'] ?? false)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                Token Refresh Needed
                                            </span>
                                        @elseif($status['can_sync'] ?? false)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Sync Pending
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Last Sync</span>
                                    <div class="font-medium">{{ $status['last_sync'] ?? 'Never' }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Sync Status</span>
                                    <div class="font-medium">{{ $status['sync_status'] ?? 'Unknown' }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Auto-Sync</span>
                                    <div class="font-medium">{{ $integration->is_active ? 'Enabled' : 'Disabled' }}</div>
                                </div>
                            </div>
                            
                            @if($integration->sync_frequency)
                            <div class="mt-2 text-xs text-gray-500">
                                Sync frequency: {{ ucfirst($integration->sync_frequency) }}
                            </div>
                            @endif
                            
                            @if(($status['needs_token_refresh'] ?? false) || !($status['can_sync'] ?? false))
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <button class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">
                                    Fix Sync Issue →
                                </button>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-6 border border-gray-200 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500 text-sm mt-2">No calendar integrations configured</p>
                            <p class="text-xs text-gray-400 mt-1">Connect your calendar to sync appointments automatically</p>
                            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('calendar.create') }}" class="inline-flex items-center mt-3 text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Calendar Integration
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

        <!-- Task Management Section -->
        <div class="mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Task Stats -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Task Management</h3>
                        <a href="{{ route('tasks.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            View All →
                        </a>
                    </div>
                    
                    <!-- Task Statistics -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Tasks</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $taskStats['total'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Pending</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $taskStats['pending'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Completed</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $taskStats['completed'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">High Priority</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $taskStats['high_priority'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Tasks -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Recent Tasks</h4>
                        @forelse($recentTasks->take(3) as $task)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>
                                <p class="text-xs text-gray-500">
                                    Assigned to: {{ $task->assignedUser->name }}
                                    @if($task->appointment)
                                    | Related to: {{ $task->appointment->customer->name }}
                                    @endif
                                </p>
                                @if($task->due_date)
                                <p class="text-xs text-gray-400">Due: {{ $task->due_date->format('M j, Y') }}</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($task->priority === 'high') bg-red-100 text-red-800
                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($task->status === 'completed') bg-green-100 text-green-800
                                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No recent tasks</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Online Booking Links for Social Media -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Share Booking Links</h3>
                        <span class="text-sm text-gray-500">Promote your services</span>
                    </div>

                    @php
                        $services = $company->services()->active()->get();
                    @endphp

                    @if($services->count() > 0)
                    <div class="space-y-4">
                        @foreach($services->take(3) as $service)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $service->name }}</h4>
                                    <p class="text-xs text-gray-500">${{ number_format($service->price, 2) }} • {{ $service->duration }} min</p>
                                </div>
                                <button type="button" 
                                        onclick="copyBookingLink('{{ $service->getBookingUrl() }}')"
                                        class="text-gray-400 hover:text-gray-600 focus:outline-none"
                                        title="Copy booking link">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Booking URL -->
                            <div class="mb-3">
                                <input type="text" 
                                       value="{{ $service->getBookingUrl() }}" 
                                       readonly 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-xs font-mono bg-gray-50 text-gray-600"
                                       onclick="this.select()">
                            </div>

                            <!-- Social Media Share Buttons -->
                                @include('components.social-share', [
                                    'shareData' => [
                                        'url' => $service->getBookingUrl(),
                                        'facebook_url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($service->getBookingUrl()),
                                        'twitter_url' => 'https://twitter.com/intent/tweet?url=' . urlencode($service->getBookingUrl()) . '&text=' . urlencode('Book ' . $service->name . ' - ' . $company->name),
                                        'linkedin_url' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($service->getBookingUrl()) . '&title=' . urlencode('Book ' . $service->name . ' - ' . $company->name),
                                        'whatsapp_url' => 'https://wa.me/?text=' . urlencode($service->getBookingUrl()),
                                        'email_url' => 'mailto:?subject=' . urlencode('Book ' . $service->name . ' - ' . $company->name) . '&body=' . urlencode($service->getBookingUrl()),
                                    ],
                                    'serviceId' => $service->id ?? 'default'
                                ])
                        </div>
                        @endforeach

                        @if($services->count() > 3)
                        <div class="text-center">
                            <a href="{{ route('services.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                View all {{ $services->count() }} services →
                            </a>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="text-gray-500 text-sm mb-2">No active services available</p>
                        <a href="{{ route('services.create') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Create your first service →
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Copy booking link functionality
function copyBookingLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Show success message
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        
        Toast.fire({
            icon: 'success',
            title: 'Booking link copied to clipboard!'
        });
    }, function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for browsers that don't support clipboard API
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        
        Toast.fire({
            icon: 'success',
            title: 'Booking link copied to clipboard!'
        });
    });
}
</script>
@endpush

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
            labels: {!! json_encode($analytics['chart_labels'] ?? []) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($analytics['chart_revenue'] ?? []) !!},
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
