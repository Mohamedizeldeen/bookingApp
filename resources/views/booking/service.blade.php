@extends('layouts.guest')

@section('title', 'Book ' . $service->name . ' - ' . $company->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-alt text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Book {{ $service->name }}</h1>
            <p class="text-lg text-gray-600">with {{ $company->name }}</p>
        </div>

        <!-- Service Information Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $service->name }}</h3>
                    @if($service->description)
                        <p class="text-gray-600 mb-4">{{ $service->description }}</p>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-clock mr-2 text-blue-500"></i>
                            <span>Duration: {{ $service->duration_minutes }} minutes</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-dollar-sign mr-2 text-green-500"></i>
                            <span>Price: ${{ number_format($service->price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Book Your Appointment</h2>
                <p class="text-gray-600 mt-1">Fill in your details to schedule your {{ $service->name }} appointment</p>
            </div>
            
            <div class="p-6">
                <form action="{{ route('booking.store', $company->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                    
                    <!-- Customer Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="customer_name" 
                                   id="customer_name" 
                                   required
                                   value="{{ old('customer_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('customer_name') border-red-300 @enderror">
                            @error('customer_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="customer_email" 
                                   id="customer_email" 
                                   required
                                   value="{{ old('customer_email') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('customer_email') border-red-300 @enderror">
                            @error('customer_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               name="customer_phone" 
                               id="customer_phone" 
                               required
                               value="{{ old('customer_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('customer_phone') border-red-300 @enderror">
                        @error('customer_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Appointment Date -->
                    <div>
                        <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Date & Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="appointment_date" 
                               id="appointment_date" 
                               required
                               min="{{ date('Y-m-d\TH:i', strtotime('+1 hour')) }}"
                               value="{{ old('appointment_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('appointment_date') border-red-300 @enderror">
                        @error('appointment_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Please select a date and time at least 1 hour in advance</p>
                    </div>
                    
                    <!-- Additional Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3"
                                  placeholder="Any special requests or information we should know about..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('booking.page', $company->id) }}" 
                           class="text-gray-600 hover:text-gray-800 font-medium">
                            ← Back to Services
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-8 py-3 rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Book Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Company Info -->
        <div class="mt-8 text-center">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $company->name }}</h3>
                @if($company->description)
                    <p class="text-gray-600 mb-4">{{ $company->description }}</p>
                @endif
                
                <div class="flex flex-wrap justify-center gap-4 text-sm text-gray-600">
                    @if($company->phone)
                        <div class="flex items-center">
                            <i class="fas fa-phone mr-1 text-blue-500"></i>
                            <span>{{ $company->phone }}</span>
                        </div>
                    @endif
                    @if($company->email)
                        <div class="flex items-center">
                            <i class="fas fa-envelope mr-1 text-blue-500"></i>
                            <span>{{ $company->email }}</span>
                        </div>
                    @endif
                    @if($company->website)
                        <div class="flex items-center">
                            <i class="fas fa-globe mr-1 text-blue-500"></i>
                            <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                Website
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
