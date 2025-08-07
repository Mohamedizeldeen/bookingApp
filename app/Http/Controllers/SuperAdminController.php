<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    /**
     * Super Admin Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->route('login.show')->withErrors(['permission' => 'Access denied. Super admin only.']);
        }

        // Get all companies with statistics
        $companies = Company::with(['users', 'services', 'appointments', 'customers'])
            ->withCount(['users', 'services', 'appointments', 'customers'])
            ->get();

        // Calculate statistics
        $totalCompanies = $companies->count();
        $activeCompanies = $companies->where('subscription_status', 'active');
        $blockedCompanies = $companies->where('is_blocked', true);
        $pendingCompanies = $companies->where('subscription_status', 'pending');
        $expiredCompanies = $companies->where('subscription_status', 'expired');
        $totalRevenue = $companies->sum('monthly_fee');

        // Companies needing attention
        $expiringSoon = $companies->filter(function($company) {
            return $company->getDaysUntilExpiry() !== null && $company->getDaysUntilExpiry() <= 7 && $company->getDaysUntilExpiry() >= 0;
        });

        $overduePayments = $companies->filter(function($company) {
            return $company->next_payment_due && $company->next_payment_due->isPast();
        });

        return view('super-admin.dashboard', compact(
            'companies', 
            'totalCompanies',
            'activeCompanies', 
            'blockedCompanies', 
            'pendingCompanies', 
            'expiredCompanies',
            'totalRevenue',
            'expiringSoon', 
            'overduePayments'
        ));
    }

    /**
     * Show company details
     */
    public function showCompany(Company $company)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->route('login.show')->withErrors(['permission' => 'Access denied. Super admin only.']);
        }

        $company->load(['users', 'services', 'appointments.customer', 'customers']);
        
        // Get recent activity
        $recentAppointments = $company->appointments()
            ->with(['customer', 'service', 'assignedUser'])
            ->latest()
            ->take(10)
            ->get();

        return view('super-admin.company-details', compact('company', 'recentAppointments'));
    }

    /**
     * Block a company
     */
    public function blockCompany(Request $request, Company $company)
    {
        $user = Auth::user();
        
        Log::info('Block company method called', [
            'user_id' => $user ? $user->id : 'null',
            'user_role' => $user ? $user->role : 'null',
            'company_id' => $company->id,
            'company_name' => $company->company_name,
            'request_data' => $request->all()
        ]);
        
        if (!$user || !$user->isSuperAdmin()) {
            Log::warning('Access denied for block company', ['user_id' => $user ? $user->id : 'null']);
            return redirect()->back()->withErrors(['permission' => 'Access denied. Super admin only.']);
        }

        $validatedData = $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        try {
            Log::info('Attempting to block company', ['company_id' => $company->id, 'reason' => $validatedData['reason']]);
            $company->block($validatedData['reason']);
            Log::info('Company blocked successfully', ['company_id' => $company->id]);
            
            return redirect()->back()->with('status', "Company '{$company->company_name}' has been blocked successfully.");
        } catch (\Exception $e) {
            Log::error('Failed to block company', ['company_id' => $company->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to block company: ' . $e->getMessage()]);
        }
    }

    /**
     * Unblock a company
     */
    public function unblockCompany(Company $company)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Access denied. Super admin only.']);
        }

        $company->unblock();

        return redirect()->back()->with('status', "Company '{$company->company_name}' has been unblocked successfully.");
    }

    /**
     * Update company subscription
     */
    public function updateSubscription(Request $request, Company $company)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Access denied. Super admin only.']);
        }

        $request->validate([
            'subscription_status' => 'required|in:active,blocked,pending,expired',
            'monthly_fee' => 'required|numeric|min:0',
            'subscription_start_date' => 'nullable|date',
            'subscription_end_date' => 'nullable|date|after:subscription_start_date',
        ]);

        $company->updateSubscription(
            $request->subscription_status,
            $request->subscription_start_date,
            $request->subscription_end_date,
            $request->monthly_fee
        );

        if ($request->subscription_status === 'active') {
            $company->update(['last_payment_date' => now()]);
        }

        return redirect()->back()->with('status', "Subscription updated successfully for '{$company->company_name}'.");
    }

    /**
     * Delete a company (dangerous operation)
     */
    public function deleteCompany(Company $company)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Access denied. Super admin only.']);
        }

        $companyName = $company->company_name;
        
        // Delete all related data
        $company->appointments()->delete();
        $company->customers()->delete();
        $company->services()->delete();
        $company->notifications()->delete();
        $company->users()->delete();
        $company->delete();

        return redirect()->route('super-admin.dashboard')->with('status', "Company '{$companyName}' and all its data has been permanently deleted.");
    }

    /**
     * Show the form for creating a new company
     */
    public function createCompany()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->route('login.show')->withErrors(['permission' => 'Access denied. Super admin only.']);
        }

        return view('super-admin.create-company');
    }

    /**
     * Store a newly created company
     */
    public function storeCompany(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->route('login.show')->withErrors(['permission' => 'Access denied. Super admin only.']);
        }

        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:6',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'subscription_status' => 'required|in:active,pending,blocked,expired',
            'monthly_fee' => 'required|numeric|min:0',
            'subscription_start_date' => 'nullable|date',
            'next_payment_due' => 'nullable|date|after:today',
        ]);

        try {
            // Create the company
            $company = Company::create([
                'company_name' => $validatedData['company_name'],
                'phone' => $validatedData['contact_phone'],
                'contact_email' => $validatedData['contact_email'] ?? $validatedData['admin_email'],
                'address' => $validatedData['address'],
                'subscription_status' => $validatedData['subscription_status'],
                'monthly_fee' => $validatedData['monthly_fee'],
                'subscription_start_date' => $validatedData['subscription_start_date'] ? Carbon::parse($validatedData['subscription_start_date']) : now(),
                'next_payment_due' => $validatedData['next_payment_due'] ? Carbon::parse($validatedData['next_payment_due']) : null,
                'last_payment_date' => $validatedData['subscription_status'] === 'active' ? now() : null,
                'is_blocked' => false,
            ]);

            // Create the admin user for the company
            $adminUser = User::create([
                'name' => $validatedData['admin_name'],
                'email' => $validatedData['admin_email'],
                'password' => Hash::make($validatedData['admin_password']),
                'role' => 'admin',
                'company_id' => $company->id,
                'email_verified_at' => now(),
            ]);

            // Update company with owner (user_id)
            $company->update(['user_id' => $adminUser->id]);

            return redirect()->route('super-admin.dashboard')->with('status', "Company '{$company->company_name}' created successfully with admin user '{$adminUser->name}'.");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create company: ' . $e->getMessage()])->withInput();
        }
    }
}
