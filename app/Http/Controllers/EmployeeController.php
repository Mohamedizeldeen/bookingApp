<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Employee Dashboard for subscription checking
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user || !$user->canCheckSubscriptions()) {
            return redirect()->route('login.show')->withErrors(['permission' => 'Access denied. Employee access only.']);
        }

        // Get all companies with subscription info
        $companies = Company::with(['users'])
            ->withCount(['users', 'services', 'appointments'])
            ->get();

        // Filter companies by subscription status
        $activeCompanies = $companies->where('subscription_status', 'active');
        $pendingCompanies = $companies->where('subscription_status', 'pending');
        $expiredCompanies = $companies->where('subscription_status', 'expired');
        $blockedCompanies = $companies->where('is_blocked', true);

        // Companies needing attention
        $expiringSoon = $companies->filter(function($company) {
            return $company->getDaysUntilExpiry() !== null && $company->getDaysUntilExpiry() <= 7 && $company->getDaysUntilExpiry() >= 0;
        });

        $overduePayments = $companies->filter(function($company) {
            return $company->next_payment_due && $company->next_payment_due->isPast();
        });

        return view('employee.dashboard', compact(
            'companies', 
            'activeCompanies', 
            'pendingCompanies', 
            'expiredCompanies', 
            'blockedCompanies',
            'expiringSoon',
            'overduePayments'
        ));
    }

    /**
     * Mark payment as received
     */
    public function markPaymentReceived(Company $company)
    {
        $user = Auth::user();
        
        if (!$user || !$user->canCheckSubscriptions()) {
            return redirect()->back()->withErrors(['permission' => 'Access denied. Employee access only.']);
        }

        $company->update([
            'last_payment_date' => now(),
            'next_payment_due' => now()->addMonth(),
            'subscription_status' => 'active',
            'is_blocked' => false,
            'block_reason' => null,
            'blocked_at' => null,
        ]);

        return redirect()->back()->with('status', "Payment marked as received for '{$company->company_name}'. Subscription extended for one month.");
    }

    /**
     * View company subscription details
     */
    public function viewCompany(Company $company)
    {
        $user = Auth::user();
        
        if (!$user || !$user->canCheckSubscriptions()) {
            return redirect()->route('login.show')->withErrors(['permission' => 'Access denied. Employee access only.']);
        }

        $company->load(['users', 'services']);
        
        return view('employee.company-details', compact('company'));
    }
}
