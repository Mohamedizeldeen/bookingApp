<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - {{ $appointment->company->company_name }}</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-2">Booking Confirmed!</h1>
            <p class="text-xl text-purple-100">Your appointment has been successfully scheduled</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Confirmation Details -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                <h2 class="text-xl font-bold text-green-800">Appointment Details</h2>
                <p class="text-green-600">Please save this information for your records</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Customer Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Name:</span>
                                <span class="ml-2 text-gray-900">{{ $appointment->customer->name }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Email:</span>
                                <span class="ml-2 text-gray-900">{{ $appointment->customer->email }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Phone:</span>
                                <span class="ml-2 text-gray-900">{{ $appointment->customer->phone }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Information</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Service:</span>
                                <span class="ml-2 text-gray-900">{{ $appointment->service->name }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Date & Time:</span>
                                <span class="ml-2 text-gray-900">{{ $appointment->appointment_date->format('l, M j, Y \a\t g:i A') }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Duration:</span>
                                <span class="ml-2 text-gray-900">{{ $appointment->service->duration }} minutes</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Price:</span>
                                <span class="ml-2 text-gray-900 font-semibold">${{ number_format($appointment->service->price, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Status:</span>
                                <span class="ml-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($appointment->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes</h3>
                        <p class="text-gray-600">{{ $appointment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Company Information -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $appointment->company->company_name }}</h3>
                
                @if($appointment->company->phone)
                    <div class="flex items-center text-gray-600 mb-3">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        {{ $appointment->company->phone }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">What happens next?</h3>
            <div class="space-y-3 text-blue-800">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>We'll review your appointment request and confirm availability</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>You'll receive a confirmation call or email within 24 hours</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Please arrive 10 minutes early for your appointment</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>If you need to reschedule, please contact us as soon as possible</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
            <button onclick="window.print()" 
                    class="px-6 py-3 bg-gray-600 text-white rounded-md font-semibold hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Print Confirmation
            </button>
            
            <a href="{{ route('booking.page', [$appointment->company->id, \Illuminate\Support\Str::slug($appointment->company->company_name)]) }}" 
               class="px-6 py-3 bg-purple-600 text-white rounded-md font-semibold hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 text-center">
                Book Another Appointment
            </a>
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
