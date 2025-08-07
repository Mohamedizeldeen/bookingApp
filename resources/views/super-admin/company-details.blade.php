<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->company_name }} - Company Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('super-admin.dashboard') }}" class="text-blue-200 hover:text-white mr-4">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">{{ $company->company_name }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-blue-200">{{ Auth::user()->name }}</span>
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
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Detailed information about {{ $company->company_name }}</p>
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
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="flex items-center space-x-4">
                                @if($company->is_blocked)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Blocked
                                    </span>
                                    <form action="{{ route('super-admin.company.unblock', $company) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                            Unblock Company
                                        </button>
                                    </form>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <button onclick="blockCompany({{ $company->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                        Block Company
                                    </button>
                                @endif
                            </div>
                            @if($company->is_blocked && $company->block_reason)
                                <div class="mt-2 text-sm text-red-600">
                                    <strong>Block Reason:</strong> {{ $company->block_reason }}
                                </div>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Subscription</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="flex items-center space-x-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($company->subscription_status) }}
                                </span>
                                <span class="text-gray-600">${{ number_format($company->monthly_fee ?? 0) }}/month</span>
                            </div>
                            @if($company->last_payment_date)
                                <div class="mt-1 text-sm text-gray-500">
                                    Last Payment: {{ $company->last_payment_date->format('M j, Y') }}
                                </div>
                            @endif
                            @if($company->next_payment_due)
                                <div class="text-sm {{ $company->next_payment_due->isPast() ? 'text-red-600' : 'text-gray-500' }}">
                                    Next Payment Due: {{ $company->next_payment_due->format('M j, Y') }}
                                    @if($company->next_payment_due->isPast())
                                        (Overdue)
                                    @endif
                                </div>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Users</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $company->users->count() }} users</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Services</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $company->services->count() }} services</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Total Appointments</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $company->appointments->count() }} appointments</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Update Subscription Section -->
        <div class="bg-white shadow sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Update Subscription</h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Modify subscription details and payment information.</p>
                </div>
                <form action="{{ route('super-admin.company.subscription.update', $company) }}" method="POST" class="mt-5">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-2">
                            <label for="subscription_status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="subscription_status" name="subscription_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="active" {{ $company->subscription_status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ $company->subscription_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="expired" {{ $company->subscription_status === 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="cancelled" {{ $company->subscription_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="monthly_fee" class="block text-sm font-medium text-gray-700">Monthly Fee ($)</label>
                            <input type="number" name="monthly_fee" id="monthly_fee" 
                                   value="{{ $company->monthly_fee ?? 0 }}" 
                                   step="0.01" min="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="next_payment_due" class="block text-sm font-medium text-gray-700">Next Payment Due</label>
                            <input type="date" name="next_payment_due" id="next_payment_due" 
                                   value="{{ $company->next_payment_due ? $company->next_payment_due->format('Y-m-d') : '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="mt-5">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Subscription
                        </button>
                    </div>
                </form>
            </div>
        </div>

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
        <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
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

        <!-- Danger Zone -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Danger Zone</h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Permanently delete this company and all associated data. This action cannot be undone.</p>
                </div>
                <div class="mt-5">
                    <button onclick="confirmDelete({{ $company->id }})" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Company
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Block Company Modal -->
    <div id="blockModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('super-admin.company.block', $company) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Block Company</h3>
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700">Block Reason *</label>
                            <textarea name="reason" id="reason" rows="3" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" 
                                      placeholder="e.g., Non-payment of subscription fees"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm sm:ml-3">
                            Block Company
                        </button>
                        <button type="button" onclick="closeBlockModal()" class="mt-3 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded text-sm sm:mt-0">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function blockCompany(companyId) {
            // Clear previous reason
            document.getElementById('reason').value = '';
            // Show the modal
            document.getElementById('blockModal').classList.remove('hidden');
        }

        function closeBlockModal() {
            // Hide the modal
            document.getElementById('blockModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('blockModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBlockModal();
            }
        });

        function confirmDelete(companyId) {
            if (confirm('Are you sure you want to delete this company? This action cannot be undone and will delete all associated data.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/super-admin/company/${companyId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

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
</body>
</html>
