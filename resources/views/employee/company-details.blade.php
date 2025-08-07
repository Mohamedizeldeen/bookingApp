<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->company_name }} - Employee View</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-purple-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('employee.dashboard') }}" class="text-purple-200 hover:text-white mr-4">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">{{ $company->company_name }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-purple-200">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded text-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Company Header -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Company Information</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Payment and subscription details for {{ $company->company_name }}</p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $company->company_name }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Owner</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $company->owner->name ?? 'No owner assigned' }}
                            @if($company->owner)
                                <br><span class="text-gray-500">{{ $company->owner->email }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($company->is_blocked)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Blocked
                                </span>
                                @if($company->block_reason)
                                    <div class="mt-2 text-sm text-red-600">
                                        <strong>Reason:</strong> {{ $company->block_reason }}
                                    </div>
                                @endif
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($company->subscription_status) }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Monthly Fee</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="text-lg font-semibold">${{ number_format($company->monthly_fee ?? 0) }}</span> per month
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($company->last_payment_date)
                                <div class="mb-2">
                                    <strong>Last Payment:</strong> {{ $company->last_payment_date->format('M j, Y') }}
                                    <span class="text-gray-500">({{ $company->last_payment_date->diffForHumans() }})</span>
                                </div>
                            @endif
                            
                            @if($company->next_payment_due)
                                <div class="{{ $company->next_payment_due->isPast() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                    <strong>Next Payment Due:</strong> {{ $company->next_payment_due->format('M j, Y') }}
                                    @if($company->next_payment_due->isPast())
                                        <div class="text-red-500 mt-1">
                                            ⚠️ Overdue by {{ $company->next_payment_due->diffForHumans(null, true) }}
                                        </div>
                                    @else
                                        <span class="text-gray-500">({{ $company->next_payment_due->diffForHumans() }})</span>
                                    @endif
                                </div>
                            @else
                                <div class="text-gray-500">No payment schedule set</div>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Company Statistics</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <div class="text-lg font-semibold">{{ $company->users->count() }}</div>
                                    <div class="text-xs text-gray-500">Users</div>
                                </div>
                                <div>
                                    <div class="text-lg font-semibold">{{ $company->services->count() }}</div>
                                    <div class="text-xs text-gray-500">Services</div>
                                </div>
                                <div>
                                    <div class="text-lg font-semibold">{{ $company->appointments->count() }}</div>
                                    <div class="text-xs text-gray-500">Total Appointments</div>
                                </div>
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Payment Action -->
        @if($company->subscription_status !== 'active' || ($company->next_payment_due && $company->next_payment_due->isPast()))
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">Payment Required</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>This company needs to make a payment. Once you confirm payment has been received, click the button below to update their subscription.</p>
                    </div>
                    <div class="mt-4">
                        <form action="{{ route('employee.company.mark-payment', $company) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to mark payment as received for {{ $company->company_name }}? This will extend their subscription for one month.')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Mark Payment Received
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Payment Up to Date</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>This company's subscription is active and payments are current.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Services List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Services ({{ $company->services->count() }})</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">All services offered by this company</p>
            </div>
            @if($company->services->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($company->services as $service)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $service->service_name }}</div>
                                    <div class="text-sm text-gray-500">
                                        ${{ number_format($service->price, 2) }} • {{ $service->duration }} minutes
                                        @if(!$service->is_active)
                                            <span class="text-red-600"> • Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $service->appointments->count() }} appointments
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="px-6 py-4 text-center text-gray-500">
                    No services created yet
                </div>
            @endif
        </div>

        <!-- Users List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Users ({{ $company->users->count() }})</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">All users in this company</p>
            </div>
            @if($company->users->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($company->users as $user)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div class="text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="px-6 py-4 text-center text-gray-500">
                    No users found
                </div>
            @endif
        </div>
    </div>

    @if(session('status'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <script>
        // Auto-hide status messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.fixed.bottom-4');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
