<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckCompanyStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip check for super admin and employee
        if ($user && ($user->isSuperAdmin() || $user->isEmployee())) {
            return $next($request);
        }

        // Check if user belongs to a company
        if ($user && $user->company_id) {
            $company = $user->company;
            
            // If company is blocked, logout user and redirect
            if ($company && $company->is_blocked) {
                Auth::logout();
                
                return redirect()->route('login.show')->withErrors([
                    'blocked' => "Your company account has been suspended. Reason: {$company->block_reason}. Please contact support to resolve this issue."
                ]);
            }
            
            // If subscription is expired, show warning but allow access
            if ($company && $company->subscription_status === 'expired') {
                session()->flash('subscription_warning', 'Your company subscription has expired. Please renew to continue using all features.');
            }
        }

        return $next($request);
    }
}
