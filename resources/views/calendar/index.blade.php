@extends('layouts.app')

@section('title', 'Calendar Integration')

@push('styles')
<style>
    .integration-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .integration-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .integration-card.connected {
        border-color: #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    }
    .integration-card.error {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
    .provider-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        margin-bottom: 16px;
    }
    .google { background: linear-gradient(135deg, #ea4335 0%, #fbbc04 100%); }
    .outlook { background: linear-gradient(135deg, #0078d4 0%, #106ebe 100%); }
    .apple { background: linear-gradient(135deg, #000000 0%, #434343 100%); }
    .caldav { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
    .sync-status.synced { color: #10b981; }
    .sync-status.pending { color: #f59e0b; }
    .sync-status.error { color: #ef4444; }
    .sync-status.inactive { color: #6b7280; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Calendar Integration</h1>
                    <p class="text-gray-600 mt-1">Connect your external calendars to sync appointments automatically</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button onclick="syncAllCalendars()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Sync All
                    </button>
                    <a href="{{ route('dashboard.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Sync Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Connected Calendars</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $integrations->where('is_active', true)->count() }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Events Synced Today</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $syncedEventsToday ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Last Sync</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $lastSync ?? 'Never' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Sync Frequency</dt>
                            <dd class="text-lg font-medium text-gray-900">Every 30min</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Providers -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Available Calendar Providers</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                $providers = [
                    'google' => [
                        'name' => 'Google Calendar',
                        'description' => 'Sync with your Google Calendar account',
                        'icon' => 'G',
                        'class' => 'google'
                    ],
                    'outlook' => [
                        'name' => 'Outlook Calendar',
                        'description' => 'Connect to Microsoft Outlook/Office 365',
                        'icon' => 'O',
                        'class' => 'outlook'
                    ],
                    'apple' => [
                        'name' => 'Apple iCloud',
                        'description' => 'Sync with Apple iCloud Calendar',
                        'icon' => '',
                        'class' => 'apple'
                    ],
                    'caldav' => [
                        'name' => 'CalDAV',
                        'description' => 'Connect any CalDAV-compatible calendar',
                        'icon' => 'CAL',
                        'class' => 'caldav'
                    ]
                ];
                @endphp

                @foreach($providers as $providerKey => $provider)
                @php
                $integration = $integrations->where('provider', $providerKey)->first();
                $isConnected = $integration && $integration->is_active;
                $hasError = $integration && !empty($integration->last_error);
                @endphp
                
                <div class="integration-card {{ $isConnected ? 'connected' : ($hasError ? 'error' : '') }} bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="provider-icon {{ $provider['class'] }} mx-auto">
                        @if($provider['icon'] === '' && $providerKey === 'apple')
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/>
                            </svg>
                        @else
                            {{ $provider['icon'] }}
                        @endif
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $provider['name'] }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $provider['description'] }}</p>
                    
                    @if($isConnected)
                        <div class="mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                Connected
                            </span>
                            @if($integration->last_sync_at)
                            <p class="text-xs text-gray-500 mt-2">
                                Last sync: {{ $integration->last_sync_at->diffForHumans() }}
                            </p>
                            @endif
                        </div>
                        
                        <div class="flex space-x-2">
                            <button onclick="syncCalendar('{{ $integration->id }}')" class="flex-1 bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Sync Now
                            </button>
                            <button onclick="disconnectCalendar('{{ $integration->id }}')" class="flex-1 bg-red-50 text-red-700 hover:bg-red-100 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Disconnect
                            </button>
                        </div>
                        
                        <button onclick="configureCalendar('{{ $integration->id }}')" class="w-full mt-2 bg-gray-50 text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Configure
                        </button>
                    @elseif($hasError)
                        <div class="mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                Error
                            </span>
                            <p class="text-xs text-red-600 mt-2">{{ $integration->last_error }}</p>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button onclick="retryConnection('{{ $integration->id }}')" class="flex-1 bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Retry
                            </button>
                            <button onclick="removeIntegration('{{ $integration->id }}')" class="flex-1 bg-red-50 text-red-700 hover:bg-red-100 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Remove
                            </button>
                        </div>
                    @else
                        <button onclick="connectCalendar('{{ $providerKey }}')" class="w-full bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Connect {{ $provider['name'] }}
                        </button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Active Integrations -->
        @if($integrations->where('is_active', true)->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Active Integrations</h2>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calendar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Sync</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Events Synced</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($integrations->where('is_active', true) as $integration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="provider-icon {{ $integration->provider }} h-8 w-8 text-xs">
                                                    {{ strtoupper(substr($integration->provider, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $integration->name }}</div>
                                                <div class="text-sm text-gray-500">{{ ucfirst($integration->provider) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($integration->last_error)
                                            <span class="sync-status error inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Error
                                            </span>
                                        @elseif($integration->needsSync())
                                            <span class="sync-status pending inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @else
                                            <span class="sync-status synced inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Synced
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $integration->last_sync_at ? $integration->last_sync_at->diffForHumans() : 'Never' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $integration->events_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="syncCalendar('{{ $integration->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                                Sync
                                            </button>
                                            <button onclick="configureCalendar('{{ $integration->id }}')" class="text-gray-600 hover:text-gray-900">
                                                Configure
                                            </button>
                                            <button onclick="disconnectCalendar('{{ $integration->id }}')" class="text-red-600 hover:text-red-900">
                                                Disconnect
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Sync Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Sync Settings</h2>
            <form id="syncSettingsForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sync_frequency" class="block text-sm font-medium text-gray-700">Sync Frequency</label>
                        <select id="sync_frequency" name="sync_frequency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="15">Every 15 minutes</option>
                            <option value="30" selected>Every 30 minutes</option>
                            <option value="60">Every hour</option>
                            <option value="180">Every 3 hours</option>
                            <option value="360">Every 6 hours</option>
                            <option value="720">Every 12 hours</option>
                            <option value="1440">Daily</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="sync_direction" class="block text-sm font-medium text-gray-700">Sync Direction</label>
                        <select id="sync_direction" name="sync_direction" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="bidirectional">Two-way sync</option>
                            <option value="import_only">Import only (External → Booking App)</option>
                            <option value="export_only">Export only (Booking App → External)</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="auto_sync" name="auto_sync" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="auto_sync" class="ml-2 block text-sm text-gray-900">Enable automatic synchronization</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="conflict_resolution" name="conflict_resolution" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="conflict_resolution" class="ml-2 block text-sm text-gray-900">Automatically resolve conflicts (prioritize external calendar)</label>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Calendar Configuration Modal -->
<div id="configModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="configModalTitle">Configure Calendar</h3>
                <button onclick="closeConfigModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="configModalContent">
                <!-- Dynamic content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function connectCalendar(provider) {
    window.location.href = `/calendar/connect/${provider}`;
}

function syncCalendar(integrationId) {
    fetch(`/api/calendar/${integrationId}/sync`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Calendar sync initiated successfully');
            location.reload();
        } else {
            alert('Error initiating calendar sync: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error initiating calendar sync');
    });
}

function syncAllCalendars() {
    if (confirm('Sync all connected calendars?')) {
        fetch('/api/calendar/sync-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('All calendars sync initiated successfully');
                location.reload();
            } else {
                alert('Error initiating sync: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error initiating sync');
        });
    }
}

function disconnectCalendar(integrationId) {
    if (confirm('Are you sure you want to disconnect this calendar?')) {
        fetch(`/api/calendar/${integrationId}/disconnect`, {
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
                alert('Error disconnecting calendar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error disconnecting calendar');
        });
    }
}

function configureCalendar(integrationId) {
    fetch(`/api/calendar/${integrationId}/config`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('configModalTitle').textContent = `Configure ${data.name}`;
            document.getElementById('configModalContent').innerHTML = generateConfigForm(data);
            document.getElementById('configModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading calendar configuration');
        });
}

function generateConfigForm(integration) {
    return `
        <form id="configForm" class="space-y-4">
            <input type="hidden" id="integrationId" value="${integration.id}">
            
            <div>
                <label for="configName" class="block text-sm font-medium text-gray-700">Calendar Name</label>
                <input type="text" id="configName" name="name" value="${integration.name}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="configSyncDirection" class="block text-sm font-medium text-gray-700">Sync Direction</label>
                <select id="configSyncDirection" name="sync_direction" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="bidirectional" ${integration.sync_direction === 'bidirectional' ? 'selected' : ''}>Two-way sync</option>
                    <option value="import_only" ${integration.sync_direction === 'import_only' ? 'selected' : ''}>Import only</option>
                    <option value="export_only" ${integration.sync_direction === 'export_only' ? 'selected' : ''}>Export only</option>
                </select>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="configActive" name="is_active" ${integration.is_active ? 'checked' : ''} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="configActive" class="ml-2 block text-sm text-gray-900">Active</label>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeConfigModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Save Configuration
                </button>
            </div>
        </form>
    `;
}

function closeConfigModal() {
    document.getElementById('configModal').classList.add('hidden');
}

function retryConnection(integrationId) {
    fetch(`/api/calendar/${integrationId}/retry`, {
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
        } else {
            alert('Error retrying connection: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error retrying connection');
    });
}

function removeIntegration(integrationId) {
    if (confirm('Are you sure you want to remove this integration?')) {
        fetch(`/api/calendar/${integrationId}`, {
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
                alert('Error removing integration');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing integration');
        });
    }
}

// Handle sync settings form
document.getElementById('syncSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        sync_frequency: formData.get('sync_frequency'),
        sync_direction: formData.get('sync_direction'),
        auto_sync: document.getElementById('auto_sync').checked,
        conflict_resolution: document.getElementById('conflict_resolution').checked
    };
    
    fetch('/api/calendar/settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully');
        } else {
            alert('Error saving settings');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving settings');
    });
});

// Handle config form submission when it's dynamically created
document.addEventListener('submit', function(e) {
    if (e.target.id === 'configForm') {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const integrationId = formData.get('integrationId') || document.getElementById('integrationId').value;
        const data = {
            name: formData.get('name'),
            sync_direction: formData.get('sync_direction'),
            is_active: document.getElementById('configActive').checked
        };
        
        fetch(`/api/calendar/${integrationId}/config`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeConfigModal();
                location.reload();
            } else {
                alert('Error saving configuration');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving configuration');
        });
    }
});
</script>
@endpush
