@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile-First Header -->
    <div class="bg-white shadow-sm border-b lg:hidden">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-check text-purple-600 mr-2"></i>
                        Appointments
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $appointments->total() }} total</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('appointments.today') }}" 
                       class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calendar-day text-sm"></i>
                    </a>
                    <a href="{{ route('appointments.create') }}" 
                       class="bg-purple-600 text-white p-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Header -->
    <div class="hidden lg:block bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-check text-purple-600 mr-3"></i>
                        All Appointments
                    </h1>
                    <p class="text-gray-600 mt-2 flex items-center">
                        <i class="fas fa-building text-gray-400 mr-2"></i>
                        Manage all appointments for {{ auth()->user()->company->company_name }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('appointments.today') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors flex items-center">
                        <i class="fas fa-calendar-day mr-2"></i>
                        Today's Schedule
                    </a>
                    <a href="{{ route('appointments.create') }}" 
                       class="bg-purple-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Book Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Success/Error Messages -->
        @if(session('status'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    <span class="font-medium">Please fix the following errors:</span>
                </div>
                @foreach($errors->all() as $error)
                    <p class="ml-6">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Filter/Status Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <!-- Mobile Scrollable Tabs -->
                <nav class="flex space-x-1 overflow-x-auto pb-2 lg:space-x-8 lg:overflow-visible">
                    <a href="{{ route('appointments.index') }}" 
                       class="whitespace-nowrap py-2 px-3 border-b-2 {{ !request('status') ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span class="hidden sm:inline">All Appointments</span>
                        <span class="sm:hidden">All</span>
                    </a>
                    <a href="{{ route('appointments.index') }}?status=scheduled" 
                       class="whitespace-nowrap py-2 px-3 border-b-2 {{ request('status') === 'scheduled' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span class="hidden sm:inline">Scheduled</span>
                        <span class="sm:hidden">Scheduled</span>
                    </a>
                    <a href="{{ route('appointments.index') }}?status=confirmed" 
                       class="whitespace-nowrap py-2 px-3 border-b-2 {{ request('status') === 'confirmed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        <span class="hidden sm:inline">Confirmed</span>
                        <span class="sm:hidden">Confirmed</span>
                    </a>
                    <a href="{{ route('appointments.index') }}?status=completed" 
                       class="whitespace-nowrap py-2 px-3 border-b-2 {{ request('status') === 'completed' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm flex items-center">
                        <i class="fas fa-check-double mr-2"></i>
                        <span class="hidden sm:inline">Completed</span>
                        <span class="sm:hidden">Done</span>
                    </a>
                    <a href="{{ route('appointments.index') }}?status=cancelled" 
                       class="whitespace-nowrap py-2 px-3 border-b-2 {{ request('status') === 'cancelled' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        <span class="hidden sm:inline">Cancelled</span>
                        <span class="sm:hidden">Cancelled</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Appointments List -->
        @if($appointments->count() > 0)
            <!-- Desktop View -->
            <div class="hidden lg:block bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-list text-gray-400 mr-2"></i>
                            Appointments List
                        </h3>
                        <div class="text-sm text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            {{ $appointments->total() }} total
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        @foreach($appointments as $appointment)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-purple-600"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                                    <i class="fas fa-user-circle text-gray-400 mr-2"></i>
                                                    {{ $appointment->customer->name }}
                                                </h4>
                                                <p class="text-sm text-gray-600 flex items-center">
                                                    <i class="fas fa-cogs text-gray-400 mr-2"></i>
                                                    {{ $appointment->service->name }}
                                                </p>
                                                <p class="text-sm text-gray-500 flex items-center">
                                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                                    {{ $appointment->customer->email }}
                                                    <i class="fas fa-phone text-gray-400 ml-4 mr-2"></i>
                                                    {{ $appointment->customer->phone }}
                                                </p>
                                                @if($appointment->assignedUser)
                                                    <p class="text-sm text-gray-500 flex items-center">
                                                        <i class="fas fa-user-tie text-gray-400 mr-2"></i>
                                                        Assigned to: {{ $appointment->assignedUser->name }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-medium text-gray-900 flex items-center justify-end">
                                                    <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                                    {{ $appointment->appointment_date->format('M j, Y') }}
                                                </p>
                                                <p class="text-sm text-gray-600 flex items-center justify-end">
                                                    <i class="fas fa-clock text-gray-400 mr-2"></i>
                                                    {{ $appointment->appointment_date->format('g:i A') }}
                                                </p>
                                                <p class="text-sm text-gray-500 flex items-center justify-end">
                                                    <i class="fas fa-stopwatch text-gray-400 mr-2"></i>
                                                    {{ $appointment->service->duration }} min
                                                    <i class="fas fa-dollar-sign text-gray-400 ml-3 mr-1"></i>
                                                    {{ number_format($appointment->service->price, 2) }}
                                                </p>
                                                @if($appointment->appointment_date->isPast())
                                                    <p class="text-xs text-red-500 flex items-center justify-end">
                                                        <i class="fas fa-history mr-1"></i>
                                                        Past appointment
                                                    </p>
                                                @elseif($appointment->appointment_date->isToday())
                                                    <p class="text-xs text-blue-500 flex items-center justify-end">
                                                        <i class="fas fa-star mr-1"></i>
                                                        Today
                                                    </p>
                                                @elseif($appointment->appointment_date->isTomorrow())
                                                    <p class="text-xs text-green-500 flex items-center justify-end">
                                                        <i class="fas fa-arrow-right mr-1"></i>
                                                        Tomorrow
                                                    </p>
                                                @else
                                                    <p class="text-xs text-gray-500 flex items-center justify-end">
                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                        {{ $appointment->appointment_date->diffForHumans() }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="flex flex-col space-y-2">
                                                <span class="px-3 inline-flex text-xs leading-5 font-semibold rounded-full items-center
                                                    @if($appointment->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                                    @elseif($appointment->status === 'scheduled') bg-yellow-100 text-yellow-800
                                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    @if($appointment->status === 'completed')
                                                        <i class="fas fa-check-double mr-1"></i>
                                                    @elseif($appointment->status === 'confirmed')
                                                        <i class="fas fa-check mr-1"></i>
                                                    @elseif($appointment->status === 'scheduled')
                                                        <i class="fas fa-clock mr-1"></i>
                                                    @elseif($appointment->status === 'cancelled')
                                                        <i class="fas fa-times mr-1"></i>
                                                    @endif
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        @if($appointment->notes)
                                            <div class="mt-3 text-sm text-gray-600 bg-gray-50 p-3 rounded flex items-start">
                                                <i class="fas fa-sticky-note text-gray-400 mr-2 mt-0.5"></i>
                                                <div>
                                                    <strong>Notes:</strong> {{ $appointment->notes }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Desktop Action Buttons -->
                                    <div class="ml-6 flex flex-col space-y-2">
                                        @if(auth()->user()->role === 'admin' && in_array($appointment->status, ['scheduled', 'confirmed']))
                                            <button onclick="showAssignModal({{ $appointment->id }}, '{{ $appointment->customer->name }}', '{{ $appointment->assignedUser ? $appointment->assignedUser->name : 'Unassigned' }}')" 
                                                    class="w-full bg-purple-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-purple-700 transition-colors flex items-center justify-center">
                                                <i class="fas fa-user-plus mr-1"></i>
                                                Assign
                                            </button>
                                        @endif
                                        
                                        @php
                                            $canManageAppointment = auth()->user()->role === 'admin' || 
                                                                  $appointment->assigned_user_id === auth()->user()->id;
                                        @endphp
                                        
                                        @if($canManageAppointment && $appointment->status === 'scheduled')
                                            <form action="{{ route('appointments.confirm', $appointment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Confirm
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($canManageAppointment && $appointment->status === 'confirmed')
                                            <form action="{{ route('appointments.complete', $appointment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full bg-green-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-green-700 transition-colors flex items-center justify-center">
                                                    <i class="fas fa-check-double mr-1"></i>
                                                    Complete
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($canManageAppointment && in_array($appointment->status, ['scheduled', 'confirmed']))
                                            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?')" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full bg-red-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-red-700 transition-colors flex items-center justify-center">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(!$canManageAppointment && auth()->user()->role !== 'admin')
                                            <div class="text-xs text-gray-500 italic text-center flex items-center">
                                                <i class="fas fa-lock text-gray-400 mr-1"></i>
                                                Not assigned
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Mobile View -->
            <div class="lg:hidden space-y-4">
                @foreach($appointments as $appointment)
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                        <!-- Mobile Card Header -->
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $appointment->customer->name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $appointment->service->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full items-center
                                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($appointment->status === 'scheduled') bg-yellow-100 text-yellow-800
                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($appointment->status === 'completed')
                                            <i class="fas fa-check-double mr-1"></i>
                                        @elseif($appointment->status === 'confirmed')
                                            <i class="fas fa-check mr-1"></i>
                                        @elseif($appointment->status === 'scheduled')
                                            <i class="fas fa-clock mr-1"></i>
                                        @elseif($appointment->status === 'cancelled')
                                            <i class="fas fa-times mr-1"></i>
                                        @endif
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Card Content -->
                        <div class="px-4 py-3">
                            <div class="space-y-3">
                                <!-- Date & Time -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                        {{ $appointment->appointment_date->format('M j, Y') }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                                        {{ $appointment->appointment_date->format('g:i A') }}
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="space-y-1">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-envelope text-gray-400 mr-2 w-4"></i>
                                        {{ $appointment->customer->email }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                                        {{ $appointment->customer->phone }}
                                    </div>
                                </div>

                                <!-- Service Details -->
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-stopwatch text-gray-400 mr-2"></i>
                                        {{ $appointment->service->duration }} min
                                    </div>
                                    <div class="flex items-center text-gray-900 font-medium">
                                        <i class="fas fa-dollar-sign text-gray-400 mr-1"></i>
                                        {{ number_format($appointment->service->price, 2) }}
                                    </div>
                                </div>

                                @if($appointment->assignedUser)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-user-tie text-gray-400 mr-2"></i>
                                        Assigned to: {{ $appointment->assignedUser->name }}
                                    </div>
                                @endif

                                @if($appointment->notes)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-start text-sm text-gray-600">
                                            <i class="fas fa-sticky-note text-gray-400 mr-2 mt-0.5"></i>
                                            <div>
                                                <span class="font-medium">Notes:</span> {{ $appointment->notes }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Time Indicator -->
                                @if($appointment->appointment_date->isPast())
                                    <div class="flex items-center text-xs text-red-500">
                                        <i class="fas fa-history mr-1"></i>
                                        Past appointment
                                    </div>
                                @elseif($appointment->appointment_date->isToday())
                                    <div class="flex items-center text-xs text-blue-500">
                                        <i class="fas fa-star mr-1"></i>
                                        Today
                                    </div>
                                @elseif($appointment->appointment_date->isTomorrow())
                                    <div class="flex items-center text-xs text-green-500">
                                        <i class="fas fa-arrow-right mr-1"></i>
                                        Tomorrow
                                    </div>
                                @else
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ $appointment->appointment_date->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Mobile Action Buttons -->
                        @php
                            $canManageAppointment = auth()->user()->role === 'admin' || 
                                                  $appointment->assigned_user_id === auth()->user()->id;
                        @endphp

                        @if($canManageAppointment || auth()->user()->role === 'admin')
                            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                                <div class="flex space-x-2">
                                    @if(auth()->user()->role === 'admin' && in_array($appointment->status, ['scheduled', 'confirmed']))
                                        <button onclick="showAssignModal({{ $appointment->id }}, '{{ $appointment->customer->name }}', '{{ $appointment->assignedUser ? $appointment->assignedUser->name : 'Unassigned' }}')" 
                                                class="flex-1 bg-purple-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-purple-700 transition-colors flex items-center justify-center">
                                            <i class="fas fa-user-plus mr-1"></i>
                                            Assign
                                        </button>
                                    @endif
                                    
                                    @if($canManageAppointment && $appointment->status === 'scheduled')
                                        <form action="{{ route('appointments.confirm', $appointment) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center">
                                                <i class="fas fa-check mr-1"></i>
                                                Confirm
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($canManageAppointment && $appointment->status === 'confirmed')
                                        <form action="{{ route('appointments.complete', $appointment) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-green-700 transition-colors flex items-center justify-center">
                                                <i class="fas fa-check-double mr-1"></i>
                                                Complete
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($canManageAppointment && in_array($appointment->status, ['scheduled', 'confirmed']))
                                        <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to cancel this appointment?')" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full bg-red-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-red-700 transition-colors flex items-center justify-center">
                                                <i class="fas fa-times mr-1"></i>
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                @if(!$canManageAppointment && auth()->user()->role !== 'admin')
                                    <div class="text-xs text-gray-500 italic text-center mt-2 flex items-center justify-center">
                                        <i class="fas fa-lock text-gray-400 mr-1"></i>
                                        Not assigned to you
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($appointments->hasPages())
                <div class="mt-6">
                    {{ $appointments->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-alt text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No appointments found</h3>
                <p class="text-gray-500 mb-6 max-w-sm mx-auto">Start by creating your first appointment or share your booking link with customers.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ route('appointments.create') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Book Appointment
                    </a>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Assignment Modal -->
    <div id="assignModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form id="assignForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Assign Appointment
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" id="appointmentDetails">
                                        Assign this appointment to a staff member.
                                    </p>
                                    <div class="mt-4">
                                        <label for="assigned_user_id" class="block text-sm font-medium text-gray-700">Select Staff Member *</label>
                                        <select name="assigned_user_id" id="assigned_user_id" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                            <option value="">Select staff member...</option>
                                            @foreach(auth()->user()->company->users as $user)
                                                <option value="{{ $user->id }}">
                                                    {{ $user->name }} {{ $user->role === 'admin' ? '(Admin)' : '(Staff)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Currently assigned: <span id="currentAssignment"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Assign Staff
                        </button>
                        <button type="button" onclick="closeAssignModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showAssignModal(appointmentId, customerName, currentAssignment) {
            // Set the form action URL
            document.getElementById('assignForm').action = `/appointments/${appointmentId}/assign`;
            
            // Update modal content
            document.getElementById('appointmentDetails').textContent = `Assign appointment with ${customerName} to a staff member.`;
            document.getElementById('currentAssignment').textContent = currentAssignment;
            
            // Reset the select
            document.getElementById('assigned_user_id').value = '';
            
            // Show the modal
            document.getElementById('assignModal').classList.remove('hidden');
        }

        function closeAssignModal() {
            // Hide the modal
            document.getElementById('assignModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('assignModal').addEventListener('click', function(e) {
@endsection

<!-- Assign Staff Modal -->
<div id="assignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-user-plus text-purple-600 mr-2"></i>
                    Assign Staff
                </h3>
                <button type="button" onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="assignForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check text-gray-400 mr-1"></i>
                        Appointment
                    </label>
                    <p id="appointmentInfo" class="text-sm text-gray-600 bg-gray-50 p-2 rounded"></p>
                </div>
                <div class="mb-4">
                    <label for="assigned_user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tie text-gray-400 mr-1"></i>
                        Assign to Staff Member
                    </label>
                    <select name="assigned_user_id" id="assigned_user_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Select staff member...</option>
                        @if(isset($staffMembers))
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }} ({{ ucfirst($staff->role) }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAssignModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-user-plus mr-2"></i>
                        Assign Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showAssignModal(appointmentId, customerName, currentAssignment) {
        const modal = document.getElementById('assignModal');
        const form = document.getElementById('assignForm');
        const appointmentInfo = document.getElementById('appointmentInfo');
        
        // Set form action
        form.action = `/appointments/${appointmentId}/assign`;
        
        // Set appointment info
        appointmentInfo.textContent = `${customerName} - Currently: ${currentAssignment}`;
        
        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAssignModal() {
        const modal = document.getElementById('assignModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close modal when clicking outside
    document.getElementById('assignModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAssignModal();
        }
    });
</script>
