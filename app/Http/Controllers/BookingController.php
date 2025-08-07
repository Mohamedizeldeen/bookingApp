<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Notification;
use App\Services\NotificationService;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show the public booking page for a company.
     */
    public function showBookingPage($companyId, $slug = null)
    {
        $company = Company::with(['services'])->find($companyId);
        
        if (!$company) {
            abort(404, 'Company not found');
        }

        $services = $company->services()->get();
        
        return view('booking.public', compact('company', 'services'));
    }

    /**
     * Store a new booking from public form.
     */
    public function storeBooking(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        
        if (!$company) {
            abort(404, 'Company not found');
        }

        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after:now',
            'notes' => 'nullable|string|max:500',
            'preferences' => 'nullable|string|max:500',
        ]);

        // Verify service belongs to the company
        $service = Service::find($validatedData['service_id']);
        if ($service->company_id !== $company->id) {
            return redirect()->back()->withErrors(['service_id' => 'Invalid service selection.']);
        }

        // Create or find customer
        $customer = Customer::firstOrCreate(
            [
                'email' => $validatedData['customer_email'],
                'company_id' => $company->id,
            ],
            [
                'name' => $validatedData['customer_name'],
                'phone' => $validatedData['customer_phone'],
                'preferences' => $validatedData['preferences'],
            ]
        );

        // Create appointment
        // For public bookings, assign to company admin as creator
        $companyAdmin = $company->users()->where('role', 'admin')->first();
        
        $appointment = Appointment::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'created_by' => $companyAdmin ? $companyAdmin->id : null,
            'assigned_user_id' => $companyAdmin ? $companyAdmin->id : null,
            'appointment_date' => $validatedData['appointment_date'],
            'end_time' => \Carbon\Carbon::parse($validatedData['appointment_date'])->addMinutes($service->duration),
            'price' => $service->price,
            'notes' => $validatedData['notes'],
            'status' => 'scheduled',
        ]);

        // Notify company admin about new booking
        NotificationService::newOnlineBooking($appointment);

        return redirect()->back()->with('success', 'Your appointment has been booked successfully! We will contact you soon to confirm.');
    }

    /**
     * Show booking confirmation page.
     */
    public function showConfirmation($appointmentId)
    {
        $appointment = Appointment::with(['company', 'service', 'customer'])->find($appointmentId);
        
        if (!$appointment) {
            abort(404, 'Appointment not found');
        }

        return view('booking.confirmation', compact('appointment'));
    }
}
