<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Subscription Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-purple-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <div class="flex items-center">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                        <i class="fas fa-calendar-check text-white text-xs sm:text-sm"></i>
                    </div>
                    <h1 class="text-lg sm:text-xl font-bold truncate">
                        <span class="hidden sm:inline">BookingApp - Subscription Checker</span>
                        <span class="sm:hidden">BookingApp</span>
                    </h1>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <span class="text-purple-200 text-sm hidden sm:block">{{ Auth::user()->name }}</span>
                    <span class="text-purple-200 text-sm sm:hidden">{{ substr(Auth::user()->name, 0, 10) }}{{ strlen(Auth::user()->name) > 10 ? '...' : '' }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-2 sm:px-3 py-1 sm:py-2 rounded text-xs sm:text-sm transition-colors">
                            <i class="fas fa-sign-out-alt sm:mr-1"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
        <!-- Dashboard Header -->
        <div class="mb-6 sm:mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-chart-line text-purple-600 mr-2 sm:mr-3"></i>
                <span class="hidden sm:inline">Subscription Checker Dashboard</span>
                <span class="sm:hidden">Dashboard</span>
            </h2>
            <p class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base">Monitor company subscriptions and payment status</p>
        </div>

        <!-- Priority Alerts -->
        @if($overduePayments->count() > 0 || $expiringSoon->count() > 0)
        <div class="mb-4 sm:mb-6">
            @if($overduePayments->count() > 0)
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-2 sm:ml-3">
                        <h3 class="text-xs sm:text-sm font-medium text-red-800 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Overdue Payments ({{ $overduePayments->count() }})
                        </h3>
                        <div class="mt-1 sm:mt-2 text-xs sm:text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($overduePayments->take(5) as $company)
                                <li>
                                    <a href="{{ route('employee.company.view', $company) }}" class="underline hover:text-red-900">
                                        {{ $company->company_name }}
                                    </a>
                                    - {{ $company->next_payment_due->diffForHumans() }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($expiringSoon->count() > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Expiring Soon ({{ $expiringSoon->count() }})</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($expiringSoon->take(5) as $company)
                                <li>
                                    <a href="{{ route('employee.company.view', $company) }}" class="underline hover:text-yellow-900">
                                        {{ $company->company_name }}
                                    </a>
                                    - {{ $company->getDaysUntilExpiry() }} days remaining
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ $activeCompanies->count() }}</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $activeCompanies->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ $pendingCompanies->count() }}</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $pendingCompanies->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ $expiredCompanies->count() }}</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Expired</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $expiredCompanies->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ $blockedCompanies->count() }}</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Blocked</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $blockedCompanies->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ $overduePayments->count() }}</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Overdue</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $overduePayments->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Companies Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">All Companies</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Check payment status and mark payments as received</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscription</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($companies as $company)
                        <tr class="hover:bg-gray-50 {{ $company->next_payment_due && $company->next_payment_due->isPast() ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $company->company_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $company->users_count }} users, {{ $company->services_count }} services</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($company->is_blocked)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Blocked
                                    </span>
                                @elseif($company->subscription_status === 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @elseif($company->subscription_status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($company->subscription_status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>${{ number_format($company->monthly_fee ?? 0) }}/month</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($company->next_payment_due)
                                    <div class="{{ $company->next_payment_due->isPast() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                        Due: {{ $company->next_payment_due->format('M j, Y') }}
                                    </div>
                                    @if($company->next_payment_due->isPast())
                                        <div class="text-red-500 text-xs">
                                            Overdue by {{ $company->next_payment_due->diffForHumans(null, true) }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-500">No payment due</span>
                                @endif
                                @if($company->last_payment_date)
                                    <div class="text-xs text-gray-500">
                                        Last: {{ $company->last_payment_date->format('M j, Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('employee.company.view', $company) }}" 
                                       class="text-blue-600 hover:text-blue-900">View</a>
                                    
                                    @if($company->subscription_status !== 'active' || ($company->next_payment_due && $company->next_payment_due->isPast()))
                                        <form action="{{ route('employee.company.mark-payment', $company) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    onclick="return confirm('Mark payment as received for {{ $company->company_name }}?')"
                                                    class="text-green-600 hover:text-green-900">
                                                Mark Paid
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
