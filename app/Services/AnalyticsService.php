<?php

namespace App\Services;

use App\Models\Analytics;
use App\Models\Company;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Generate daily analytics for all companies
     */
    public function generateDailyAnalytics($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $companies = Company::all();

        foreach ($companies as $company) {
            $this->generateCompanyAnalytics($company, $date);
        }
    }

    /**
     * Generate analytics for a specific company
     */
    public function generateCompanyAnalytics(Company $company, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        // Appointment metrics
        $this->recordAppointmentMetrics($company, $date);
        
        // Revenue metrics
        $this->recordRevenueMetrics($company, $date);
        
        // Customer metrics
        $this->recordCustomerMetrics($company, $date);
        
        // Staff performance metrics
        $this->recordStaffPerformanceMetrics($company, $date);
        
        // Location metrics (if multi-location)
        $this->recordLocationMetrics($company, $date);
    }

    /**
     * Record appointment-related metrics
     */
    private function recordAppointmentMetrics(Company $company, Carbon $date)
    {
        $appointments = $company->appointments()->whereDate('appointment_date', $date);

        // Total appointments
        Analytics::record($company->id, $date, 'appointments', 'total_appointments', 
            $appointments->count());

        // Appointments by status
        $statusCounts = $appointments->groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status');

        foreach (['scheduled', 'confirmed', 'completed', 'cancelled'] as $status) {
            Analytics::record($company->id, $date, 'appointments', $status . '_appointments', 
                $statusCounts[$status] ?? 0);
        }

        // Completion rate
        $totalScheduled = ($statusCounts['scheduled'] ?? 0) + ($statusCounts['confirmed'] ?? 0) + ($statusCounts['completed'] ?? 0);
        $completionRate = $totalScheduled > 0 ? (($statusCounts['completed'] ?? 0) / $totalScheduled) * 100 : 0;
        
        Analytics::record($company->id, $date, 'appointments', 'completion_rate', $completionRate);

        // Cancellation rate
        $totalBooked = $appointments->count();
        $cancellationRate = $totalBooked > 0 ? (($statusCounts['cancelled'] ?? 0) / $totalBooked) * 100 : 0;
        
        Analytics::record($company->id, $date, 'appointments', 'cancellation_rate', $cancellationRate);
    }

    /**
     * Record revenue-related metrics
     */
    private function recordRevenueMetrics(Company $company, Carbon $date)
    {
        $completedAppointments = $company->appointments()
            ->whereDate('appointment_date', $date)
            ->where('status', 'completed')
            ->with('service');

        // Daily revenue
        $dailyRevenue = $completedAppointments->get()->sum(function ($appointment) {
            return floatval($appointment->service->price);
        });
        
        Analytics::record($company->id, $date, 'revenue', 'daily_revenue', $dailyRevenue);

        // Average revenue per appointment
        $appointmentCount = $completedAppointments->count();
        $avgRevenue = $appointmentCount > 0 ? $dailyRevenue / $appointmentCount : 0;
        
        Analytics::record($company->id, $date, 'revenue', 'avg_revenue_per_appointment', $avgRevenue);

        // Monthly revenue (rolling 30 days)
        $monthlyRevenue = $company->appointments()
            ->whereBetween('appointment_date', [$date->copy()->subDays(29), $date])
            ->where('status', 'completed')
            ->with('service')
            ->get()
            ->sum(function ($appointment) {
                return floatval($appointment->service->price);
            });
            
        Analytics::record($company->id, $date, 'revenue', 'monthly_revenue', $monthlyRevenue);
    }

    /**
     * Record customer-related metrics
     */
    private function recordCustomerMetrics(Company $company, Carbon $date)
    {
        // New customers today
        $newCustomers = $company->customers()->whereDate('created_at', $date)->count();
        Analytics::record($company->id, $date, 'customers', 'new_customers', $newCustomers);

        // Total active customers
        $totalCustomers = $company->customers()->count();
        Analytics::record($company->id, $date, 'customers', 'total_customers', $totalCustomers);

        // Returning customers (had appointment before)
        $returningCustomers = $company->appointments()
            ->whereDate('appointment_date', $date)
            ->whereHas('customer', function ($query) use ($date) {
                $query->whereHas('appointments', function ($subQuery) use ($date) {
                    $subQuery->whereDate('appointment_date', '<', $date);
                });
            })
            ->distinct('customer_id')
            ->count();
            
        Analytics::record($company->id, $date, 'customers', 'returning_customers', $returningCustomers);
    }

    /**
     * Record staff performance metrics
     */
    private function recordStaffPerformanceMetrics(Company $company, Carbon $date)
    {
        $staff = $company->users()->whereIn('role', ['admin', 'user'])->get();

        foreach ($staff as $user) {
            $userAppointments = $company->appointments()
                ->whereDate('appointment_date', $date)
                ->where('assigned_user_id', $user->id);

            // Appointments handled by this staff member
            $appointmentCount = $userAppointments->count();
            Analytics::record($company->id, $date, 'staff_performance', 'appointments_handled', 
                $appointmentCount, ['staff_id' => $user->id, 'staff_name' => $user->name]);

            // Revenue generated by this staff member
            $staffRevenue = $userAppointments->where('status', 'completed')
                ->with('service')
                ->get()
                ->sum(function ($appointment) {
                    return floatval($appointment->service->price);
                });
                
            Analytics::record($company->id, $date, 'staff_performance', 'revenue_generated', 
                $staffRevenue, ['staff_id' => $user->id, 'staff_name' => $user->name]);
        }
    }

    /**
     * Record location-specific metrics
     */
    private function recordLocationMetrics(Company $company, Carbon $date)
    {
        $locations = $company->locations()->active()->get();

        foreach ($locations as $location) {
            $locationAppointments = $company->appointments()
                ->whereDate('appointment_date', $date)
                ->where('location_id', $location->id);

            // Appointments at this location
            $appointmentCount = $locationAppointments->count();
            Analytics::record($company->id, $date, 'location_performance', 'appointments', 
                $appointmentCount, ['location_id' => $location->id, 'location_name' => $location->name]);

            // Revenue at this location
            $locationRevenue = $locationAppointments->where('status', 'completed')
                ->with('service')
                ->get()
                ->sum(function ($appointment) {
                    return floatval($appointment->service->price);
                });
                
            Analytics::record($company->id, $date, 'location_performance', 'revenue', 
                $locationRevenue, ['location_id' => $location->id, 'location_name' => $location->name]);
        }
    }

    /**
     * Get dashboard analytics for a company
     */
    public function getDashboardAnalytics(Company $company, $days = 30)
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        $appointments = $this->getAppointmentAnalytics($company, $startDate, $endDate);
        $revenue = $this->getRevenueAnalytics($company, $startDate, $endDate);
        $customers = $this->getCustomerAnalytics($company, $startDate, $endDate);
        $staff = $this->getStaffAnalytics($company, $startDate, $endDate);
        $locations = $this->getLocationAnalytics($company, $startDate, $endDate);

        // Return both flat and nested structure for compatibility
        return [
            // Flattened main metrics for backward compatibility
            'total_appointments' => $appointments['total'] ?? 0,
            'revenue' => $revenue['total'] ?? 0,
            'new_customers' => $customers['new_customers'] ?? 0,
            'completion_rate' => $appointments['completion_rate'] ?? 0,
            'total_customers' => $customers['total_customers'] ?? 0,
            'avg_rating' => 4.5, // Default placeholder
            
            // Nested structures
            'appointments' => $appointments,
            'revenue_details' => $revenue,
            'customers' => $customers,
            'staff' => $staff,
            'locations' => $locations,
            
            // Chart data placeholders
            'chart_labels' => [],
            'chart_appointments' => [],
            'chart_revenue' => [],
            
            // Additional placeholders
            'top_services' => [],
            'location_performance' => [],
            'daily_breakdown' => [],
        ];
    }

    /**
     * Get appointment analytics
     */
    private function getAppointmentAnalytics(Company $company, Carbon $startDate, Carbon $endDate)
    {
        $analytics = Analytics::where('company_id', $company->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('metric_type', 'appointments')
            ->get()
            ->groupBy('metric_name');

        // If no analytics data exists, calculate from actual appointments
        if ($analytics->isEmpty()) {
            $appointments = $company->appointments()
                ->whereBetween('appointment_date', [$startDate, $endDate])
                ->get();
                
            $total = $appointments->count();
            $completed = $appointments->where('status', 'completed')->count();
            $cancelled = $appointments->where('status', 'cancelled')->count();
            
            return [
                'total' => $total,
                'completed' => $completed,
                'cancelled' => $cancelled,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
                'cancellation_rate' => $total > 0 ? round(($cancelled / $total) * 100, 1) : 0,
                'trend' => collect(),
            ];
        }

        return [
            'total' => $analytics->get('total_appointments')?->sum('value') ?? 0,
            'completed' => $analytics->get('completed_appointments')?->sum('value') ?? 0,
            'cancelled' => $analytics->get('cancelled_appointments')?->sum('value') ?? 0,
            'completion_rate' => $analytics->get('completion_rate')?->avg('value') ?? 0,
            'cancellation_rate' => $analytics->get('cancellation_rate')?->avg('value') ?? 0,
            'trend' => $analytics->get('total_appointments')?->pluck('value', 'date') ?? collect(),
        ];
    }

    /**
     * Get revenue analytics
     */
    private function getRevenueAnalytics(Company $company, Carbon $startDate, Carbon $endDate)
    {
        $analytics = Analytics::where('company_id', $company->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('metric_type', 'revenue')
            ->get()
            ->groupBy('metric_name');

        // If no analytics data exists, calculate from actual appointments
        if ($analytics->isEmpty()) {
            $completedAppointments = $company->appointments()
                ->with('service')
                ->whereBetween('appointment_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->get();
                
            $totalRevenue = $completedAppointments->sum(function($appointment) {
                return floatval($appointment->service->price ?? 0);
            });
            
            $averagePerAppointment = $completedAppointments->count() > 0 
                ? $totalRevenue / $completedAppointments->count() 
                : 0;
            
            return [
                'total' => $totalRevenue,
                'average_per_appointment' => $averagePerAppointment,
                'trend' => collect(),
            ];
        }

        return [
            'total' => $analytics->get('daily_revenue')?->sum('value') ?? 0,
            'average_per_appointment' => $analytics->get('avg_revenue_per_appointment')?->avg('value') ?? 0,
            'trend' => $analytics->get('daily_revenue')?->pluck('value', 'date') ?? collect(),
        ];
    }

    /**
     * Get customer analytics
     */
    private function getCustomerAnalytics(Company $company, Carbon $startDate, Carbon $endDate)
    {
        $analytics = Analytics::where('company_id', $company->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('metric_type', 'customers')
            ->get()
            ->groupBy('metric_name');

        // If no analytics data exists, calculate from actual customers
        if ($analytics->isEmpty()) {
            $newCustomers = $company->customers()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $totalCustomers = $company->customers()->count();
            
            $returningCustomers = $company->customers()
                ->whereHas('appointments', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('appointment_date', [$startDate, $endDate]);
                })
                ->whereHas('appointments', function($query) use ($startDate) {
                    $query->where('appointment_date', '<', $startDate);
                })
                ->count();
            
            return [
                'new_customers' => $newCustomers,
                'returning_customers' => $returningCustomers,
                'total_customers' => $totalCustomers,
            ];
        }

        return [
            'new_customers' => $analytics->get('new_customers')?->sum('value') ?? 0,
            'returning_customers' => $analytics->get('returning_customers')?->sum('value') ?? 0,
            'total_customers' => $analytics->get('total_customers')?->last()?->value ?? 0,
        ];
    }

    /**
     * Get staff analytics
     */
    private function getStaffAnalytics(Company $company, Carbon $startDate, Carbon $endDate)
    {
        $analytics = Analytics::where('company_id', $company->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('metric_type', 'staff_performance')
            ->get()
            ->groupBy(['metric_name']);

        $staffPerformance = [];
        
        // If no analytics data exists, calculate from actual appointments
        if ($analytics->isEmpty()) {
            $users = $company->users()->get();
            
            foreach ($users as $user) {
                $appointments = $company->appointments()
                    ->where('assigned_user_id', $user->id)
                    ->whereBetween('appointment_date', [$startDate, $endDate])
                    ->with('service')
                    ->get();
                    
                $revenue = $appointments->where('status', 'completed')->sum(function($appointment) {
                    return floatval($appointment->service->price ?? 0);
                });
                
                $staffPerformance[$user->id] = [
                    'name' => $user->name,
                    'appointments' => $appointments->count(),
                    'revenue' => $revenue,
                ];
            }
            
            return $staffPerformance;
        }
        
        foreach ($analytics as $metricName => $metrics) {
            foreach ($metrics as $metric) {
                // Ensure metadata is an array before accessing it
                $metadata = is_array($metric->metadata) ? $metric->metadata : [];
                
                $staffId = $metadata['staff_id'] ?? null;
                $staffName = $metadata['staff_name'] ?? 'Unknown';
                
                if ($staffId) {
                    if (!isset($staffPerformance[$staffId])) {
                        $staffPerformance[$staffId] = [
                            'name' => $staffName,
                            'appointments' => 0,
                            'revenue' => 0,
                        ];
                    }
                    
                    if ($metricName === 'appointments_handled') {
                        $staffPerformance[$staffId]['appointments'] += $metric->value;
                    } elseif ($metricName === 'revenue_generated') {
                        $staffPerformance[$staffId]['revenue'] += $metric->value;
                    }
                }
            }
        }

        return $staffPerformance;
    }

    /**
     * Get location analytics
     */
    private function getLocationAnalytics(Company $company, Carbon $startDate, Carbon $endDate)
    {
        $analytics = Analytics::where('company_id', $company->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('metric_type', 'location_performance')
            ->get()
            ->groupBy(['metric_name']);

        $locationPerformance = [];
        
        // If no analytics data exists, calculate from actual appointments
        if ($analytics->isEmpty()) {
            $locations = $company->locations()->get();
            
            foreach ($locations as $location) {
                $appointments = $company->appointments()
                    ->where('location_id', $location->id)
                    ->whereBetween('appointment_date', [$startDate, $endDate])
                    ->with('service')
                    ->get();
                    
                $revenue = $appointments->where('status', 'completed')->sum(function($appointment) {
                    return floatval($appointment->service->price ?? 0);
                });
                
                $locationPerformance[$location->id] = [
                    'name' => $location->name,
                    'appointments' => $appointments->count(),
                    'revenue' => $revenue,
                ];
            }
            
            return $locationPerformance;
        }
        
        foreach ($analytics as $metricName => $metrics) {
            foreach ($metrics as $metric) {
                // Ensure metadata is an array before accessing it
                $metadata = is_array($metric->metadata) ? $metric->metadata : [];
                
                $locationId = $metadata['location_id'] ?? null;
                $locationName = $metadata['location_name'] ?? 'Unknown';
                
                if ($locationId) {
                    if (!isset($locationPerformance[$locationId])) {
                        $locationPerformance[$locationId] = [
                            'name' => $locationName,
                            'appointments' => 0,
                            'revenue' => 0,
                        ];
                    }
                    
                    if ($metricName === 'appointments') {
                        $locationPerformance[$locationId]['appointments'] += $metric->value;
                    } elseif ($metricName === 'revenue') {
                        $locationPerformance[$locationId]['revenue'] += $metric->value;
                    }
                }
            }
        }

        return $locationPerformance;
    }
}
