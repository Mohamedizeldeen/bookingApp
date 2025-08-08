@extends('layouts.app')

@section('title', 'Task Details')

@push('styles')
<style>
    .priority-indicator {
        width: 6px;
        border-radius: 3px;
    }
    .priority-indicator.high { background-color: #ef4444; }
    .priority-indicator.medium { background-color: #f59e0b; }
    .priority-indicator.low { background-color: #10b981; }
    
    .status-timeline {
        position: relative;
    }
    .status-timeline::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 20px;
        bottom: 0;
        width: 2px;
        background-color: #e5e7eb;
    }
    .timeline-item {
        position: relative;
        padding-left: 40px;
        margin-bottom: 16px;
    }
    .timeline-dot {
        position: absolute;
        left: 8px;
        top: 4px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #6b7280;
    }
    .timeline-dot.active {
        background-color: #10b981;
    }
    .timeline-dot.current {
        background-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('tasks.index') }}" class="text-gray-400 hover:text-gray-500">Tasks</a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-4 text-sm font-medium text-gray-500">Task Details</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $task->title }}</h1>
                </div>
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Tasks
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Task Overview -->
                <div class="bg-white shadow rounded-lg">
                    <div class="flex">
                        <div class="priority-indicator {{ $task->priority }}"></div>
                        <div class="flex-1 p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $task->getPriorityBadgeClass() }}">
                                        {{ ucfirst($task->priority) }} Priority
                                    </span>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $task->getStatusBadgeClass() }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                    @if($task->isOverdue())
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                        Overdue
                                    </span>
                                    @endif
                                </div>
                                
                                @if($task->status !== 'completed' && $task->status !== 'cancelled')
                                    @if(auth()->user()->role === 'admin' || $task->assigned_to === auth()->user()->id)
                                    <div class="flex space-x-2">
                                        @if($task->status === 'pending')
                                        <button onclick="updateTaskStatus('in_progress')" 
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                            Start Task
                                        </button>
                                        @endif
                                        
                                        @if($task->status === 'in_progress')
                                        <button onclick="updateTaskStatus('completed')" 
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                            Mark Complete
                                        </button>
                                        @endif
                                        
                                        <button onclick="updateTaskStatus('cancelled')" 
                                                class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            Cancel Task
                                        </button>
                                    </div>
                                    @endif
                                @endif
                            </div>
                            
                            @if($task->description)
                            <div class="prose max-w-none">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                                <p class="text-gray-700 leading-relaxed">{{ $task->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Related Appointment -->
                @if($task->appointment)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Related Appointment</h3>
                    <div class="border rounded-lg p-4 bg-blue-50 border-blue-200">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $task->appointment->service->name }}</h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    Customer: {{ $task->appointment->customer->name }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Date: {{ $task->appointment->appointment_date->format('l, F j, Y \a\t g:i A') }}
                                </p>
                                @if($task->appointment->status)
                                <p class="text-sm text-gray-600">
                                    Status: <span class="font-medium">{{ ucfirst($task->appointment->status) }}</span>
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Comments Section (Future Enhancement) -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Activity & Comments</h3>
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-2.82-.46l-5.1 2.55A1 1 0 014 21V8a8 8 0 018-8c4.418 0 8 3.582 8 8z"></path>
                        </svg>
                        <p class="mt-2">No comments yet</p>
                        <p class="text-sm">Comments and activity tracking coming soon</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Task Information -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Task Information</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                            <dd class="mt-1 flex items-center">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ substr($task->assignedTo->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $task->assignedTo->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $task->assignedTo->email }}</p>
                                </div>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Assigned By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $task->assignedBy->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Task Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $task->created_at->format('M j, Y \a\t g:i A') }}</dd>
                        </div>

                        @if($task->due_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                            <dd class="mt-1 text-sm {{ $task->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                {{ $task->due_date->format('l, F j, Y') }}
                                @if($task->isOverdue())
                                    <span class="block text-xs">({{ $task->due_date->diffForHumans() }})</span>
                                @else
                                    <span class="block text-xs text-gray-500">({{ $task->due_date->diffForHumans() }})</span>
                                @endif
                            </dd>
                        </div>
                        @endif

                        @if($task->completed_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Completed</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $task->completed_at->format('M j, Y \a\t g:i A') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Status Timeline -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Timeline</h3>
                    <div class="status-timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $task->status === 'pending' ? 'current' : 'active' }}"></div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">Task Created</p>
                                <p class="text-gray-500">{{ $task->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>

                        @if($task->status !== 'pending')
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $task->status === 'in_progress' ? 'current' : 'active' }}"></div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">In Progress</p>
                                <p class="text-gray-500">{{ $task->updated_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if(in_array($task->status, ['completed', 'cancelled']))
                        <div class="timeline-item">
                            <div class="timeline-dot active"></div>
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">{{ ucfirst($task->status) }}</p>
                                <p class="text-gray-500">
                                    @if($task->completed_at)
                                        {{ $task->completed_at->format('M j, Y g:i A') }}
                                    @else
                                        {{ $task->updated_at->format('M j, Y g:i A') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateTaskStatus(status) {
    const actions = {
        'in_progress': 'start',
        'completed': 'complete',
        'cancelled': 'cancel'
    };
    
    if (!confirm(`Are you sure you want to ${actions[status]} this task?`)) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/tasks/{{ $task->id }}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating task status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating task status');
    });
}
</script>
@endpush
@endsection
