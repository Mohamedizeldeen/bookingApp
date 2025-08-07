<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service - BookingApp</title>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 002 2h2a2 2 0 002-2V6"></path>
                        </svg>
                    </div>
                    <span class="ml-2 text-xl font-bold text-gray-900">BookingApp</span>
                    <span class="ml-4 text-sm text-gray-500">{{ auth()->user()->company->company_name }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                    <a href="{{ route('services.index') }}" class="text-gray-500 hover:text-gray-700">Services</a>
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

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Authorization Check -->
        @if(auth()->user()->role !== 'admin')
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Access Denied</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Only company administrators can edit services.</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('services.index') }}" 
                               class="text-sm font-medium text-red-800 hover:text-red-600">
                                Back to Services →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
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
                <h1 class="text-3xl font-bold text-gray-900">Edit Service</h1>
                <p class="text-gray-600">Update the details for "{{ $service->name }}"</p>
            </div>

            <!-- Service Form -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <form action="{{ route('services.update', $service) }}" method="POST" class="px-6 py-6 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Service Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Service Name</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               required
                               value="{{ old('name', $service->name) }}"
                               placeholder="e.g., Haircut, Massage, Consultation"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <p class="mt-2 text-sm text-gray-500">
                            Enter a clear, descriptive name for your service.
                        </p>
                    </div>

                    <!-- Service Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3" 
                                  placeholder="Describe what this service includes, any special features, or requirements..."
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">{{ old('description', $service->description) }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">
                            Help customers understand what to expect from this service.
                        </p>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700">Duration (Minutes)</label>
                        <input type="number" 
                               name="duration" 
                               id="duration" 
                               required
                               min="1"
                               max="480"
                               value="{{ old('duration', $service->duration_minutes) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <p class="mt-2 text-sm text-gray-500">
                            How long does this service typically take? (Maximum 8 hours)
                        </p>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price ($)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   name="price" 
                                   id="price" 
                                   required
                                   min="0"
                                   step="0.01"
                                   value="{{ old('price', $service->price) }}"
                                   placeholder="0.00"
                                   class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Enter the price customers will pay for this service.
                        </p>
                    </div>

                    <!-- Service Availability -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Service Status</label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Service is active and available for booking</span>
                            </label>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Inactive services won't be available for customer booking.
                        </p>
                    </div>

                    <!-- Service Preview -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Service Preview</h4>
                        <div class="border border-gray-200 rounded-lg p-4 bg-white">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h5 id="preview-name" class="font-semibold text-gray-900">{{ $service->name }}</h5>
                                    <p id="preview-description" class="text-sm text-gray-600 mt-1">{{ $service->description ?: 'Service description will appear here' }}</p>
                                </div>
                                <div class="text-right">
                                    <p id="preview-price" class="text-lg font-bold text-purple-600">${{ number_format($service->price, 2) }}</p>
                                    <p id="preview-duration" class="text-sm text-gray-500">{{ $service->duration_minutes }} minutes</p>
                                </div>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">This is how your service will appear to customers.</p>
                    </div>

                    <!-- Danger Zone -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-red-800 mb-2">Danger Zone</h4>
                            <p class="text-sm text-red-700 mb-3">
                                Deleting this service will permanently remove it and may affect existing appointments.
                            </p>
                            <button type="button" 
                                    onclick="confirmDelete()"
                                    class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded">
                                Delete Service
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('services.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Update Service
                        </button>
                    </div>
                </form>
            </div>

            <!-- Hidden Delete Form -->
            <form id="delete-form" action="{{ route('services.destroy', $service) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    <script>
        // Live preview update
        function updatePreview() {
            const name = document.getElementById('name').value || 'Your Service Name';
            const description = document.getElementById('description').value || 'Service description will appear here';
            const price = document.getElementById('price').value || '0.00';
            const duration = document.getElementById('duration').value || '60';
            
            document.getElementById('preview-name').textContent = name;
            document.getElementById('preview-description').textContent = description;
            document.getElementById('preview-price').textContent = '$' + parseFloat(price).toFixed(2);
            document.getElementById('preview-duration').textContent = duration + ' minutes';
        }

        // Add event listeners
        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('description').addEventListener('input', updatePreview);
        document.getElementById('price').addEventListener('input', updatePreview);
        document.getElementById('duration').addEventListener('input', updatePreview);

        // Delete confirmation
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this service? This action cannot be undone and may affect existing appointments.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</body>
</html>
