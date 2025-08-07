@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile-First Header -->
    <div class="bg-white shadow-sm border-b lg:hidden">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-cogs text-green-600 mr-2"></i>
                        Services
                    </h1>
                    <p class="text-sm text-gray-600 mt-1 flex items-center">
                        <i class="fas fa-list-ul text-gray-400 mr-1"></i>
                        {{ $services->count() }} available
                    </p>
                </div>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('services.create') }}" 
                       class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus text-sm"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Desktop Header -->
    <div class="hidden lg:block bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-cogs text-green-600 mr-3"></i>
                        Manage Services
                    </h1>
                    <p class="text-gray-600 mt-2 flex items-center">
                        <i class="fas fa-building text-gray-400 mr-2"></i>
                        Create and manage services offered by your business
                        <i class="fas fa-chart-bar text-gray-400 ml-4 mr-2"></i>
                        {{ $services->count() }} total services
                    </p>
                </div>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('services.create') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Service
                    </a>
                @endif
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

        @if($services->count() > 0)
            <!-- Desktop Grid View -->
            <div class="hidden lg:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Service Header -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                                    {{ $service->name }}
                                </h3>
                                @if(auth()->user()->role === 'admin')
                                    <div class="flex space-x-2">
                                        <a href="{{ route('services.edit', $service) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('services.destroy', $service) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this service?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center">
                                                <i class="fas fa-trash mr-1"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                            
                            @if($service->description)
                                <p class="text-gray-600 text-sm mb-4 flex items-start">
                                    <i class="fas fa-align-left text-gray-400 mr-2 mt-0.5"></i>
                                    {{ $service->description }}
                                </p>
                            @endif
                        </div>
                        
                        <!-- Service Details -->
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 flex items-center">
                                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                                        Duration:
                                    </span>
                                    <span class="text-sm text-gray-900 font-medium">{{ $service->duration }} minutes</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 flex items-center">
                                        <i class="fas fa-dollar-sign text-gray-400 mr-2"></i>
                                        Price:
                                    </span>
                                    <span class="text-sm font-semibold text-green-600 flex items-center">
                                        <i class="fas fa-money-bill-wave mr-1"></i>
                                        ${{ number_format($service->price, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Service Footer -->
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-calendar-plus text-gray-400 mr-1"></i>
                                    Created {{ $service->created_at->diffForHumans() }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Mobile List View -->
            <div class="lg:hidden space-y-4">
                @foreach($services as $service)
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                        <!-- Mobile Service Header -->
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-star text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $service->name }}</h3>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-money-bill-wave mr-1"></i>
                                            ${{ number_format($service->price, 2) }}
                                        </p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            </div>
                        </div>

                        <!-- Mobile Service Content -->
                        <div class="px-4 py-3">
                            <div class="space-y-3">
                                @if($service->description)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-start text-sm text-gray-600">
                                            <i class="fas fa-align-left text-gray-400 mr-2 mt-0.5"></i>
                                            {{ $service->description }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Service Details -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-clock text-gray-400 mr-2"></i>
                                            Duration
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $service->duration }} minutes</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-calendar-plus text-gray-400 mr-2"></i>
                                            Created
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $service->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Action Buttons -->
                        @if(auth()->user()->role === 'admin')
                            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                                <div class="flex space-x-2">
                                    <a href="{{ route('services.edit', $service) }}" 
                                       class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('services.destroy', $service) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this service?')" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-red-700 transition-colors flex items-center justify-center">
                                            <i class="fas fa-trash mr-1"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-cogs text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center justify-center">
                    <i class="fas fa-info-circle text-gray-400 mr-2"></i>
                    No services available
                </h3>
                <p class="text-gray-500 mb-6 max-w-sm mx-auto">Start building your service catalog by creating your first service offering.</p>
                @if(auth()->user()->role === 'admin')
                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <a href="{{ route('services.create') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Add First Service
                        </a>
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Admin Only Notice -->
        @if(auth()->user()->role !== 'admin')
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-medium text-blue-800 flex items-center">
                            <i class="fas fa-user-shield mr-2"></i>
                            Staff Access Information
                        </h3>
                        <p class="mt-1 text-sm text-blue-700">You can view all available services, but only company admins can create, edit, or delete services.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
