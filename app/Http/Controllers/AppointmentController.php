<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display appointments for staff dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login.show');
        }

        // Get appointments based on user role
        $appointmentsQuery = $user->company->appointments()
            ->with(['customer', 'service', 'assignedUser']);

        // If user is not admin, only show appointments assigned to them
        if ($user->role !== 'admin') {
            $appointmentsQuery->where('assigned_user_id', $user->id);
        }

        $appointments = $appointmentsQuery
            ->orderBy('appointment_date', 'asc')
            ->paginate(20);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show today's appointments for staff.
     */
    public function today()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login.show');
        }

        $todayAppointmentsQuery = $user->company->appointments()
            ->with(['customer', 'service', 'assignedUser'])
            ->whereDate('appointment_date', today());

        // If user is not admin, only show appointments assigned to them
        if ($user->role !== 'admin') {
            $todayAppointmentsQuery->where('assigned_user_id', $user->id);
        }

        $todayAppointments = $todayAppointmentsQuery
            ->orderBy('appointment_date', 'asc')
            ->get();

        return view('appointments.today', compact('todayAppointments'));
    }

    /**
     * Show appointments assigned to the current staff member.
     */
    public function myAppointments()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login.show');
        }

        // Get appointments assigned to the current user
        $myAppointments = $user->company->appointments()
            ->with(['customer', 'service', 'assignedUser'])
            ->where('assigned_user_id', $user->id)
            ->orderBy('appointment_date', 'asc')
            ->paginate(20);

        return view('appointments.my', compact('myAppointments'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login.show');
        }

        $customers = $user->company->customers()->get();
        $services = $user->company->services()->get();
        $staff = $user->company->users()->get();

        return view('appointments.create', compact('customers', 'services', 'staff'));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login.show');
        }

        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify customer and service belong to the same company
        $customer = Customer::find($validatedData['customer_id']);
        $service = Service::find($validatedData['service_id']);
        
        if ($customer->company_id !== $user->company_id || $service->company_id !== $user->company_id) {
            return redirect()->back()->withErrors(['permission' => 'Invalid customer or service selection.']);
        }

        $appointment = Appointment::create([
            'company_id' => $user->company_id,
            'customer_id' => $validatedData['customer_id'],
            'service_id' => $validatedData['service_id'],
            'assigned_user_id' => $validatedData['assigned_user_id'] ?? $user->id,
            'created_by' => $user->id,
            'appointment_date' => $validatedData['appointment_date'],
            'end_time' => Carbon::parse($validatedData['appointment_date'])->addMinutes($service->duration),
            'price' => $service->price,
            'notes' => $validatedData['notes'],
            'status' => 'scheduled',
        ]);

        // Create notification for the appointment
        NotificationService::appointmentCreated($appointment);

        return redirect()->route('appointments.index')->with('status', 'Appointment created successfully!');
    }

    /**
     * Confirm an appointment.
     */
    public function confirm(Appointment $appointment)
    {
        $user = Auth::user();
        
        if (!$user || $appointment->company_id !== $user->company_id) {
            return redirect()->back()->withErrors(['permission' => 'You can only confirm appointments from your company.']);
        }

        // Staff can only confirm appointments assigned to them, admins can confirm any
        if ($user->role !== 'admin' && $appointment->assigned_user_id !== $user->id) {
            return redirect()->back()->withErrors(['permission' => 'You can only confirm appointments assigned to you.']);
        }

        $appointment->update(['status' => 'confirmed']);

        // Create confirmation notification
        NotificationService::appointmentConfirmed($appointment);

        return redirect()->back()->with('status', 'Appointment confirmed successfully!');
    }

    /**
     * Complete an appointment.
     */
    public function complete(Appointment $appointment)
    {
        $user = Auth::user();
        
        if (!$user || $appointment->company_id !== $user->company_id) {
            return redirect()->back()->withErrors(['permission' => 'You can only complete appointments from your company.']);
        }

        // Staff can only complete appointments assigned to them, admins can complete any
        if ($user->role !== 'admin' && $appointment->assigned_user_id !== $user->id) {
            return redirect()->back()->withErrors(['permission' => 'You can only complete appointments assigned to you.']);
        }

        $appointment->update(['status' => 'completed']);

        // Create completion notification
        NotificationService::appointmentCompleted($appointment);

        return redirect()->back()->with('status', 'Appointment marked as completed!');
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Appointment $appointment)
    {
        $user = Auth::user();
        
        if (!$user || $appointment->company_id !== $user->company_id) {
            return redirect()->back()->withErrors(['permission' => 'You can only cancel appointments from your company.']);
        }

        // Staff can only cancel appointments assigned to them, admins can cancel any
        if ($user->role !== 'admin' && $appointment->assigned_user_id !== $user->id) {
            return redirect()->back()->withErrors(['permission' => 'You can only cancel appointments assigned to you.']);
        }

        $appointment->update(['status' => 'cancelled']);

        // Create cancellation notification
        NotificationService::appointmentCancelled($appointment);

        return redirect()->back()->with('status', 'Appointment cancelled successfully!');
    }

    /**
     * Assign appointment to a staff member (Admin only)
     */
    public function assign(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        
        if (!$user || $appointment->company_id !== $user->company_id) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'You can only assign appointments from your company.'], 403);
            }
            return redirect()->back()->withErrors(['permission' => 'You can only assign appointments from your company.']);
        }

        // Only admins can reassign appointments
        if ($user->role !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Only admins can assign appointments to staff.'], 403);
            }
            return redirect()->back()->withErrors(['permission' => 'Only admins can assign appointments to staff.']);
        }

        $request->validate([
            'assigned_user_id' => 'nullable|exists:users,id'
        ]);

        // Handle unassignment (empty assigned_user_id)
        if (empty($request->assigned_user_id)) {
            $previousAssignedUser = $appointment->assignedUser;
            
            $appointment->update([
                'assigned_user_id' => null
            ]);

            $message = "Appointment unassigned from " . ($previousAssignedUser ? $previousAssignedUser->name : 'staff') . " successfully!";
            
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return redirect()->back()->with('status', $message);
        }

        // Verify the assigned user belongs to the same company
        $assignedUser = User::find($request->assigned_user_id);
        if ($assignedUser->company_id !== $user->company_id) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'You can only assign to staff from your company.'], 403);
            }
            return redirect()->back()->withErrors(['permission' => 'You can only assign to staff from your company.']);
        }

        $previousAssignedUser = $appointment->assignedUser;
        
        $appointment->update([
            'assigned_user_id' => $request->assigned_user_id
        ]);

        // Create notification for new assigned user
        NotificationService::appointmentAssigned($appointment);

        $assignedUserName = $assignedUser->name;
        $previousUserName = $previousAssignedUser ? $previousAssignedUser->name : 'Unassigned';
        $message = "Appointment reassigned from {$previousUserName} to {$assignedUserName} successfully!";

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->back()->with('status', $message);
    }
}
