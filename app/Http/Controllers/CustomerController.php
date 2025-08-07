<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers for the current company
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user has permission (admin or staff)
        if (!$user || ($user->role !== 'admin' && $user->role !== 'user')) {
            return redirect()->route('dashboard')->withErrors(['permission' => 'Access denied. Admin or staff only.']);
        }

        $customers = Customer::where('company_id', $user->company_id)
                    ->with(['appointments' => function($query) {
                        $query->latest()->take(3);
                    }])
                    ->withCount('appointments')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user has permission (admin or staff)
        if (!$user || ($user->role !== 'admin' && $user->role !== 'user')) {
            return redirect()->route('dashboard')->withErrors(['permission' => 'Access denied. Admin or staff only.']);
        }

        return view('customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has permission (admin or staff)
        if (!$user || ($user->role !== 'admin' && $user->role !== 'user')) {
            return redirect()->route('dashboard')->withErrors(['permission' => 'Access denied. Admin or staff only.']);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,NULL,id,company_id,' . $user->company_id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'notes' => 'nullable|string|max:1000',
            'preferences' => 'nullable|string|max:500',
        ]);

        // Add company_id to the data
        $validatedData['company_id'] = $user->company_id;
        $validatedData['is_active'] = true;
        
        // Convert preferences to array if provided
        if ($validatedData['preferences']) {
            $validatedData['preferences'] = array_map('trim', explode(',', $validatedData['preferences']));
        }

        $customer = Customer::create($validatedData);

        return redirect()->route('customers.index')->with('success', "Customer '{$customer->name}' has been created successfully.");
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        $user = Auth::user();
        
        // Check if user has permission and customer belongs to their company
        if (!$user || ($user->role !== 'admin' && $user->role !== 'user') || $customer->company_id !== $user->company_id) {
            return redirect()->route('dashboard')->withErrors(['permission' => 'Access denied or customer not found.']);
        }

        $customer->load(['appointments.service', 'appointments.assignedUser']);
        
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit(Customer $customer)
    {
        $user = Auth::user();
        
        // Check if user has permission and customer belongs to their company
        if (!$user || ($user->role !== 'admin' && $user->role !== 'user') || $customer->company_id !== $user->company_id) {
            return redirect()->route('dashboard')->withErrors(['permission' => 'Access denied or customer not found.']);
        }

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();
        
        // Check if user has permission and customer belongs to their company
        if (!$user || ($user->role !== 'admin' && $user->role !== 'user') || $customer->company_id !== $user->company_id) {
            return redirect()->route('dashboard')->withErrors(['permission' => 'Access denied or customer not found.']);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->id . ',id,company_id,' . $user->company_id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'notes' => 'nullable|string|max:1000',
            'preferences' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        // Convert preferences to array if provided
        if (isset($validatedData['preferences']) && $validatedData['preferences']) {
            $validatedData['preferences'] = array_map('trim', explode(',', $validatedData['preferences']));
        } else {
            $validatedData['preferences'] = [];
        }

        $customer->update($validatedData);

        return redirect()->route('customers.show', $customer)->with('success', "Customer '{$customer->name}' has been updated successfully.");
    }

    /**
     * Remove the specified customer (soft delete - set inactive)
     */
    public function destroy(Customer $customer)
    {
        $user = Auth::user();
        
        // Check if user has permission (admin only for deletion) and customer belongs to their company
        if (!$user || $user->role !== 'admin' || $customer->company_id !== $user->company_id) {
            return redirect()->route('dashboard')->withErrors(['permission' => 'Access denied. Admin only or customer not found.']);
        }

        // Check if customer has upcoming appointments
        $upcomingAppointments = $customer->appointments()
                                          ->where('appointment_date', '>=', now())
                                          ->where('status', '!=', 'cancelled')
                                          ->count();

        if ($upcomingAppointments > 0) {
            return redirect()->back()->withErrors(['error' => "Cannot delete customer '{$customer->name}' as they have {$upcomingAppointments} upcoming appointment(s). Please cancel or reschedule them first."]);
        }

        // Soft delete by setting inactive
        $customer->update(['is_active' => false]);

        return redirect()->route('customers.index')->with('success', "Customer '{$customer->name}' has been deactivated successfully.");
    }

    /**
     * Reactivate a customer
     */
    public function reactivate(Customer $customer)
    {
        $user = Auth::user();
        
        // Check if user has permission and customer belongs to their company
        if (!$user || ($user->role !== 'admin' && $user->role !== 'user') || $customer->company_id !== $user->company_id) {
            return redirect()->route('dashboard')->withErrors(['permission' => 'Access denied or customer not found.']);
        }

        $customer->update(['is_active' => true]);

        return redirect()->back()->with('success', "Customer '{$customer->name}' has been reactivated successfully.");
    }
}
