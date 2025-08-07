<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - BookingApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glass-effect { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        /* Ensure inputs are properly sized on mobile */
        @media (max-width: 640px) {
            .mobile-input {
                font-size: 16px; /* Prevents zoom on iOS */
            }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center py-4 sm:py-8 px-4">
    <div class="w-full max-w-md sm:max-w-lg">
        <!-- Back to Home Link -->
        <div class="text-center mb-4 sm:mb-6">
            <a href="/" class="text-white/80 hover:text-white transition-colors duration-300 text-sm font-medium">
                ← Back to Home
            </a>
        </div>

        <!-- Signup Card -->
        <div class="glass-effect rounded-2xl shadow-2xl p-6 sm:p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-600 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Create Account</h1>
                <p class="text-gray-600">Join us today and start booking with ease</p>
            </div>

            <!-- Success Message -->
            @if(session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-green-700">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800 mb-1">Please fix the following errors:</h3>
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Signup Form -->
            <form action="{{ route('signup') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           required 
                           autofocus
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 {{ $errors->has('name') ? 'border-red-500' : '' }}"
                           placeholder="Enter your full name">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 {{ $errors->has('email') ? 'border-red-500' : '' }}"
                           placeholder="Enter your email">
                </div>

                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Name
                    </label>
                    <input type="text" 
                           id="company_name" 
                           name="company_name" 
                           value="{{ old('company_name') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 {{ $errors->has('company_name') ? 'border-red-500' : '' }}"
                           placeholder="Enter your company name">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number <span class="text-gray-400">(Optional)</span>
                    </label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 {{ $errors->has('phone') ? 'border-red-500' : '' }}"
                           placeholder="Enter your phone number">
                </div>

                <div>
                    <label for="subscription_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Subscription Plan
                    </label>
                    <select id="subscription_type" 
                            name="subscription_type" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 {{ $errors->has('subscription_type') ? 'border-red-500' : '' }}">
                        <option value="">Select a plan</option>
                        <option value="starter" {{ old('subscription_type') == 'starter' ? 'selected' : '' }}>Starter - $10/month</option>
                        <option value="professional" {{ old('subscription_type') == 'professional' ? 'selected' : '' }}>Professional - $30/month</option>
                        <option value="enterprise" {{ old('subscription_type') == 'enterprise' ? 'selected' : '' }}>Enterprise - $50/month</option>
                        <option value="free" {{ old('subscription_type') == 'free' ? 'selected' : '' }}>Free Trial</option>
                    </select>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 {{ $errors->has('password') ? 'border-red-500' : '' }}"
                           placeholder="Create a secure password">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                           placeholder="Confirm your password">
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start">
                    <input type="checkbox" 
                           id="terms" 
                           name="terms" 
                           required
                           class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 mt-1">
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        I agree to the <a href="#" class="text-purple-600 hover:text-purple-500 font-medium">Terms of Service</a> 
                        and <a href="#" class="text-purple-600 hover:text-purple-500 font-medium">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" 
                        class="w-full bg-purple-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-[1.02]">
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-8 mb-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Already have an account?</span>
                    </div>
                </div>
            </div>

            <!-- Sign In Link -->
            <div class="text-center">
                <a href="{{ route('login.show') }}" 
                   class="w-full inline-block border-2 border-purple-600 text-purple-600 font-semibold py-3 px-4 rounded-lg hover:bg-purple-600 hover:text-white transition-all duration-300">
                    Sign In Instead
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-white/80 text-sm">© {{ date('Y') }} BookingApp. All rights reserved.</p>
        </div>
    </div>
</body>
</html>