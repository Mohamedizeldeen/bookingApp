<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\LocationService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    protected $locationService;
    protected $analyticsService;

    public function __construct(LocationService $locationService, AnalyticsService $analyticsService)
    {
        $this->locationService = $locationService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display company locations
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        $date = $request->get('date', now()->format('Y-m-d'));
        $locations = $this->locationService->getCompanyLocationsWithAvailability($company, $date);

        return view('locations.index', compact('locations', 'company', 'date'));
    }

    /**
     * Show location creation form
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can create locations.');
        }

        return view('locations.create');
    }

    /**
     * Store new location
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can create locations.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'timezone' => 'nullable|string|max:50',
            'working_hours' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $locationData = $request->all();
        
        // Validate working hours if provided
        if ($request->has('working_hours')) {
            if (!$this->locationService->validateWorkingHours($request->working_hours)) {
                return redirect()->back()
                    ->withErrors(['working_hours' => 'Invalid working hours format.'])
                    ->withInput();
            }
        }

        try {
            $location = $this->locationService->createLocation($user->company, $locationData);
            
            return redirect()->route('locations.index')
                ->with('success', 'Location created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create location: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show location details
     */
    public function show(Location $location)
    {
        $user = Auth::user();
        
        if ($location->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $analytics = $this->locationService->getLocationAnalytics($location);
        $capacity = $this->locationService->getLocationCapacity($location, now());

        return view('locations.show', compact('location', 'analytics', 'capacity'));
    }

    /**
     * Show location edit form
     */
    public function edit(Location $location)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $location->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        return view('locations.edit', compact('location'));
    }

    /**
     * Update location
     */
    public function update(Request $request, Location $location)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $location->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'timezone' => 'nullable|string|max:50',
            'working_hours' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate working hours if provided
        if ($request->has('working_hours')) {
            if (!$this->locationService->validateWorkingHours($request->working_hours)) {
                return redirect()->back()
                    ->withErrors(['working_hours' => 'Invalid working hours format.'])
                    ->withInput();
            }
        }

        try {
            $this->locationService->updateLocation($location, $request->all());
            
            return redirect()->route('locations.show', $location)
                ->with('success', 'Location updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update location: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Deactivate location
     */
    public function destroy(Location $location)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $location->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        try {
            $result = $this->locationService->deactivateLocation($location);
            
            $message = 'Location deactivated successfully.';
            if ($result['future_appointments_count'] > 0) {
                $message .= " {$result['future_appointments_count']} future appointments were affected.";
            }
            
            return redirect()->route('locations.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to deactivate location: ' . $e->getMessage()]);
        }
    }

    /**
     * Get location availability slots (AJAX)
     */
    public function getAvailability(Request $request, Location $location)
    {
        $user = Auth::user();
        
        if ($location->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $date = $request->get('date', now()->format('Y-m-d'));
        $serviceDuration = $request->get('duration', 60);
        
        $slots = $this->locationService->getLocationAvailableSlots($location, $date, $serviceDuration);
        
        return response()->json([
            'success' => true,
            'slots' => $slots,
            'date' => $date,
        ]);
    }

    /**
     * Find nearest locations (AJAX)
     */
    public function findNearest(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->get('radius', 50);
        $limit = $request->get('limit', 10);

        $locations = $this->locationService->findNearestLocations(
            $user->company, 
            $latitude, 
            $longitude, 
            $radius, 
            $limit
        );

        return response()->json([
            'success' => true,
            'locations' => $locations,
        ]);
    }

    /**
     * Transfer appointments between locations
     */
    public function transferAppointments(Request $request, Location $fromLocation)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $fromLocation->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'to_location_id' => 'required|exists:locations,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $toLocation = Location::find($request->to_location_id);
        
        if ($toLocation->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        try {
            $transferredCount = $this->locationService->transferAppointments(
                $fromLocation,
                $toLocation,
                $request->start_date,
                $request->end_date
            );
            
            return redirect()->back()
                ->with('success', "Successfully transferred {$transferredCount} appointments.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to transfer appointments: ' . $e->getMessage()]);
        }
    }
}
