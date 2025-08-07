<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Company - SaaS Owner Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-bold">SaaS Owner Dashboard</h1>
                    <span class="text-blue-200">Create New Company</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ auth()->user()->name }}</span>
                    <a href="{{ route('super-admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 rounded transition duration-200">
                        ← Back to Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-700 px-4 py-2 rounded transition duration-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Create New Company Account</h2>
                    <p class="text-gray-600">Set up a new company with admin user and subscription details</p>
                </div>

                <!-- Success/Error Messages -->
                @if(session('status'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Company Creation Form -->
                <div class="bg-white rounded-lg shadow-md">
                    <form method="POST" action="{{ route('super-admin.company.store') }}" class="p-6">
                        @csrf
                        
                        <!-- Company Information Section -->
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Company Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
                                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           required>
                                </div>

                                <div>
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                                    <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Company Contact Email</label>
                                    <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <textarea id="address" name="address" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Admin User Section -->
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Admin User Account</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">Admin Name *</label>
                                    <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           required>
                                </div>

                                <div>
                                    <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">Admin Email *</label>
                                    <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           required>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">Admin Password *</label>
                                    <input type="password" id="admin_password" name="admin_password" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           required minlength="6">
                                    <p class="text-sm text-gray-500 mt-1">Minimum 6 characters</p>
                                </div>
                            </div>
                        </div>

                        <!-- Subscription Details Section -->
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Subscription Details</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="subscription_status" class="block text-sm font-medium text-gray-700 mb-2">Subscription Status *</label>
                                    <select id="subscription_status" name="subscription_status" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                            required>
                                        <option value="pending" {{ old('subscription_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="active" {{ old('subscription_status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="blocked" {{ old('subscription_status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                        <option value="expired" {{ old('subscription_status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="monthly_fee" class="block text-sm font-medium text-gray-700 mb-2">Monthly Fee ($) *</label>
                                    <input type="number" id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', '29.99') }}" 
                                           step="0.01" min="0" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           required>
                                </div>

                                <div>
                                    <label for="subscription_start_date" class="block text-sm font-medium text-gray-700 mb-2">Subscription Start Date</label>
                                    <input type="date" id="subscription_start_date" name="subscription_start_date" 
                                           value="{{ old('subscription_start_date', date('Y-m-d')) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="next_payment_due" class="block text-sm font-medium text-gray-700 mb-2">Next Payment Due</label>
                                    <input type="date" id="next_payment_due" name="next_payment_due" 
                                           value="{{ old('next_payment_due', date('Y-m-d', strtotime('+1 month'))) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t">
                            <a href="{{ route('super-admin.dashboard') }}" 
                               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                                Create Company Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-fill contact email from admin email if not provided
        document.getElementById('admin_email').addEventListener('blur', function() {
            const contactEmail = document.getElementById('contact_email');
            if (!contactEmail.value && this.value) {
                contactEmail.value = this.value;
            }
        });

        // Update next payment due when subscription start date changes
        document.getElementById('subscription_start_date').addEventListener('change', function() {
            const nextPaymentDue = document.getElementById('next_payment_due');
            if (this.value) {
                const startDate = new Date(this.value);
                startDate.setMonth(startDate.getMonth() + 1);
                nextPaymentDue.value = startDate.toISOString().split('T')[0];
            }
        });
    </script>
</body>
</html>
