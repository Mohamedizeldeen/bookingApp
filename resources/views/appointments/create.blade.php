<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book New Appointment - BookingApp</title>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="ml-2 text-xl font-bold text-gray-900">BookingApp</span>
                    <span class="ml-4 text-sm text-gray-500">{{ auth()->user()->company->company_name }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                    <a href="{{ route('appointments.index') }}" class="text-gray-500 hover:text-gray-700">All Appointments</a>
                    <a href="{{ route('appointments.today') }}" class="text-gray-500 hover:text-gray-700">Today's Schedule</a>
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
            <h1 class="text-3xl font-bold text-gray-900">Book New Appointment</h1>
            <p class="text-gray-600">Create a new appointment for a customer</p>
        </div>

        <!-- Appointment Form -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form action="{{ route('appointments.store') }}" method="POST" class="px-6 py-6 space-y-6">
                @csrf
                
                <!-- Customer Selection -->
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                    <select name="customer_id" id="customer_id" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Select a customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->email }}
                            </option>
                        @endforeach
                    </select>
                    @if($customers->count() === 0)
                        <p class="mt-2 text-sm text-gray-500">
                            No customers available. Customers are automatically created when they book through your public booking page.
                        </p>
                    @endif
                </div>

                <!-- Service Selection -->
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700">Service</label>
                    <select name="service_id" id="service_id" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Select a service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" 
                                    data-duration="{{ $service->duration }}" 
                                    data-price="{{ $service->price }}"
                                    {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} - {{ $service->duration }} min - ${{ number_format($service->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @if($services->count() === 0)
                        <p class="mt-2 text-sm text-red-500">
                            No services available. 
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('services.create') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                                    Create your first service
                                </a>
                            @else
                                Please ask your admin to create services first.
                            @endif
                        </p>
                    @endif
                </div>

                <!-- Staff Assignment -->
                <div>
                    <label for="assigned_user_id" class="block text-sm font-medium text-gray-700">Assign to Staff</label>
                    <select name="assigned_user_id" id="assigned_user_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Auto-assign (myself)</option>
                        @foreach($staff as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} {{ $user->id === auth()->id() ? '(me)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-sm text-gray-500">
                        If no staff is selected, the appointment will be assigned to you.
                    </p>
                </div>

                <!-- Appointment Date & Time -->
                <div>
                    <label for="appointment_date" class="block text-sm font-medium text-gray-700">Appointment Date & Time</label>
                    <input type="datetime-local" 
                           name="appointment_date" 
                           id="appointment_date" 
                           required
                           min="{{ now()->format('Y-m-d\TH:i') }}"
                           value="{{ old('appointment_date') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    <p class="mt-2 text-sm text-gray-500">
                        Select the date and time for the appointment. Must be in the future.
                    </p>
                </div>

                <!-- Service Details (populated by JavaScript) -->
                <div id="service-details" class="hidden">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Service Details</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Duration:</span>
                                <span id="service-duration" class="font-medium text-gray-900"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Price:</span>
                                <span id="service-price" class="font-medium text-gray-900"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea name="notes" 
                              id="notes" 
                              rows="3" 
                              placeholder="Any special requests or notes for this appointment..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">{{ old('notes') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('appointments.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                        Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show service details when service is selected
        document.getElementById('service_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const detailsDiv = document.getElementById('service-details');
            
            if (selectedOption.value) {
                const duration = selectedOption.getAttribute('data-duration');
                const price = selectedOption.getAttribute('data-price');
                
                document.getElementById('service-duration').textContent = duration + ' minutes';
                document.getElementById('service-price').textContent = '$' + parseFloat(price).toFixed(2);
                
                detailsDiv.classList.remove('hidden');
            } else {
                detailsDiv.classList.add('hidden');
            }
        });

        // Trigger the event if there's already a selected service (for form validation errors)
        if (document.getElementById('service_id').value) {
            document.getElementById('service_id').dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>
