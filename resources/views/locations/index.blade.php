@extends('layouts.app')

@section('title', 'Location Management')

@push('styles')
<style>
    .location-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .location-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .map-container {
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        position: relative;
        overflow: hidden;
    }
    .status-toggle.active {
        background-color: #10b981;
        transform: translateX(20px);
    }
    .status-toggle {
        background-color: #ef4444;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        transform: translateX(2px);
        transition: all 0.3s ease;
    }
    .toggle-bg {
        width: 44px;
        height: 24px;
        background-color: #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .toggle-bg.active {
        background-color: #d1fae5;
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
                    <h1 class="text-3xl font-bold text-gray-900">Location Management</h1>
                    <p class="text-gray-600 mt-1">Manage your business locations and availability</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button onclick="openLocationModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Location
                    </button>
                    <a href="{{ route('dashboard.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Locations</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $locations->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Locations</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $locations->where('is_active', true)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
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
                            <dd class="text-lg font-medium text-gray-900">{{ $todayAppointmentsCount ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg. Revenue/Location</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($avgRevenuePerLocation ?? 0, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Locations Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($locations as $location)
            <div class="location-card bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Location Image/Map -->
                <div class="map-container">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-white text-center">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            <p class="text-sm font-medium">{{ $location->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Location Details -->
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $location->name }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ $location->address }}</p>
                            
                            <!-- Location Status -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm text-gray-500">Status:</span>
                                <div class="flex items-center">
                                    <div class="toggle-bg {{ $location->is_active ? 'active' : '' }}" onclick="toggleLocationStatus({{ $location->id }})">
                                        <div class="status-toggle {{ $location->is_active ? 'active' : '' }}"></div>
                                    </div>
                                    <span class="ml-2 text-sm {{ $location->is_active ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $location->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Location Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Today</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $location->today_appointments ?? 0 }}</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600">This Week</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $location->week_appointments ?? 0 }}</p>
                                </div>
                            </div>

                            <!-- Working Hours -->
                            @if(!empty($location->business_hours))
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Business Hours:</p>
                                <div class="text-xs text-gray-500 space-y-1">
                                    @foreach($location->business_hours as $day => $hours)
                                    <div class="flex justify-between">
                                        <span>{{ ucfirst($day) }}:</span>
                                        <span>{{ $hours['open'] ?? 'Closed' }} - {{ $hours['close'] ?? '' }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <button onclick="editLocation({{ $location->id }})" class="flex-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    Edit
                                </button>
                                <button onclick="viewLocationDetails({{ $location->id }})" class="flex-1 bg-gray-50 text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    Details
                                </button>
                                <button onclick="deleteLocation({{ $location->id }})" class="bg-red-50 text-red-700 hover:bg-red-100 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No locations</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new location.</p>
                    <div class="mt-6">
                        <button onclick="openLocationModal()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Location
                        </button>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Location Modal -->
<div id="locationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add New Location</h3>
                <button onclick="closeLocationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="locationForm" class="space-y-4">
                @csrf
                <input type="hidden" id="locationId" name="location_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Location Name</label>
                        <input type="text" id="name" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" id="phone" name="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="address" name="address" rows="2" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                        <input type="number" step="any" id="latitude" name="latitude" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                        <input type="number" step="any" id="longitude" name="longitude" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Active Location</label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeLocationModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Save Location
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openLocationModal(locationId = null) {
    const modal = document.getElementById('locationModal');
    const form = document.getElementById('locationForm');
    const title = document.getElementById('modalTitle');
    
    if (locationId) {
        title.textContent = 'Edit Location';
        // Load location data
        loadLocationData(locationId);
    } else {
        title.textContent = 'Add New Location';
        form.reset();
        document.getElementById('locationId').value = '';
    }
    
    modal.classList.remove('hidden');
}

function closeLocationModal() {
    document.getElementById('locationModal').classList.add('hidden');
}

function loadLocationData(locationId) {
    fetch(`/api/locations/${locationId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('locationId').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('phone').value = data.phone || '';
            document.getElementById('address').value = data.address;
            document.getElementById('latitude').value = data.latitude || '';
            document.getElementById('longitude').value = data.longitude || '';
            document.getElementById('is_active').checked = data.is_active;
        })
        .catch(error => {
            console.error('Error loading location data:', error);
            alert('Error loading location data');
        });
}

document.getElementById('locationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const locationId = document.getElementById('locationId').value;
    const url = locationId ? `/api/locations/${locationId}` : '/api/locations';
    const method = locationId ? 'PUT' : 'POST';
    
    const data = {};
    formData.forEach((value, key) => {
        if (key === 'is_active') {
            data[key] = document.getElementById('is_active').checked;
        } else {
            data[key] = value;
        }
    });
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.id) {
            closeLocationModal();
            location.reload();
        } else {
            alert('Error saving location');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving location');
    });
});

function toggleLocationStatus(locationId) {
    fetch(`/api/locations/${locationId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating location status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating location status');
    });
}

function editLocation(locationId) {
    openLocationModal(locationId);
}

function viewLocationDetails(locationId) {
    window.location.href = `/locations/${locationId}`;
}

function deleteLocation(locationId) {
    if (confirm('Are you sure you want to delete this location? This action cannot be undone.')) {
        fetch(`/api/locations/${locationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting location');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting location');
        });
    }
}
</script>
@endpush
