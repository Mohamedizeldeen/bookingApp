<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - {{ $company->company_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="gradient-bg text-white py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-2">{{ $company->company_name }}</h1>
            <p class="text-xl text-purple-100">Book Your Appointment Online</p>
            @if($company->phone)
                <p class="text-purple-200 mt-2">📞 {{ $company->phone }}</p>
            @endif
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
                        <h2 class="text-xl font-bold">Book Your Appointment</h2>
                        <p class="text-purple-100">Fill out the form below to schedule your appointment</p>
                    </div>
                    
                    <form action="{{ route('booking.store', $company->id) }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                           placeholder="Your full name">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                           placeholder="your@email.com">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       placeholder="(555) 123-4567">
                            </div>
                        </div>

                        <!-- Service Selection -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900">Select Service</h3>
                            
                            <div class="space-y-3">
                                @foreach($services as $service)
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="service_id" value="{{ $service->id }}" 
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                               {{ old('service_id') == $service->id ? 'checked' : '' }} required>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-medium text-gray-900">{{ $service->name }}</h4>
                                                <div class="text-right">
                                                    <p class="font-semibold text-purple-600">${{ number_format($service->price, 2) }}</p>
                                                    <p class="text-sm text-gray-500">{{ $service->duration }} min</p>
                                                </div>
                                            </div>
                                            @if($service->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $service->description }}</p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Appointment Date & Time -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900">Preferred Date & Time</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time *</label>
                                <input type="datetime-local" name="appointment_date" value="{{ old('appointment_date') }}" required
                                       min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <p class="text-xs text-gray-500 mt-1">Please select your preferred appointment time. We'll confirm availability and contact you shortly.</p>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900">Additional Information</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Special Notes or Requests</label>
                                <textarea name="notes" rows="3" placeholder="Any special requests or notes for your appointment..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('notes') }}</textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Preferences</label>
                                <textarea name="preferences" rows="2" placeholder="Any preferences or requirements we should know about..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('preferences') }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 px-6 rounded-md font-semibold hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition duration-200">
                                Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Company Information Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden sticky top-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">About {{ $company->company_name }}</h3>
                        
                        <!-- Subscription Type Badge -->
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($company->type_of_subscription === 'enterprise') bg-purple-100 text-purple-800
                                @elseif($company->type_of_subscription === 'professional') bg-blue-100 text-blue-800
                                @elseif($company->type_of_subscription === 'starter') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($company->type_of_subscription) }} Plan
                            </span>
                        </div>

                        <!-- Contact Information -->
                        @if($company->phone)
                            <div class="flex items-center text-gray-600 mb-3">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                {{ $company->phone }}
                            </div>
                        @endif

                        <!-- Services Count -->
                        <div class="flex items-center text-gray-600 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            {{ $services->count() }} Services Available
                        </div>

                        <!-- Booking Process -->
                        <div class="border-t pt-4">
                            <h4 class="font-medium text-gray-900 mb-3">How it works:</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-5 h-5 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xs font-semibold mr-2 mt-0.5">1</span>
                                    <span>Fill out the booking form</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-5 h-5 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xs font-semibold mr-2 mt-0.5">2</span>
                                    <span>We'll review your request</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-5 h-5 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xs font-semibold mr-2 mt-0.5">3</span>
                                    <span>Confirmation via email/phone</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-5 h-5 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xs font-semibold mr-2 mt-0.5">4</span>
                                    <span>Attend your appointment</span>
                                </div>
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div class="border-t pt-4 mt-4">
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                Your information is secure and private
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">Powered by BookingApp - Professional Appointment Management</p>
        </div>
    </footer>
</body>
</html>
