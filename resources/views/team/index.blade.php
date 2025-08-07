@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Authorization Check -->
    @if(auth()->user()->role !== 'admin')
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-shield-alt text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-red-800 mb-2 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Access Denied
                </h3>
                <p class="text-red-700 mb-6">Only company administrators can manage team members and access this page.</p>
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    @else
        <!-- Mobile-First Header -->
        <div class="bg-white shadow-sm border-b lg:hidden">
            <div class="px-4 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-user-friends text-indigo-600 mr-2"></i>
                            Team
                        </h1>
                        <p class="text-sm text-gray-600 mt-1 flex items-center">
                            <i class="fas fa-users text-gray-400 mr-1"></i>
                            {{ $teamMembers->count() }} members
                        </p>
                    </div>
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 text-white p-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-home text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Desktop Header -->
        <div class="hidden lg:block bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-user-friends text-indigo-600 mr-3"></i>
                            Team Management
                        </h1>
                        <p class="text-gray-600 mt-2 flex items-center">
                            <i class="fas fa-building text-gray-400 mr-2"></i>
                            Manage your team members for {{ auth()->user()->company->company_name }}
                            <i class="fas fa-chart-bar text-gray-400 ml-4 mr-2"></i>
                            {{ $teamMembers->count() }} total members
                        </p>
                    </div>
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
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

            <!-- Team Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <!-- Total Members -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-4 lg:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-gray-600 text-lg lg:text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 lg:ml-5 w-0 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate flex items-center">
                                    <i class="fas fa-chart-line text-gray-400 mr-1"></i>
                                    Total Team Members
                                </dt>
                                <dd class="text-lg lg:text-2xl font-bold text-gray-900">{{ $teamMembers->count() }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Staff -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-4 lg:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-check text-green-600 text-lg lg:text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 lg:ml-5 w-0 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate flex items-center">
                                    <i class="fas fa-shield-check text-green-400 mr-1"></i>
                                    Active Staff
                                </dt>
                                <dd class="text-lg lg:text-2xl font-bold text-green-600">{{ $teamMembers->where('role', 'user')->count() }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-4 lg:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-blue-600 text-lg lg:text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 lg:ml-5 w-0 flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate flex items-center">
                                    <i class="fas fa-clock text-blue-400 mr-1"></i>
                                    Upcoming Appointments
                                </dt>
                                <dd class="text-lg lg:text-2xl font-bold text-blue-600">
                                    {{ $teamMembers->sum(function($member) { return $member->assignedAppointments->count(); }) }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($teamMembers->count() > 0)
                <!-- Desktop Team Members List -->
                <div class="hidden lg:block bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-list text-gray-400 mr-2"></i>
                            Team Members Directory
                        </h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-4">
                            @foreach($teamMembers as $member)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-user text-indigo-600"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                                        <i class="fas fa-id-card text-gray-400 mr-2"></i>
                                                        {{ $member->name }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600 flex items-center">
                                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                                        {{ $member->email }}
                                                    </p>
                                                    <div class="flex items-center space-x-4 mt-1">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full items-center
                                                            @if($member->role === 'admin') bg-purple-100 text-purple-800
                                                            @else bg-green-100 text-green-800 @endif">
                                                            @if($member->role === 'admin')
                                                                <i class="fas fa-crown mr-1"></i>
                                                                Admin
                                                            @else
                                                                <i class="fas fa-user-tie mr-1"></i>
                                                                Staff
                                                            @endif
                                                        </span>
                                                        <span class="text-xs text-gray-500 flex items-center">
                                                            <i class="fas fa-calendar-plus text-gray-400 mr-1"></i>
                                                            Joined {{ $member->created_at->format('M j, Y') }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-medium text-gray-900 flex items-center justify-end">
                                                        <i class="fas fa-calendar-check text-gray-400 mr-2"></i>
                                                        Upcoming Appointments
                                                    </p>
                                                    <p class="text-lg font-bold text-indigo-600">
                                                        {{ $member->assignedAppointments->count() }}
                                                    </p>
                                                    @if($member->assignedAppointments->count() > 0)
                                                        <p class="text-xs text-gray-500 flex items-center justify-end">
                                                            <i class="fas fa-arrow-right text-gray-400 mr-1"></i>
                                                            Next: {{ $member->assignedAppointments->first()->appointment_date->format('M j') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="ml-6 flex flex-col space-y-2">
                                            @if($member->assignedAppointments->count() > 0)
                                                <span class="text-xs text-orange-600 font-medium text-center flex items-center">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Has upcoming appointments
                                                </span>
                                                <button type="button" 
                                                        onclick="showAppointmentWarning('{{ $member->name }}', {{ $member->assignedAppointments->count() }})"
                                                        class="bg-gray-400 text-white px-3 py-1 rounded text-sm font-medium cursor-not-allowed flex items-center justify-center">
                                                    <i class="fas fa-lock mr-1"></i>
                                                    Cannot Delete
                                                </button>
                                            @else
                                                <form action="{{ route('team.destroy', $member) }}" method="POST" 
                                                      onsubmit="return confirmDelete('{{ $member->name }}')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium flex items-center justify-center transition-colors">
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Delete Member
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Mobile Team Members List -->
                <div class="lg:hidden space-y-4">
                    @foreach($teamMembers as $member)
                        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                            <!-- Mobile Member Header -->
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-indigo-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-900">{{ $member->name }}</h3>
                                            <p class="text-xs text-gray-500 flex items-center">
                                                <i class="fas fa-calendar-check mr-1"></i>
                                                {{ $member->assignedAppointments->count() }} appointments
                                            </p>
                                        </div>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full items-center
                                        @if($member->role === 'admin') bg-purple-100 text-purple-800
                                        @else bg-green-100 text-green-800 @endif">
                                        @if($member->role === 'admin')
                                            <i class="fas fa-crown mr-1"></i>
                                            Admin
                                        @else
                                            <i class="fas fa-user-tie mr-1"></i>
                                            Staff
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Mobile Member Content -->
                            <div class="px-4 py-3">
                                <div class="space-y-3">
                                    <!-- Contact Info -->
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-envelope text-gray-400 mr-2 w-4"></i>
                                        {{ $member->email }}
                                    </div>

                                    <!-- Join Date -->
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-calendar-plus text-gray-400 mr-2 w-4"></i>
                                        Joined {{ $member->created_at->format('M j, Y') }}
                                    </div>

                                    @if($member->assignedAppointments->count() > 0)
                                        <div class="bg-blue-50 p-3 rounded-lg">
                                            <div class="flex items-center text-sm text-blue-700">
                                                <i class="fas fa-clock text-blue-500 mr-2"></i>
                                                <span class="font-medium">Next appointment:</span>
                                                <span class="ml-1">{{ $member->assignedAppointments->first()->appointment_date->format('M j, Y') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <div class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                                No upcoming appointments
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Mobile Action Buttons -->
                            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                                @if($member->assignedAppointments->count() > 0)
                                    <div class="text-center">
                                        <div class="text-xs text-orange-600 font-medium mb-2 flex items-center justify-center">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Cannot delete - has upcoming appointments
                                        </div>
                                        <button type="button" 
                                                onclick="showAppointmentWarning('{{ $member->name }}', {{ $member->assignedAppointments->count() }})"
                                                class="w-full bg-gray-400 text-white px-3 py-2 rounded text-sm font-medium cursor-not-allowed flex items-center justify-center">
                                            <i class="fas fa-lock mr-1"></i>
                                            Cannot Delete
                                        </button>
                                    </div>
                                @else
                                    <form action="{{ route('team.destroy', $member) }}" method="POST" 
                                          onsubmit="return confirmDelete('{{ $member->name }}')" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm font-medium flex items-center justify-center transition-colors">
                                            <i class="fas fa-trash mr-1"></i>
                                            Delete Member
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user-friends text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center justify-center">
                        <i class="fas fa-info-circle text-gray-400 mr-2"></i>
                        No team members yet
                    </h3>
                    <p class="text-gray-500 mb-6 max-w-sm mx-auto">Start building your team by adding staff members from the dashboard invite section.</p>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Go to Dashboard
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>

<script>
    function confirmDelete(memberName) {
        return confirm(`Are you sure you want to delete ${memberName} from your team? This action cannot be undone.`);
    }

    function showAppointmentWarning(memberName, appointmentCount) {
        alert(`Cannot delete ${memberName} because they have ${appointmentCount} upcoming appointment(s). Please reassign or cancel these appointments first.`);
    }
</script>
@endsection
