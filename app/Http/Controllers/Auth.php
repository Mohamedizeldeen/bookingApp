<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;

class Auth extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }
    public function showRegistrationForm()
    {
        return view('signup');
    }

    public function login(Request $request)
    {
        // Handle login logic here
        $checked = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if(AuthFacade::attempt($checked, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = AuthFacade::user();
            
            // Redirect based on user role
            if ($user->isSuperAdmin()) {
                return redirect()->route('super-admin.dashboard');
            } elseif ($user->isEmployee()) {
                return redirect()->route('employee.dashboard');
            } else {
                return redirect()->intended('dashboard');
            }
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);

    }

    public function logout(Request $request)
    {
        AuthFacade::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function register(Request $request)
    {
        // First, validate all the data together
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'company_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'subscription_type' => 'required|in:free,starter,professional,enterprise',
        ]);

        // Check if the company exists by company_name
        $existingCompany = company::where('company_name', $validatedData['company_name'])->first();
        
        if ($existingCompany) {
            return back()->withErrors(['company_name' => 'Company already exists.']);
        }

        // Create new company first (without user_id for now)
        $company = company::create([
            'company_name' => $validatedData['company_name'],
            'phone' => $validatedData['phone'],
            'type_of_subscription' => $validatedData['subscription_type'],
            // Don't set user_id here to avoid foreign key constraint
        ]);

        // Create the user with company_id
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'company_id' => $company->id,
            'role' => 'admin', // First user of company should be admin
        ]);

        // Now update company with the user_id (owner of the company)
        $company->update(['user_id' => $user->id]);

        return redirect()->route('login.show')->with('status', 'Registration successful! Please login to continue.');
    }

    public function addUser(Request $request)
    {
        // Check if the current user is an admin
        $currentUser = AuthFacade::user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            return back()->withErrors(['permission' => 'You do not have permission to add users.']);
        }

        // Validate user information (role is automatically set to 'user')
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create the user with the same company_id as the current user
        // Role is automatically set to 'user' (staff) - only one admin per company
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'company_id' => $currentUser->company_id,
            'role' => 'user', // Always create staff users, not admins
        ]);

        return redirect()->back()->with('status', 'Staff member added successfully to your company.');
    }
}
