<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->company) {
            return redirect()->route('login.show');
        }

        $company = $user->company;
        
        // Build query based on user role and filters
        $tasksQuery = $company->tasks()->with(['assignedBy', 'assignedTo', 'appointment']);

        // Filter based on user role
        if ($user->role !== 'admin') {
            $tasksQuery->where('assigned_to', $user->id);
        }

        // Apply filters
        if ($request->filled('status')) {
            $tasksQuery->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $tasksQuery->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to') && $user->role === 'admin') {
            $tasksQuery->where('assigned_to', $request->assigned_to);
        }

        $tasks = $tasksQuery->orderBy('due_date', 'asc')
                          ->orderBy('priority', 'desc')
                          ->paginate(20);

        // Get staff members for admin filter
        $staffMembers = $user->role === 'admin' ? $company->users : collect();

        // Get quick stats
        $stats = [
            'total' => $company->tasks()->count(),
            'pending' => $company->tasks()->pending()->count(),
            'overdue' => $company->tasks()->overdue()->count(),
            'due_today' => $company->tasks()->dueToday()->count(),
        ];

        return view('tasks.index', compact('tasks', 'staffMembers', 'stats'));
    }

    /**
     * Show the form for creating a new task
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->back()->withErrors(['permission' => 'Only admins can create tasks.']);
        }

        $company = $user->company;
        $staffMembers = $company->users()->where('role', '!=', 'admin')->get();
        
        // Get recent appointments for potential task assignment
        $recentAppointments = $company->appointments()
            ->with(['customer', 'service'])
            ->where('appointment_date', '>=', now()->subDays(7))
            ->where('appointment_date', '<=', now()->addDays(30))
            ->orderBy('appointment_date')
            ->limit(50)
            ->get();

        // Pre-fill if related to appointment
        $relatedAppointment = null;
        if ($request->filled('appointment_id')) {
            $relatedAppointment = $company->appointments()->find($request->appointment_id);
        }

        return view('tasks.create', compact('staffMembers', 'recentAppointments', 'relatedAppointment'));
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'admin') {
            return redirect()->back()->withErrors(['permission' => 'Only admins can create tasks.']);
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'task_type' => 'required|in:general,appointment_related,customer_follow_up,maintenance,admin',
            'due_date' => 'nullable|date|after_or_equal:today',
            'related_appointment_id' => 'nullable|exists:appointments,id',
        ]);

        // Verify assigned user belongs to same company
        $assignedUser = User::find($request->assigned_to);
        if ($assignedUser->company_id !== $user->company_id) {
            return redirect()->back()->withErrors(['assigned_to' => 'Can only assign tasks to your company staff.']);
        }

        $task = Task::create([
            'company_id' => $user->company_id,
            'assigned_by' => $user->id,
            'assigned_to' => $request->assigned_to,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'task_type' => $request->task_type,
            'due_date' => $request->due_date ? Carbon::parse($request->due_date) : null,
            'related_appointment_id' => $request->related_appointment_id,
            'status' => 'pending',
        ]);

        return redirect()->route('tasks.index')->with('status', 'Task created successfully!');
    }

    /**
     * Display the specified task
     */
    public function show(Task $task)
    {
        $user = Auth::user();
        
        if (!$user || $task->company_id !== $user->company_id) {
            return redirect()->back()->withErrors(['permission' => 'Task not found.']);
        }

        // Check permissions
        if ($user->role !== 'admin' && $task->assigned_to !== $user->id) {
            return redirect()->back()->withErrors(['permission' => 'You can only view tasks assigned to you.']);
        }

        $task->load(['assignedBy', 'assignedTo', 'appointment.customer', 'appointment.service']);

        return view('tasks.show', compact('task'));
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, Task $task)
    {
        $user = Auth::user();
        
        if (!$user || $task->company_id !== $user->company_id) {
            return response()->json(['success' => false, 'message' => 'Task not found.'], 404);
        }

        // Check permissions
        if ($user->role !== 'admin' && $task->assigned_to !== $user->id) {
            return response()->json(['success' => false, 'message' => 'You can only update tasks assigned to you.'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
            'notes' => $request->notes ?: $task->notes,
        ]);

        $message = "Task status updated to " . ucfirst(str_replace('_', ' ', $request->status)) . "!";

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->back()->with('status', $message);
    }

    /**
     * Get dashboard widget data
     */
    public function getDashboardData()
    {
        $user = Auth::user();
        
        if (!$user || !$user->company) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $company = $user->company;
        
        // Get tasks based on user role
        $tasksQuery = $company->tasks();
        if ($user->role !== 'admin') {
            $tasksQuery->where('assigned_to', $user->id);
        }

        $data = [
            'total_tasks' => $tasksQuery->count(),
            'pending_tasks' => $tasksQuery->where('status', 'pending')->count(),
            'overdue_tasks' => $tasksQuery->overdue()->count(),
            'due_today' => $tasksQuery->dueToday()->count(),
            'recent_tasks' => $tasksQuery->with(['assignedTo', 'assignedBy'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];

        return response()->json($data);
    }
}
