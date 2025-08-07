@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .metric-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: transform 0.2s ease-in-out;
    }
    .metric-card:hover {
        transform: translateY(-2px);
    }
    .chart-container {
        position: relative;
        height: 400px;
        background: white;
        border-radius: 8px;
        padding: 20px;
    }
    .filter-button.active {
        background-color: #4f46e5;
        color: white;
    }
    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }
    .trend-neutral { color: #6b7280; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
                    <p class="text-gray-600 mt-1">Comprehensive business insights and performance metrics</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button onclick="exportAnalytics()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Report
                    </button>
                    <a href="{{ route('dashboard.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex space-x-2 mb-4 sm:mb-0">
                        <button onclick="setDateRange('today')" class="filter-button px-3 py-2 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50">
                            Today
                        </button>
                        <button onclick="setDateRange('week')" class="filter-button active px-3 py-2 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50">
                            This Week
                        </button>
                        <button onclick="setDateRange('month')" class="filter-button px-3 py-2 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50">
                            This Month
                        </button>
                        <button onclick="setDateRange('quarter')" class="filter-button px-3 py-2 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50">
                            This Quarter
                        </button>
                        <button onclick="setDateRange('year')" class="filter-button px-3 py-2 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50">
                            This Year
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <input type="date" id="startDate" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <span class="self-center text-gray-500">to</span>
                        <input type="date" id="endDate" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <button onclick="applyCustomDateRange()" class="px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="metric-card rounded-lg p-6 text-center">
                <div class="flex items-center justify-center mb-2">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold" id="totalRevenue">${{ number_format($analytics['total_revenue'] ?? 0, 2) }}</div>
                <div class="text-sm opacity-90">Total Revenue</div>
                <div class="text-xs mt-1">
                    <span class="{{ $analytics['revenue_trend'] >= 0 ? 'trend-up' : 'trend-down' }}">
                        {{ $analytics['revenue_trend'] >= 0 ? '↗' : '↘' }} {{ abs($analytics['revenue_trend'] ?? 0) }}%
                    </span>
                </div>
            </div>

            <div class="metric-card rounded-lg p-6 text-center">
                <div class="flex items-center justify-center mb-2">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold" id="totalAppointments">{{ $analytics['total_appointments'] ?? 0 }}</div>
                <div class="text-sm opacity-90">Total Appointments</div>
                <div class="text-xs mt-1">
                    <span class="{{ $analytics['appointments_trend'] >= 0 ? 'trend-up' : 'trend-down' }}">
                        {{ $analytics['appointments_trend'] >= 0 ? '↗' : '↘' }} {{ abs($analytics['appointments_trend'] ?? 0) }}%
                    </span>
                </div>
            </div>

            <div class="metric-card rounded-lg p-6 text-center">
                <div class="flex items-center justify-center mb-2">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold" id="totalCustomers">{{ $analytics['total_customers'] ?? 0 }}</div>
                <div class="text-sm opacity-90">Total Customers</div>
                <div class="text-xs mt-1">
                    <span class="{{ $analytics['customers_trend'] >= 0 ? 'trend-up' : 'trend-down' }}">
                        {{ $analytics['customers_trend'] >= 0 ? '↗' : '↘' }} {{ abs($analytics['customers_trend'] ?? 0) }}%
                    </span>
                </div>
            </div>

            <div class="metric-card rounded-lg p-6 text-center">
                <div class="flex items-center justify-center mb-2">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold" id="completionRate">{{ $analytics['completion_rate'] ?? 0 }}%</div>
                <div class="text-sm opacity-90">Completion Rate</div>
                <div class="text-xs mt-1">
                    <span class="{{ $analytics['completion_trend'] >= 0 ? 'trend-up' : 'trend-down' }}">
                        {{ $analytics['completion_trend'] >= 0 ? '↗' : '↘' }} {{ abs($analytics['completion_trend'] ?? 0) }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Revenue Trend</h3>
                    <select id="revenueChartType" onchange="updateRevenueChart()" class="text-sm border-gray-300 rounded-md">
                        <option value="line">Line Chart</option>
                        <option value="bar">Bar Chart</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Appointments Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Appointments Overview</h3>
                    <select id="appointmentsChartType" onchange="updateAppointmentsChart()" class="text-sm border-gray-300 rounded-md">
                        <option value="doughnut">Doughnut Chart</option>
                        <option value="pie">Pie Chart</option>
                        <option value="bar">Bar Chart</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="appointmentsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Location Performance & Service Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Location Performance -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Location Performance</h3>
                <div class="chart-container">
                    <canvas id="locationChart"></canvas>
                </div>
            </div>

            <!-- Service Performance -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Top Services</h3>
                <div class="chart-container">
                    <canvas id="serviceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Customers -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Top Customers</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointments</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($topCustomers ?? [] as $customer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer['name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $customer['email'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $customer['appointments_count'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($customer['total_revenue'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Analytics Events -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Analytics Events</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentEvents ?? [] as $event)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $event['event_type'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $event['value'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $event['created_at'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No recent events</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Calendar Sync Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Calendar Sync Performance</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ $calendarStats['total_integrations'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Connected Calendars</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $calendarStats['successful_syncs'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Successful Syncs Today</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ $calendarStats['failed_syncs'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Failed Syncs Today</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let revenueChart, appointmentsChart, locationChart, serviceChart;

// Sample data - this would come from your backend
const analyticsData = {
    revenue: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        data: [1200, 1900, 3000, 5000, 2000, 3000, 4500]
    },
    appointments: {
        labels: ['Scheduled', 'Confirmed', 'Completed', 'Cancelled'],
        data: [25, 35, 30, 10],
        colors: ['#fbbf24', '#10b981', '#3b82f6', '#ef4444']
    },
    locations: {
        labels: @json($locationStats['labels'] ?? ['Main Office', 'Branch 1', 'Branch 2']),
        data: @json($locationStats['data'] ?? [45, 30, 25])
    },
    services: {
        labels: @json($serviceStats['labels'] ?? ['Consultation', 'Treatment', 'Follow-up']),
        data: @json($serviceStats['data'] ?? [40, 35, 25])
    }
};

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: analyticsData.revenue.labels,
            datasets: [{
                label: 'Revenue ($)',
                data: analyticsData.revenue.data,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.4
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

    // Appointments Chart
    const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
    appointmentsChart = new Chart(appointmentsCtx, {
        type: 'doughnut',
        data: {
            labels: analyticsData.appointments.labels,
            datasets: [{
                data: analyticsData.appointments.data,
                backgroundColor: analyticsData.appointments.colors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Location Chart
    const locationCtx = document.getElementById('locationChart').getContext('2d');
    locationChart = new Chart(locationCtx, {
        type: 'bar',
        data: {
            labels: analyticsData.locations.labels,
            datasets: [{
                label: 'Appointments',
                data: analyticsData.locations.data,
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b']
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

    // Service Chart
    const serviceCtx = document.getElementById('serviceChart').getContext('2d');
    serviceChart = new Chart(serviceCtx, {
        type: 'horizontalBar',
        data: {
            labels: analyticsData.services.labels,
            datasets: [{
                label: 'Bookings',
                data: analyticsData.services.data,
                backgroundColor: ['#8b5cf6', '#06b6d4', '#f97316']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
}

function setDateRange(range) {
    // Remove active class from all buttons
    document.querySelectorAll('.filter-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to clicked button
    event.target.classList.add('active');
    
    // Update analytics data based on selected range
    loadAnalyticsData(range);
}

function applyCustomDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate && endDate) {
        loadAnalyticsData('custom', startDate, endDate);
    }
}

function loadAnalyticsData(range, startDate = null, endDate = null) {
    fetch('/api/analytics/data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            range: range,
            start_date: startDate,
            end_date: endDate
        })
    })
    .then(response => response.json())
    .then(data => {
        updateDashboard(data);
    })
    .catch(error => {
        console.error('Error loading analytics data:', error);
    });
}

function updateDashboard(data) {
    // Update metrics
    document.getElementById('totalRevenue').textContent = '$' + (data.total_revenue || 0).toLocaleString();
    document.getElementById('totalAppointments').textContent = data.total_appointments || 0;
    document.getElementById('totalCustomers').textContent = data.total_customers || 0;
    document.getElementById('completionRate').textContent = (data.completion_rate || 0) + '%';
    
    // Update charts with new data
    if (data.revenue_chart) {
        revenueChart.data.labels = data.revenue_chart.labels;
        revenueChart.data.datasets[0].data = data.revenue_chart.data;
        revenueChart.update();
    }
    
    if (data.appointments_chart) {
        appointmentsChart.data.datasets[0].data = data.appointments_chart.data;
        appointmentsChart.update();
    }
}

function updateRevenueChart() {
    const chartType = document.getElementById('revenueChartType').value;
    revenueChart.config.type = chartType;
    revenueChart.update();
}

function updateAppointmentsChart() {
    const chartType = document.getElementById('appointmentsChartType').value;
    appointmentsChart.config.type = chartType;
    appointmentsChart.update();
}

function exportAnalytics() {
    const range = document.querySelector('.filter-button.active').textContent.trim();
    window.location.href = `/analytics/export?range=${encodeURIComponent(range)}`;
}
</script>
@endpush
