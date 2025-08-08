@extends('layouts.app')

@section('title', $location->name . ' - Location Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
            <div class="p-6 sm:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $location->name }}</h1>
                        <p class="text-gray-600 mt-1">{{ $location->address }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('locations.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Locations
                        </a>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('locations.edit', $location) }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Location
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $location->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <span class="w-2 h-2 mr-2 rounded-full {{ $location->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                        {{ $location->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Location Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Location Information</h2>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $location->address }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Capacity</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $location->capacity }} appointments</dd>
                            </div>
                            @if($location->phone)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="tel:{{ $location->phone }}" class="text-indigo-600 hover:text-indigo-500">{{ $location->phone }}</a>
                                </dd>
                            </div>
                            @endif
                            @if($location->email)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="mailto:{{ $location->email }}" class="text-indigo-600 hover:text-indigo-500">{{ $location->email }}</a>
                                </dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Timezone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $location->timezone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Operating Hours</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($location->opens_at)->format('g:i A') }} - 
                                    {{ \Carbon\Carbon::parse($location->closes_at)->format('g:i A') }}
                                </dd>
                            </div>
                            @if($location->latitude && $location->longitude)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Coordinates</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $location->latitude }}, {{ $location->longitude }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Current Capacity -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Capacity</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-blue-700">Today's Bookings</div>
                                <div class="text-2xl font-bold text-blue-900">{{ $capacity['today_bookings'] ?? 0 }}</div>
                                <div class="text-xs text-blue-600">of {{ $location->capacity }} capacity</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-green-700">Available Slots</div>
                                <div class="text-2xl font-bold text-green-900">{{ ($location->capacity - ($capacity['today_bookings'] ?? 0)) }}</div>
                                <div class="text-xs text-green-600">remaining today</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-purple-700">Utilization</div>
                                <div class="text-2xl font-bold text-purple-900">
                                    {{ $location->capacity > 0 ? round((($capacity['today_bookings'] ?? 0) / $location->capacity) * 100) : 0 }}%
                                </div>
                                <div class="text-xs text-purple-600">today</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics -->
                @if(isset($analytics))
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Analytics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-gray-700">This Week</div>
                                <div class="text-xl font-bold text-gray-900">{{ $analytics['week_bookings'] ?? 0 }} bookings</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-gray-700">This Month</div>
                                <div class="text-xl font-bold text-gray-900">{{ $analytics['month_bookings'] ?? 0 }} bookings</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-gray-700">Average Rating</div>
                                <div class="text-xl font-bold text-gray-900">{{ $analytics['avg_rating'] ?? 'N/A' }}</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-gray-700">Completion Rate</div>
                                <div class="text-xl font-bold text-gray-900">{{ $analytics['completion_rate'] ?? 'N/A' }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="space-y-6">
                <!-- Location Features -->
                @if($location->settings)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Features & Amenities</h3>
                        <div class="space-y-2">
                            @foreach($location->settings as $key => $value)
                                @if($value)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('appointments.create') }}?location={{ $location->id }}" 
                               class="block w-full px-4 py-2 bg-indigo-600 text-white text-center rounded-md hover:bg-indigo-700">
                                Book Appointment
                            </a>
                            <a href="{{ route('locations.availability', $location) }}" 
                               class="block w-full px-4 py-2 bg-gray-600 text-white text-center rounded-md hover:bg-gray-700">
                                Check Availability
                            </a>
                            @if(auth()->user()->role === 'admin')
                            <button onclick="confirmTransfer()" 
                                    class="block w-full px-4 py-2 bg-yellow-600 text-white text-center rounded-md hover:bg-yellow-700">
                                Transfer Appointments
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Map -->
                @if($location->latitude && $location->longitude)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Map</h3>
                        <div class="bg-gray-200 h-48 rounded-lg flex items-center justify-center">
                            <p class="text-gray-500">Map integration placeholder</p>
                            <div class="text-xs text-gray-400 mt-2">
                                {{ $location->latitude }}, {{ $location->longitude }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function confirmTransfer() {
    if (confirm('Are you sure you want to transfer appointments from this location?')) {
        // This would open a modal or redirect to transfer page
        console.log('Transfer appointments from location {{ $location->id }}');
    }
}
</script>
@endsection
