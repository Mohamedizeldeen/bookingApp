<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of services for the authenticated user's company.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login.show');
        }

        $services = $user->company->services()->get();
        
        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service (Admin only).
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Only company admins can create services.']);
        }

        return view('services.create');
    }

    /**
     * Store a newly created service (Admin only).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Only company admins can create services.']);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:15|max:480', // 15 minutes to 8 hours
            'price' => 'required|numeric|min:0',
        ]);

        Service::create([
            'company_id' => $user->company_id,
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'duration_minutes' => $validatedData['duration'],
            'price' => $validatedData['price'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('services.index')->with('status', 'Service created successfully!');
    }

    /**
     * Show the form for editing a service (Admin only).
     */
    public function edit(Service $service)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Only company admins can edit services.']);
        }

        // Ensure the service belongs to the user's company
        if ($service->company_id !== $user->company_id) {
            return redirect()->back()->withErrors(['permission' => 'You can only edit services from your company.']);
        }

        return view('services.edit', compact('service'));
    }

    /**
     * Update a service (Admin only).
     */
    public function update(Request $request, Service $service)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Only company admins can update services.']);
        }

        // Ensure the service belongs to the user's company
        if ($service->company_id !== $user->company_id) {
            return redirect()->back()->withErrors(['permission' => 'You can only update services from your company.']);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:15|max:480',
            'price' => 'required|numeric|min:0',
        ]);

        $service->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'duration_minutes' => $validatedData['duration'],
            'price' => $validatedData['price'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('services.index')->with('status', 'Service updated successfully!');
    }

    /**
     * Remove a service (Admin only).
     */
    public function destroy(Service $service)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Only company admins can delete services.']);
        }

        // Ensure the service belongs to the user's company
        if ($service->company_id !== $user->company_id) {
            return redirect()->back()->withErrors(['permission' => 'You can only delete services from your company.']);
        }

        $service->delete();

        return redirect()->route('services.index')->with('status', 'Service deleted successfully!');
    }
}
