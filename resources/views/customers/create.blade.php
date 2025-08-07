@extends('layouts.app')

@section('title', 'Add New Customer')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile-First Header -->
    <div class="bg-white shadow-sm border-b lg:hidden">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                        Add Customer
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Create new customer profile</p>
                </div>
                <a href="{{ route('customers.index') }}" 
                   class="bg-gray-600 text-white p-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Desktop Header -->
    <div class="hidden lg:block bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
                            <li class="inline-flex items-center">
                                <a href="{{ route('customers.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
                                    <i class="fas fa-users mr-2"></i>
                                    Customers
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-500">Add New Customer</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                        Add New Customer
                    </h1>
                    <p class="text-gray-600 mt-2">Create a new customer profile for your company</p>
                </div>
                <a href="{{ route('customers.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Customers
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Success/Error Messages -->
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                <span class="font-medium">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-6 space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Customer Form -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <form action="{{ route('customers.store') }}" method="POST" class="p-4 sm:p-6 lg:p-8">
            @csrf
            
            <!-- Basic Information -->
            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tag text-gray-400 mr-1"></i>
                            Full Name *
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               class="w-full px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base" 
                               placeholder="Enter customer's full name"
                               required>
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-birthday-cake text-gray-400 mr-1"></i>
                            Date of Birth
                        </label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" 
                               class="w-full px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base">
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                    <i class="fas fa-envelope text-blue-600 mr-2"></i>
                    Contact Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-at text-gray-400 mr-1"></i>
                            Email Address *
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="w-full px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base" 
                               placeholder="customer@example.com"
                               required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone text-gray-400 mr-1"></i>
                            Phone Number *
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                               class="w-full px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base" 
                               placeholder="+1 (555) 123-4567"
                               required>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                    <i class="fas fa-cog text-blue-600 mr-2"></i>
                    Additional Information
                </h3>
                
                <div class="space-y-4 sm:space-y-6">
                    <div>
                        <label for="preferences" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-heart text-gray-400 mr-1"></i>
                            Preferences
                        </label>
                        <input type="text" id="preferences" name="preferences" value="{{ old('preferences') }}" 
                               placeholder="e.g., morning appointments, specific staff member, allergies"
                               class="w-full px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base">
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Enter preferences separated by commas</p>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note text-gray-400 mr-1"></i>
                            Notes
                        </label>
                        <textarea id="notes" name="notes" rows="3" 
                                  placeholder="Any additional notes about this customer..."
                                  class="w-full px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm sm:text-base resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('customers.index') }}" 
                   class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200 text-center text-sm sm:text-base font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 text-sm sm:text-base font-medium">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Customer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-format phone number
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 6) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 3) {
            value = value.replace(/(\d{3})(\d{3})/, '($1) $2');
        }
        e.target.value = value;
    });
</script>
@endsection
