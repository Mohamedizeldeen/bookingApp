<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Company;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LocationService
{
    /**
     * Create a new location for a company
     */
    public function createLocation(Company $company, array $locationData)
    {
        $location = new Location();
        $location->company_id = $company->id;
        $location->name = $locationData['name'];
        $location->address = $locationData['address'];
        $location->phone = $locationData['phone'] ?? null;
        $location->email = $locationData['email'] ?? null;
        $location->latitude = $locationData['latitude'] ?? null;
        $location->longitude = $locationData['longitude'] ?? null;
        $location->timezone = $locationData['timezone'] ?? config('app.timezone');
        $location->working_hours = $locationData['working_hours'] ?? $this->getDefaultWorkingHours();
        $location->is_active = $locationData['is_active'] ?? true;
        $location->save();

        return $location;
    }

    /**
     * Update an existing location
     */
    public function updateLocation(Location $location, array $locationData)
    {
        $location->update([
            'name' => $locationData['name'] ?? $location->name,
            'address' => $locationData['address'] ?? $location->address,
            'phone' => $locationData['phone'] ?? $location->phone,
            'email' => $locationData['email'] ?? $location->email,
            'latitude' => $locationData['latitude'] ?? $location->latitude,
            'longitude' => $locationData['longitude'] ?? $location->longitude,
            'timezone' => $locationData['timezone'] ?? $location->timezone,
            'working_hours' => $locationData['working_hours'] ?? $location->working_hours,
            'is_active' => $locationData['is_active'] ?? $location->is_active,
        ]);

        return $location;
    }

    /**
     * Get locations for a company with availability
     */
    public function getCompanyLocationsWithAvailability(Company $company, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $locations = $company->locations()->active()->get();

        foreach ($locations as $location) {
            $location->is_open = $this->isLocationOpen($location, $date);
            $location->appointment_count = $this->getLocationAppointmentCount($location, $date);
            $location->availability_slots = $this->getLocationAvailableSlots($location, $date);
        }

        return $locations;
    }

    /**
     * Check if location is open at given date/time
     */
    public function isLocationOpen(Location $location, Carbon $datetime)
    {
        $workingHours = $location->working_hours ?? $this->getDefaultWorkingHours();
        $dayOfWeek = strtolower($datetime->format('l'));
        
        if (!isset($workingHours[$dayOfWeek]) || !$workingHours[$dayOfWeek]['is_open']) {
            return false;
        }

        $dayHours = $workingHours[$dayOfWeek];
        $currentTime = $datetime->format('H:i');
        
        return $currentTime >= $dayHours['start_time'] && $currentTime <= $dayHours['end_time'];
    }

    /**
     * Get appointment count for a location on a specific date
     */
    public function getLocationAppointmentCount(Location $location, Carbon $date)
    {
        return $location->appointments()
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->count();
    }

    /**
     * Get available time slots for a location on a specific date
     */
    public function getLocationAvailableSlots(Location $location, Carbon $date, $serviceDuration = 60)
    {
        if (!$this->isLocationOpen($location, $date)) {
            return [];
        }

        $workingHours = $location->working_hours ?? $this->getDefaultWorkingHours();
        $dayOfWeek = strtolower($date->format('l'));
        
        if (!isset($workingHours[$dayOfWeek]) || !$workingHours[$dayOfWeek]['is_open']) {
            return [];
        }

        $dayHours = $workingHours[$dayOfWeek];
        $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $dayHours['start_time']);
        $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $dayHours['end_time']);

        // Get existing appointments for this location and date
        $existingAppointments = $location->appointments()
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')
            ->get();

        $availableSlots = [];
        $currentTime = $startTime->copy();

        while ($currentTime->copy()->addMinutes($serviceDuration)->lte($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($serviceDuration);
            
            // Check if this slot conflicts with any existing appointment
            $hasConflict = $existingAppointments->contains(function ($appointment) use ($currentTime, $slotEnd) {
                $appointmentStart = $appointment->appointment_date;
                $appointmentEnd = $appointment->end_time;
                
                return $currentTime->lt($appointmentEnd) && $slotEnd->gt($appointmentStart);
            });

            if (!$hasConflict) {
                $availableSlots[] = [
                    'start_time' => $currentTime->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'datetime' => $currentTime->toISOString(),
                ];
            }

            $currentTime->addMinutes(30); // 30-minute slots
        }

        return $availableSlots;
    }

    /**
     * Get nearby locations using GPS coordinates
     */
    public function getNearbyLocations($latitude, $longitude, $radiusKm = 10): Collection
    {
        return Location::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->filter(function ($location) use ($latitude, $longitude, $radiusKm) {
                $distance = $this->calculateDistance($latitude, $longitude, $location->latitude, $location->longitude);
                return $distance !== null && $distance <= $radiusKm;
            })
            ->sortBy(function ($location) use ($latitude, $longitude) {
                return $this->calculateDistance($latitude, $longitude, $location->latitude, $location->longitude);
            });
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula
     */
    public function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        if (!$lat1 || !$lng1 || !$lat2 || !$lng2) {
            return null;
        }

        $earthRadius = 6371; // kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Get location analytics
     */
    public function getLocationAnalytics(Location $location, $days = 30)
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        $appointments = $location->appointments()
            ->with('service')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->get();

        $completed = $appointments->where('status', 'completed');
        
        return [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $completed->count(),
            'cancelled_appointments' => $appointments->where('status', 'cancelled')->count(),
            'revenue' => $completed->sum(function($appointment) {
                return floatval($appointment->service->price ?? 0);
            }),
            'utilization_rate' => $this->getLocationUtilization($location, $endDate) * 100,
            'average_daily_appointments' => round($appointments->count() / $days, 1),
            'completion_rate' => $appointments->count() > 0 
                ? round(($completed->count() / $appointments->count()) * 100, 1) 
                : 0,
        ];
    }

    /**
     * Get location capacity
     */
    public function getLocationCapacity(Location $location, Carbon $date)
    {
        $availableSlots = $this->getLocationAvailableSlots($location, $date);
        $bookedSlots = $this->getLocationAppointmentCount($location, $date);
        $totalSlots = count($availableSlots) + $bookedSlots;

        return [
            'total_slots' => $totalSlots,
            'available_slots' => count($availableSlots),
            'booked_slots' => $bookedSlots,
            'utilization_percentage' => $totalSlots > 0 ? round(($bookedSlots / $totalSlots) * 100, 1) : 0,
        ];
    }

    /**
     * Get location utilization rate
     */
    public function getLocationUtilization(Location $location, Carbon $date)
    {
        $workingHours = $location->working_hours ?? $this->getDefaultWorkingHours();
        $dayOfWeek = strtolower($date->format('l'));
        
        if (!isset($workingHours[$dayOfWeek]) || !$workingHours[$dayOfWeek]['is_open']) {
            return 0;
        }

        $dayHours = $workingHours[$dayOfWeek];
        $startTime = Carbon::parse($dayHours['start_time']);
        $endTime = Carbon::parse($dayHours['end_time']);
        $totalMinutes = $endTime->diffInMinutes($startTime);

        $bookedMinutes = $location->appointments()
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'confirmed', 'completed'])
            ->with('service')
            ->get()
            ->sum(function($appointment) {
                return $appointment->service->duration ?? 60;
            });

        return $totalMinutes > 0 ? round($bookedMinutes / $totalMinutes, 2) : 0;
    }

    /**
     * Get location recommendations for optimization
     */
    public function getLocationRecommendations(Company $company, array $context = []): array
    {
        $recommendations = [];
        $locations = $company->locations()->with('appointments')->get();
        
        foreach ($locations as $location) {
            $analytics = $this->getLocationAnalytics($location, 30);
            $recommendation = [
                'location' => $location,
                'suggestions' => [],
                'priority' => 'low',
            ];
            
            // Low utilization suggestion
            if ($analytics['utilization_rate'] < 30) {
                $recommendation['suggestions'][] = [
                    'type' => 'marketing',
                    'title' => 'Increase Marketing',
                    'message' => 'Location has low utilization (' . $analytics['utilization_rate'] . '%). Consider marketing campaigns.',
                    'action' => 'Create targeted local advertising',
                ];
                $recommendation['priority'] = 'medium';
            }
            
            // High utilization suggestion
            if ($analytics['utilization_rate'] > 85) {
                $recommendation['suggestions'][] = [
                    'type' => 'capacity',
                    'title' => 'Expand Capacity',
                    'message' => 'Location operating at high capacity (' . $analytics['utilization_rate'] . '%). Consider expansion.',
                    'action' => 'Extend hours or add staff',
                ];
                $recommendation['priority'] = 'high';
            }
            
            if (!empty($recommendation['suggestions'])) {
                $recommendations[] = $recommendation;
            }
        }
        
        return $recommendations;
    }

    /**
     * Get default working hours
     */
    private function getDefaultWorkingHours()
    {
        return [
            'monday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
            'tuesday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
            'wednesday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
            'thursday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
            'friday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
            'saturday' => ['is_open' => false, 'start_time' => '10:00', 'end_time' => '14:00'],
            'sunday' => ['is_open' => false, 'start_time' => '10:00', 'end_time' => '14:00'],
        ];
    }
}
