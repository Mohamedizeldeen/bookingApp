<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyBlocked
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }

        // Skip check for super admins and employees
        if ($user->isSuperAdmin() || $user->isEmployee()) {
            return $next($request);
        }

        // Check if user's company is blocked
        if ($user->company && $user->company->is_blocked) {
            Auth::logout();
            
            $blockReason = $user->company->block_reason ?? 'Account suspended due to non-payment. Please contact support.';
            
            return redirect()->route('login.show')
                ->withErrors(['blocked' => $blockReason])
                ->with('company_blocked', true);
        }

        // Check if subscription is expired
        if ($user->company && $user->company->subscription_status === 'expired') {
            Auth::logout();
            
            return redirect()->route('login.show')
                ->withErrors(['expired' => 'Your subscription has expired. Please renew to continue using the service.'])
                ->with('subscription_expired', true);
        }

        return $next($request);
    }
}
