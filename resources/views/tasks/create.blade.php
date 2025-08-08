@extends('layouts.app')

@section('title', 'Create Task')

@push('styles')
<style>
    .priority-preview {
        border-left: 4px solid;
        padding-left: 12px;
    }
    .priority-preview.high { border-color: #ef4444; }
    .priority-preview.medium { border-color: #f59e0b; }
    .priority-preview.low { border-color: #10b981; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Task</h1>
                    <p class="text-gray-600 mt-1">Assign a task to your team members</p>
                </div>
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Tasks
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('tasks.store') }}" class="space-y-6 p-6">
                @csrf

                <!-- Task Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Task Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('title') border-red-300 @enderror"
                           placeholder="Enter a clear, descriptive task title"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Task Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Task Description
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                              placeholder="Provide detailed instructions or context for this task">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Row 1: Assigned To and Priority -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                            Assign To <span class="text-red-500">*</span>
                        </label>
                        <select name="assigned_to" 
                                id="assigned_to" 
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('assigned_to') border-red-300 @enderror"
                                required>
                            <option value="">Select a team member</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ old('assigned_to') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ $staff->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" 
                                id="priority" 
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('priority') border-red-300 @enderror"
                                required
                                onchange="updatePriorityPreview()">
                            <option value="">Select priority level</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low Priority</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium Priority</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High Priority</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div id="priority-preview" class="mt-2 p-2 bg-gray-50 rounded text-sm text-gray-600 hidden">
                            <div id="priority-indicator" class="priority-preview"></div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Task Type and Due Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="task_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Task Type
                        </label>
                        <select name="task_type" 
                                id="task_type" 
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('task_type') border-red-300 @enderror">
                            <option value="general" {{ old('task_type') === 'general' ? 'selected' : '' }}>General Task</option>
                            <option value="appointment_related" {{ old('task_type') === 'appointment_related' ? 'selected' : '' }}>Appointment Related</option>
                            <option value="customer_follow_up" {{ old('task_type') === 'customer_follow_up' ? 'selected' : '' }}>Customer Follow-up</option>
                            <option value="maintenance" {{ old('task_type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="admin" {{ old('task_type') === 'admin' ? 'selected' : '' }}>Administrative</option>
                        </select>
                        @error('task_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Due Date
                        </label>
                        <input type="date" 
                               name="due_date" 
                               id="due_date" 
                               value="{{ old('due_date') }}"
                               min="{{ date('Y-m-d') }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('due_date') border-red-300 @enderror">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Leave empty if no specific due date</p>
                    </div>
                </div>

                <!-- Appointment Association -->
                <div id="appointment-section" class="hidden">
                    <label for="related_appointment_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Related Appointment (Optional)
                    </label>
                    <select name="related_appointment_id" 
                            id="related_appointment_id" 
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('related_appointment_id') border-red-300 @enderror">
                        <option value="">No related appointment</option>
                        @foreach($recentAppointments as $appointment)
                            <option value="{{ $appointment->id }}" {{ old('related_appointment_id') == $appointment->id ? 'selected' : '' }}>
                                {{ $appointment->customer->name }} - {{ $appointment->service->name }} 
                                ({{ $appointment->appointment_date->format('M j, Y g:i A') }})
                            </option>
                        @endforeach
                    </select>
                    @error('related_appointment_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Task Preview -->
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-gray-300">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Task Preview:</h4>
                    <div id="task-preview" class="text-sm text-gray-600">
                        Complete the form above to see a preview of your task
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('tasks.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updatePriorityPreview() {
    const priority = document.getElementById('priority').value;
    const preview = document.getElementById('priority-preview');
    const indicator = document.getElementById('priority-indicator');
    
    if (priority) {
        preview.classList.remove('hidden');
        indicator.className = `priority-preview ${priority}`;
        
        const descriptions = {
            'high': 'High priority tasks require immediate attention and should be completed as soon as possible.',
            'medium': 'Medium priority tasks are important but can be scheduled around high priority items.',
            'low': 'Low priority tasks can be completed when time allows and other priorities are handled.'
        };
        
        indicator.textContent = descriptions[priority];
    } else {
        preview.classList.add('hidden');
    }
    
    updateTaskPreview();
}

function updateTaskPreview() {
    const title = document.getElementById('title').value;
    const assignedTo = document.getElementById('assigned_to');
    const priority = document.getElementById('priority').value;
    const taskType = document.getElementById('task_type').value;
    const dueDate = document.getElementById('due_date').value;
    const preview = document.getElementById('task-preview');
    
    if (!title) {
        preview.textContent = 'Complete the form above to see a preview of your task';
        return;
    }
    
    let previewText = `"${title}"`;
    
    if (assignedTo.value) {
        const selectedOption = assignedTo.options[assignedTo.selectedIndex];
        previewText += ` will be assigned to ${selectedOption.text.split(' (')[0]}`;
    }
    
    if (priority) {
        previewText += ` with ${priority} priority`;
    }
    
    if (dueDate) {
        const date = new Date(dueDate);
        previewText += ` and is due on ${date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        })}`;
    }
    
    if (taskType && taskType !== 'general') {
        previewText += `. This is a ${taskType.replace('_', ' ')} task`;
    }
    
    previewText += '.';
    
    preview.textContent = previewText;
}

function toggleAppointmentSection() {
    const taskType = document.getElementById('task_type').value;
    const appointmentSection = document.getElementById('appointment-section');
    
    if (taskType === 'appointment_related') {
        appointmentSection.classList.remove('hidden');
    } else {
        appointmentSection.classList.add('hidden');
        document.getElementById('related_appointment_id').value = '';
    }
}

// Event listeners
document.getElementById('title').addEventListener('input', updateTaskPreview);
document.getElementById('assigned_to').addEventListener('change', updateTaskPreview);
document.getElementById('priority').addEventListener('change', updatePriorityPreview);
document.getElementById('task_type').addEventListener('change', function() {
    toggleAppointmentSection();
    updateTaskPreview();
});
document.getElementById('due_date').addEventListener('change', updateTaskPreview);

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updatePriorityPreview();
    toggleAppointmentSection();
});
</script>
@endpush
@endsection
