<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BookingApp')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
        }
        .gradient-bg { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        }
        /* Mobile menu styles */
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .mobile-menu.open {
            transform: translateX(0);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <!-- Logo and Company Name -->
                <div class="flex items-center flex-shrink-0 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-600 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-xs sm:text-sm"></i>
                    </div>
                    <a href="{{ route('dashboard') }}" class="ml-2 text-lg sm:text-xl font-bold text-gray-900 hover:text-gray-700 truncate">BookingApp</a>
                    @if(auth()->user() && auth()->user()->company)
                        <span class="hidden sm:block ml-2 md:ml-4 text-xs sm:text-sm text-gray-500 truncate">{{ auth()->user()->company->company_name }}</span>
                    @endif
                </div>
                
                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-2 lg:space-x-4 xl:space-x-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center text-gray-700 hover:text-purple-600 px-2 lg:px-3 py-2 rounded-md text-xs lg:text-sm font-medium transition-colors">
                        <i class="fas fa-tachometer-alt mr-1 lg:mr-2 text-xs lg:text-sm"></i>
                        <span class="hidden lg:block">Dashboard</span>
                        <span class="lg:hidden">Home</span>
                    </a>
                    
                    @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'user'))
                        <a href="{{ route('appointments.index') }}" class="flex items-center text-gray-700 hover:text-purple-600 px-2 lg:px-3 py-2 rounded-md text-xs lg:text-sm font-medium transition-colors">
                            <i class="fas fa-calendar-check mr-1 lg:mr-2 text-xs lg:text-sm"></i>
                            <span class="hidden lg:block">Appointments</span>
                            <span class="lg:hidden">Appts</span>
                        </a>
                        <a href="{{ route('customers.index') }}" class="flex items-center text-gray-700 hover:text-purple-600 px-2 lg:px-3 py-2 rounded-md text-xs lg:text-sm font-medium transition-colors">
                            <i class="fas fa-users mr-1 lg:mr-2 text-xs lg:text-sm"></i>
                            <span class="hidden lg:block">Customers</span>
                            <span class="lg:hidden">Clients</span>
                        </a>
                    @endif
                    
                    @if(auth()->user() && auth()->user()->role === 'admin')
                        <a href="{{ route('services.index') }}" class="flex items-center text-gray-700 hover:text-purple-600 px-2 lg:px-3 py-2 rounded-md text-xs lg:text-sm font-medium transition-colors">
                            <i class="fas fa-cogs mr-1 lg:mr-2 text-xs lg:text-sm"></i>
                            <span class="hidden lg:block">Services</span>
                            <span class="lg:hidden">Services</span>
                        </a>
                        <a href="{{ route('tasks.index') }}" class="flex items-center text-gray-700 hover:text-purple-600 px-2 lg:px-3 py-2 rounded-md text-xs lg:text-sm font-medium transition-colors">
                            <i class="fas fa-tasks mr-1 lg:mr-2 text-xs lg:text-sm"></i>
                            <span class="hidden lg:block">Tasks</span>
                            <span class="lg:hidden">Tasks</span>
                        </a>
                        <a href="{{ route('team.index') }}" class="flex items-center text-gray-700 hover:text-purple-600 px-2 lg:px-3 py-2 rounded-md text-xs lg:text-sm font-medium transition-colors">
                            <i class="fas fa-user-friends mr-1 lg:mr-2 text-xs lg:text-sm"></i>
                            <span class="hidden lg:block">Team</span>
                            <span class="lg:hidden">Team</span>
                        </a>
                    @endif
                    
                    @if(auth()->user() && auth()->user()->role !== 'admin')
                        <a href="{{ route('tasks.index') }}" class="flex items-center text-gray-700 hover:text-purple-600 px-2 lg:px-3 py-2 rounded-md text-xs lg:text-sm font-medium transition-colors">
                            <i class="fas fa-tasks mr-1 lg:mr-2 text-xs lg:text-sm"></i>
                            <span class="hidden lg:block">My Tasks</span>
                            <span class="lg:hidden">Tasks</span>
                        </a>
                    @endif
                </div>
                
                <!-- User Menu & Mobile Menu Button -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Notifications (responsive visibility) -->
                    @auth
                        <div class="hidden sm:block relative">
                            <button type="button" id="notificationButton" class="p-1.5 sm:p-2 rounded-full text-gray-400 hover:text-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                <i class="fas fa-bell text-sm sm:text-lg"></i>
                                <span id="notificationBadge" class="absolute -top-0.5 sm:-top-1 -right-0.5 sm:-right-1 h-4 w-4 sm:h-5 sm:w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden">0</span>
                            </button>
                        </div>
                    @endauth
                    
                    <!-- User Info (progressive disclosure) -->
                    @auth
                        <div class="hidden lg:flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-user-circle text-gray-400 text-lg"></i>
                                <span class="text-gray-700 text-sm font-medium">{{ auth()->user()->name }}</span>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full font-medium
                                @if(auth()->user()->role === 'admin') 
                                    bg-purple-100 text-purple-800
                                @elseif(auth()->user()->role === 'super_admin') 
                                    bg-red-100 text-red-800
                                @elseif(auth()->user()->role === 'employee') 
                                    bg-blue-100 text-blue-800
                                @else 
                                    bg-gray-100 text-gray-800 
                                @endif">
                                <i class="fas fa-crown mr-1" style="font-size: 10px;"></i>
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </span>
                        </div>
                        
                        <!-- Medium screen user info -->
                        <div class="hidden md:flex lg:hidden items-center">
                            <div class="relative">
                                <button type="button" onclick="toggleUserMenu()" class="flex items-center space-x-1 text-gray-700 hover:text-purple-600 p-2 rounded-md transition-colors">
                                    <i class="fas fa-user-circle text-lg"></i>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <div class="px-4 py-2 text-sm text-gray-500 border-b">
                                        {{ auth()->user()->name }}
                                        <span class="block text-xs">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>
                                    </div>
                                    <form action="{{ route('logout') }}" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Large screen logout -->
                        <div class="hidden lg:block">
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="flex items-center text-gray-700 hover:text-red-600 text-sm font-medium transition-colors">
                                    <i class="fas fa-sign-out-alt mr-1"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login.show') }}" class="flex items-center text-gray-700 hover:text-purple-600 text-xs sm:text-sm font-medium transition-colors">
                            <i class="fas fa-sign-in-alt mr-1 sm:mr-2"></i>
                            <span class="hidden sm:block">Login</span>
                        </a>
                    @endauth
                    
                    <!-- Mobile menu button (responsive sizing) -->
                    <div class="md:hidden">
                        <button type="button" onclick="toggleMobileMenu()" class="inline-flex items-center justify-center p-1.5 sm:p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 transition-colors">
                            <i class="fas fa-bars text-base sm:text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu (responsive for all small screens) -->
        <div id="mobileMenu" class="md:hidden mobile-menu fixed inset-y-0 left-0 z-50 w-72 sm:w-80 bg-white shadow-xl">
            <div class="p-4 sm:p-6 h-full flex flex-col">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <div class="flex items-center">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-600 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white text-xs sm:text-sm"></i>
                        </div>
                        <span class="ml-2 text-base sm:text-lg font-bold text-gray-900">BookingApp</span>
                    </div>
                    <button type="button" id="closeMobileMenu" class="p-1.5 sm:p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times text-base sm:text-lg"></i>
                    </button>
                </div>
                
                @auth
                    <!-- User info in mobile (responsive) -->
                    <div class="border-b pb-4 sm:pb-6 mb-4 sm:mb-6">
                        <div class="flex items-center space-x-2 sm:space-x-3 mb-3">
                            <i class="fas fa-user-circle text-purple-500 text-xl sm:text-2xl"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->company->company_name ?? '' }}</p>
                            </div>
                        </div>
                        <span class="inline-flex text-xs px-2 sm:px-3 py-1 rounded-full font-medium
                            @if(auth()->user()->role === 'admin') 
                                bg-purple-100 text-purple-800
                            @elseif(auth()->user()->role === 'super_admin') 
                                bg-red-100 text-red-800
                            @elseif(auth()->user()->role === 'employee') 
                                bg-blue-100 text-blue-800
                            @else 
                                bg-gray-100 text-gray-800 
                            @endif">
                            <i class="fas fa-crown mr-1" style="font-size: 8px;"></i>
                            {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                        </span>
                    </div>
                    
                    <!-- Navigation links in mobile (responsive spacing) -->
                    <nav class="space-y-2 sm:space-y-3 flex-1 overflow-y-auto">
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                            <i class="fas fa-tachometer-alt mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                            Dashboard
                        </a>
                        
                        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'user'))
                            <a href="{{ route('appointments.index') }}" class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                                <i class="fas fa-calendar-check mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                                Appointments
                            </a>
                            <a href="{{ route('customers.index') }}" class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                                <i class="fas fa-users mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                                Customers
                            </a>
                        @endif
                        
                        @if(auth()->user() && auth()->user()->role === 'admin')
                            <a href="{{ route('services.index') }}" class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                                <i class="fas fa-cogs mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                                Services
                            </a>
                            <a href="{{ route('tasks.index') }}" class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                                <i class="fas fa-tasks mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                                Tasks
                            </a>
                            <a href="{{ route('team.index') }}" class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                                <i class="fas fa-user-friends mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                                Team
                            </a>
                        @endif
                        
                        @if(auth()->user() && auth()->user()->role !== 'admin')
                            <a href="{{ route('tasks.index') }}" class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                                <i class="fas fa-tasks mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                                My Tasks
                            </a>
                        @endif
                        
                        <!-- Mobile notifications (responsive) -->
                        <button type="button" onclick="handleMobileNotifications()" class="w-full flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                            <i class="fas fa-bell mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                            Notifications
                            <span id="mobileNotificationBadge" class="ml-auto h-4 w-4 sm:h-5 sm:w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden">0</span>
                        </button>
                    </nav>
                    
                    <!-- Logout in mobile (sticky bottom) -->
                    <div class="mt-auto pt-4 sm:pt-6 border-t">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fas fa-sign-out-alt mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <div class="space-y-2 sm:space-y-3">
                        <a href="{{ route('login.show') }}" class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                            <i class="fas fa-sign-in-alt mr-2 sm:mr-3 text-gray-400 w-4 sm:w-5 text-center"></i>
                            Login
                        </a>
                    </div>
                @endauth
            </div>
        </div>
        
        <!-- Mobile menu overlay -->
        <div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer (responsive) -->
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto py-3 sm:py-4 px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                <div class="text-xs sm:text-sm text-gray-500 text-center sm:text-left">
                    © {{ date('Y') }} BookingApp. All rights reserved.
                </div>
                <div class="flex space-x-3 sm:space-x-4 text-xs sm:text-sm text-gray-500">
                    <span class="flex items-center">
                        <i class="fas fa-code mr-1 text-xs"></i>
                        Version 1.0
                    </span>
                    <span class="hidden sm:flex items-center">
                        <i class="fas fa-heart text-red-400 mr-1 text-xs"></i>
                        Made with Laravel
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle function
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('mobileMenuOverlay');
            const body = document.body;
            
            if (mobileMenu.style.transform === 'translateX(0px)' || mobileMenu.style.transform === '') {
                // Hide menu
                mobileMenu.style.transform = 'translateX(-100%)';
                overlay.classList.add('hidden');
                body.style.overflow = '';
            } else {
                // Show menu
                mobileMenu.style.transform = 'translateX(0px)';
                overlay.classList.remove('hidden');
                body.style.overflow = 'hidden';
            }
        }

        // Close mobile menu
        function closeMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('mobileMenuOverlay');
            const body = document.body;
            
            mobileMenu.style.transform = 'translateX(-100%)';
            overlay.classList.add('hidden');
            body.style.overflow = '';
        }

        // User menu toggle function  
        function toggleUserMenu() {
            const userMenu = document.getElementById('userMenu');
            userMenu.classList.toggle('hidden');
        }

        // Initialize mobile menu
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('mobileMenuOverlay');
            const closeMobileMenuBtn = document.getElementById('closeMobileMenu');
            
            // Initialize menu position
            if (mobileMenu) {
                mobileMenu.style.transform = 'translateX(-100%)';
                mobileMenu.style.transition = 'transform 0.3s ease-in-out';
            }
            
            // Close mobile menu button
            if (closeMobileMenuBtn) {
                closeMobileMenuBtn.addEventListener('click', closeMobileMenu);
            }
            
            // Close menu when clicking overlay
            if (overlay) {
                overlay.addEventListener('click', closeMobileMenu);
            }
            
            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                }
            });
        });

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = document.querySelector('[onclick="toggleUserMenu()"]');
            
            if (userMenu && !userMenu.contains(event.target) && userButton && !userButton.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>

    <!-- SweetAlert2 for better notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>
