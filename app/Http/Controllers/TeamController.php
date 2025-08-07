<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display team members for the authenticated admin's company.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Only company admins can manage team members.']);
        }

        // Get all team members for the admin's company, excluding the admin themselves
        $teamMembers = $user->company->users()
            ->where('id', '!=', $user->id) // Exclude the admin
            ->with(['assignedAppointments' => function($query) {
                $query->whereDate('appointment_date', '>=', today());
            }])
            ->get();

        return view('team.index', compact('teamMembers'));
    }

    /**
     * Remove a team member (Admin only).
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'Only company admins can delete team members.']);
        }

        // Ensure the user being deleted belongs to the same company
        if ($user->company_id !== $currentUser->company_id) {
            return redirect()->back()->withErrors(['permission' => 'You can only delete team members from your company.']);
        }

        // Prevent admin from deleting themselves
        if ($user->id === $currentUser->id) {
            return redirect()->back()->withErrors(['permission' => 'You cannot delete your own admin account.']);
        }

        // Prevent deleting other admins
        if ($user->isAdmin()) {
            return redirect()->back()->withErrors(['permission' => 'You cannot delete another admin account.']);
        }

        // Check if the user has any upcoming appointments
        $upcomingAppointments = $user->assignedAppointments()
            ->whereDate('appointment_date', '>=', today())
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($upcomingAppointments > 0) {
            return redirect()->back()->withErrors([
                'appointments' => "Cannot delete {$user->name}. They have {$upcomingAppointments} upcoming appointment(s). Please reassign or cancel these appointments first."
            ]);
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('team.index')->with('status', "Team member '{$userName}' has been successfully removed from your company.");
    }
}
