<?php

namespace App\Services;

use App\Models\Analytics;
use App\Models\Company;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RealTimeAnalyticsService
{
    /**
     * Get real-time dashboard metrics
     */
    public function getRealTimeMetrics(Company $company): array
    {
        $cacheKey = "realtime_metrics_{$company->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($company) {
            return [
                'live_appointments' => $this->getLiveAppointments($company),
                'today_performance' => $this->getTodayPerformance($company),
                'hourly_trends' => $this->getHourlyTrends($company),
                'location_activity' => $this->getLocationActivity($company),
                'calendar_sync_health' => $this->getCalendarSyncHealth($company),
                'staff_activity' => $this->getStaffActivity($company),
            ];
        });
    }

    /**
     * Get live appointments happening now
     */
    private function getLiveAppointments(Company $company): array
    {
        $now = now();
        $currentHour = $now->format('Y-m-d H:00:00');
        $nextHour = $now->copy()->addHour()->format('Y-m-d H:00:00');

        $liveAppointments = $company->appointments()
            ->with(['customer', 'service', 'location', 'user'])
            ->whereDate('appointment_date', today())
            ->where(function ($query) use ($currentHour, $nextHour) {
                $query->whereBetween('appointment_date', [$currentHour, $nextHour]);
            })
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->get();

        return [
            'count' => $liveAppointments->count(),
            'appointments' => $liveAppointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'customer_name' => $appointment->customer->name,
                    'service_name' => $appointment->service->name,
                    'location_name' => $appointment->location?->name,
                    'staff_name' => $appointment->user?->name,
                    'time' => $appointment->appointment_date->format('H:i'),
                    'status' => $appointment->status,
                    'duration' => $appointment->service->duration,
                ];
            }),
        ];
    }

    /**
     * Get today's performance metrics
     */
    private function getTodayPerformance(Company $company): array
    {
        $today = today();
        
        $appointments = $company->appointments()
            ->with('service')
            ->whereDate('appointment_date', $today)
            ->get();

        $completed = $appointments->where('status', 'completed');
        $revenue = $completed->sum(function ($appointment) {
            return floatval($appointment->service->price ?? 0);
        });

        $lastHour = $company->appointments()
            ->whereDate('appointment_date', $today)
            ->where('appointment_date', '>=', now()->subHour())
            ->where('status', 'completed')
            ->count();

        return [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $completed->count(),
            'revenue' => $revenue,
            'completion_rate' => $appointments->count() > 0 
                ? round(($completed->count() / $appointments->count()) * 100, 1) 
                : 0,
            'appointments_last_hour' => $lastHour,
            'average_service_time' => $completed->avg(function ($appointment) {
                return $appointment->service->duration ?? 0;
            }),
        ];
    }

    /**
     * Get hourly appointment trends for today
     */
    private function getHourlyTrends(Company $company): array
    {
        $hourlyData = DB::table('appointments')
            ->select(
                DB::raw('HOUR(appointment_date) as hour'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            )
            ->where('company_id', $company->id)
            ->whereDate('appointment_date', today())
            ->groupBy(DB::raw('HOUR(appointment_date)'))
            ->orderBy('hour')
            ->get();

        $trends = [];
        for ($hour = 8; $hour <= 18; $hour++) {
            $data = $hourlyData->firstWhere('hour', $hour);
            $trends[] = [
                'hour' => sprintf('%02d:00', $hour),
                'appointments' => $data->count ?? 0,
                'completed' => $data->completed ?? 0,
            ];
        }

        return $trends;
    }

    /**
     * Get location activity
     */
    private function getLocationActivity(Company $company): array
    {
        return $company->locations()
            ->with(['appointments' => function ($query) {
                $query->whereDate('appointment_date', today())
                      ->whereIn('status', ['confirmed', 'in_progress', 'completed']);
            }])
            ->get()
            ->map(function ($location) {
                $appointments = $location->appointments;
                $inProgress = $appointments->where('status', 'in_progress')->count();
                $upcoming = $appointments->where('status', 'confirmed')->count();
                $completed = $appointments->where('status', 'completed')->count();

                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'is_active' => $location->is_active,
                    'appointments_today' => $appointments->count(),
                    'in_progress' => $inProgress,
                    'upcoming' => $upcoming,
                    'completed' => $completed,
                    'utilization' => $this->calculateLocationUtilization($location),
                ];
            })
            ->toArray();
    }

    /**
     * Get calendar sync health
     */
    private function getCalendarSyncHealth(Company $company): array
    {
        $integrations = $company->calendarIntegrations()->get();
        
        $health = [
            'total_integrations' => $integrations->count(),
            'active_integrations' => $integrations->where('is_active', true)->count(),
            'needs_sync' => $integrations->filter(function ($integration) {
                return $integration->needsSync();
            })->count(),
            'token_issues' => $integrations->filter(function ($integration) {
                return $integration->isTokenExpired();
            })->count(),
            'last_sync_times' => $integrations->map(function ($integration) {
                return [
                    'provider' => $integration->provider,
                    'last_sync' => $integration->last_sync_at?->diffForHumans(),
                    'status' => $integration->sync_status,
                ];
            }),
        ];

        return $health;
    }

    /**
     * Get staff activity
     */
    private function getStaffActivity(Company $company): array
    {
        return $company->users()
            ->with(['appointments' => function ($query) {
                $query->whereDate('appointment_date', today())
                      ->with('service');
            }])
            ->get()
            ->map(function ($user) {
                $appointments = $user->appointments;
                $inProgress = $appointments->where('status', 'in_progress');
                $completed = $appointments->where('status', 'completed');
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'appointments_today' => $appointments->count(),
                    'in_progress' => $inProgress->count(),
                    'completed' => $completed->count(),
                    'revenue_today' => $completed->sum(function ($appointment) {
                        return floatval($appointment->service->price ?? 0);
                    }),
                    'current_status' => $this->getUserCurrentStatus($user),
                ];
            })
            ->toArray();
    }

    /**
     * Calculate location utilization percentage
     */
    private function calculateLocationUtilization($location): float
    {
        // Assuming 10 hours of operation (8 AM to 6 PM) and 60-minute average appointment
        $maxCapacity = 10; // 10 appointments per day max
        $todayAppointments = $location->appointments->count();
        
        return $todayAppointments > 0 ? round(($todayAppointments / $maxCapacity) * 100, 1) : 0;
    }

    /**
     * Get user's current status
     */
    private function getUserCurrentStatus($user): string
    {
        $now = now();
        
        $currentAppointment = $user->appointments()
            ->whereDate('appointment_date', today())
            ->where('appointment_date', '<=', $now)
            ->where('status', 'in_progress')
            ->first();

        if ($currentAppointment) {
            return 'busy';
        }

        $nextAppointment = $user->appointments()
            ->whereDate('appointment_date', today())
            ->where('appointment_date', '>', $now)
            ->where('status', 'confirmed')
            ->orderBy('appointment_date')
            ->first();

        if ($nextAppointment) {
            $timeUntilNext = $nextAppointment->appointment_date->diffInMinutes($now);
            if ($timeUntilNext <= 30) {
                return 'preparing';
            }
        }

        return 'available';
    }

    /**
     * Track real-time event
     */
    public function trackEvent(Company $company, string $event, array $data = []): void
    {
        Analytics::create([
            'company_id' => $company->id,
            'date' => today(),
            'metric_type' => 'real_time_event',
            'metric_name' => $event,
            'value' => 1,
            'metadata' => array_merge($data, [
                'timestamp' => now()->toISOString(),
                'user_id' => Auth::user()?->id,
            ]),
        ]);

        // Clear cache to refresh real-time metrics
        Cache::forget("realtime_metrics_{$company->id}");
    }

    /**
     * Get performance alerts
     */
    public function getPerformanceAlerts(Company $company): array
    {
        $alerts = [];
        $metrics = $this->getRealTimeMetrics($company);

        // Check for low completion rate
        if ($metrics['today_performance']['completion_rate'] < 80) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Today\'s completion rate is below 80%',
                'value' => $metrics['today_performance']['completion_rate'] . '%',
                'priority' => 'medium',
            ];
        }

        // Check for calendar sync issues
        if ($metrics['calendar_sync_health']['token_issues'] > 0) {
            $alerts[] = [
                'type' => 'error',
                'message' => 'Calendar integrations have token issues',
                'value' => $metrics['calendar_sync_health']['token_issues'] . ' affected',
                'priority' => 'high',
            ];
        }

        // Check for overbooked locations
        foreach ($metrics['location_activity'] as $location) {
            if ($location['utilization'] > 90) {
                $alerts[] = [
                    'type' => 'info',
                    'message' => "Location '{$location['name']}' is near capacity",
                    'value' => $location['utilization'] . '% utilized',
                    'priority' => 'low',
                ];
            }
        }

        return $alerts;
    }
}
