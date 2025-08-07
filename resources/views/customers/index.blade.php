@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile-First Header -->
    <div class="bg-white shadow-sm border-b lg:hidden">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-users text-blue-600 mr-2"></i>
                        Customers
                    </h1>
                    <p class="text-sm text-gray-600 mt-1 flex items-center">
                        <i class="fas fa-chart-bar text-gray-400 mr-1"></i>
                        {{ $customers->total() }} total
                    </p>
                </div>
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'user')
                    <a href="{{ route('customers.create') }}" 
                       class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user-plus text-sm"></i>
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
                        <i class="fas fa-users text-blue-600 mr-3"></i>
                        Customers
                    </h1>
                    <p class="text-gray-600 mt-2 flex items-center">
                        <i class="fas fa-building text-gray-400 mr-2"></i>
                        Manage your company's customers
                        <i class="fas fa-chart-bar text-gray-400 ml-4 mr-2"></i>
                        {{ $customers->total() }} total customers
                    </p>
                </div>
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'user')
                    <a href="{{ route('customers.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors flex items-center">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add New Customer
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    <span class="font-medium">Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside ml-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($customers->count() > 0)
            <!-- Desktop Table View -->
            <div class="hidden lg:block bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-list text-gray-400 mr-2"></i>
                        Customer Directory
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-user mr-1"></i>
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-address-card mr-1"></i>
                                    Contact
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-calendar-check mr-1"></i>
                                    Appointments
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-toggle-on mr-1"></i>
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-cogs mr-1"></i>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                <i class="fas fa-id-card text-gray-400 mr-2"></i>
                                                {{ $customer->name }}
                                            </div>
                                            @if($customer->date_of_birth)
                                                <div class="text-sm text-gray-500 flex items-center">
                                                    <i class="fas fa-birthday-cake text-gray-400 mr-2"></i>
                                                    Born: {{ $customer->date_of_birth->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        {{ $customer->email }}
                                    </div>
                                    <div class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                                        {{ $customer->phone }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 flex items-center">
                                        <i class="fas fa-chart-line text-gray-400 mr-2"></i>
                                        {{ $customer->appointments_count }} total
                                    </div>
                                    @if($customer->appointments->count() > 0)
                                        <div class="text-sm text-gray-500 flex items-center">
                                            <i class="fas fa-history text-gray-400 mr-2"></i>
                                            Last: {{ $customer->appointments->first()->appointment_date->format('M d, Y') }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500 flex items-center">
                                            <i class="fas fa-info-circle text-gray-400 mr-2"></i>
                                            No appointments yet
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($customer->is_active)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('customers.show', $customer) }}" 
                                           class="text-blue-600 hover:text-blue-900 flex items-center">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                        
                                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'user')
                                            <a href="{{ route('customers.edit', $customer) }}" 
                                               class="text-purple-600 hover:text-purple-900 flex items-center">
                                                <i class="fas fa-edit mr-1"></i>
                                                Edit
                                            </a>
                                            
                                            @if(!$customer->is_active)
                                                <form action="{{ route('customers.reactivate', $customer) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-600 hover:text-green-900 flex items-center">
                                                        <i class="fas fa-undo mr-1"></i>
                                                        Reactivate
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if(Auth::user()->role === 'admin')
                                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Are you sure you want to {{ $customer->is_active ? 'deactivate' : 'delete' }} this customer?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 flex items-center">
                                                        <i class="fas fa-{{ $customer->is_active ? 'user-times' : 'trash' }} mr-1"></i>
                                                        {{ $customer->is_active ? 'Deactivate' : 'Delete' }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Desktop Pagination -->
                @if($customers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden space-y-4">
                @foreach($customers as $customer)
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                        <!-- Mobile Card Header -->
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $customer->name }}</h3>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-chart-line mr-1"></i>
                                            {{ $customer->appointments_count }} appointments
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    @if($customer->is_active)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Card Content -->
                        <div class="px-4 py-3">
                            <div class="space-y-3">
                                <!-- Contact Info -->
                                <div class="space-y-1">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-envelope text-gray-400 mr-2 w-4"></i>
                                        {{ $customer->email }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                                        {{ $customer->phone }}
                                    </div>
                                    @if($customer->date_of_birth)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-birthday-cake text-gray-400 mr-2 w-4"></i>
                                            Born: {{ $customer->date_of_birth->format('M d, Y') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Appointment Stats -->
                                @if($customer->appointments->count() > 0)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-history text-gray-400 mr-2"></i>
                                            <span class="font-medium">Last appointment:</span>
                                            <span class="ml-1">{{ $customer->appointments->first()->appointment_date->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-info-circle text-gray-400 mr-2"></i>
                                            No appointments yet
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Mobile Action Buttons -->
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                            <div class="flex space-x-2">
                                <a href="{{ route('customers.show', $customer) }}" 
                                   class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </a>
                                
                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'user')
                                    <a href="{{ route('customers.edit', $customer) }}" 
                                       class="flex-1 bg-purple-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-purple-700 transition-colors flex items-center justify-center">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit
                                    </a>
                                @endif
                            </div>
                            
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'user')
                                <div class="flex space-x-2 mt-2">
                                    @if(!$customer->is_active)
                                        <form action="{{ route('customers.reactivate', $customer) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-green-700 transition-colors flex items-center justify-center">
                                                <i class="fas fa-undo mr-1"></i>
                                                Reactivate
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if(Auth::user()->role === 'admin')
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="flex-1"
                                              onsubmit="return confirm('Are you sure you want to {{ $customer->is_active ? 'deactivate' : 'delete' }} this customer?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full bg-red-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-red-700 transition-colors flex items-center justify-center">
                                                <i class="fas fa-{{ $customer->is_active ? 'user-times' : 'trash' }} mr-1"></i>
                                                {{ $customer->is_active ? 'Deactivate' : 'Delete' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Mobile Pagination -->
            @if($customers->hasPages())
                <div class="mt-6 lg:hidden">
                    {{ $customers->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center justify-center">
                    <i class="fas fa-info-circle text-gray-400 mr-2"></i>
                    No customers found
                </h3>
                <p class="text-gray-500 mb-6 max-w-sm mx-auto">Start building your customer base by adding your first customer to the system.</p>
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'user')
                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <a href="{{ route('customers.create') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>
                            Add First Customer
                        </a>
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
