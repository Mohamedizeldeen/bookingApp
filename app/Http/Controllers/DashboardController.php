<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\AnalyticsService;
use App\Services\LocationService;
use App\Services\CalendarSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $analyticsService;
    protected $locationService;
    protected $calendarSyncService;
    protected $realTimeAnalytics;

    public function __construct(
        AnalyticsService $analyticsService,
        LocationService $locationService,
        CalendarSyncService $calendarSyncService
    ) {
        $this->analyticsService = $analyticsService;
        $this->locationService = $locationService;
        $this->calendarSyncService = $calendarSyncService;
        $this->realTimeAnalytics = app(\App\Services\RealTimeAnalyticsService::class);
    }

    /**
     * Display the main dashboard with all enterprise features
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Create a default company if user doesn't have one
        if (!$company) {
            $company = new Company([
                'user_id' => $user->id,
                'name' => $user->name . "'s Company",
                'email' => $user->email,
                'phone' => '',
                'address' => '',
                'website' => '',
                'subscription_plan' => 'enterprise',
                'subscription_status' => 'active',
                'subscription_expires_at' => now()->addYear(),
                'settings' => json_encode([]),
            ]);
            $company->save();
            
            // Update user's company_id using raw update
            DB::table('users')->where('id', $user->id)->update(['company_id' => $company->id]);
            
            // Re-authenticate the user to get fresh data
            Auth::logout();
            Auth::loginUsingId($user->id);
            $user = Auth::user();
            $company = $user->company;
        }
        
        // Get analytics data with real-time enhancements
        $days = $request->get('days', 30);
        $analytics = $this->analyticsService->getDashboardAnalytics($company, $days);
        
        // Get real-time metrics and performance alerts
        $realTimeMetrics = $this->realTimeAnalytics->getRealTimeMetrics($company);
        $performanceAlerts = $this->realTimeAnalytics->getPerformanceAlerts($company);
        
        // Extract specific metrics from real-time data
        $liveMetrics = $realTimeMetrics['live_appointments'] ?? [];
        $hourlyTrends = $realTimeMetrics['hourly_trends'] ?? [];
        $locationActivity = $realTimeMetrics['location_activity'] ?? [];
        $staffMetrics = $realTimeMetrics['staff_monitoring'] ?? [];
        
        // Get enhanced location data with GPS intelligence
        $locations = $this->locationService->getCompanyLocationsWithAvailability($company);
        $locationAnalytics = [];
        $locationRecommendations = $this->locationService->getLocationRecommendations($company);
        
        foreach ($locations as $location) {
            $locationAnalytics[$location->id] = [
                'analytics' => $this->locationService->getLocationAnalytics($location, $days),
                'capacity' => $this->locationService->getLocationCapacity($location, now()),
                'utilization' => $this->locationService->getLocationUtilization($location, now()),
            ];
        }
        
        // Get enhanced calendar integrations with smart sync status
        $calendarIntegrations = $company->calendarIntegrations()->active()->get();
        $calendarSyncStatus = [];
        foreach ($calendarIntegrations as $integration) {
            $calendarSyncStatus[$integration->id] = [
                'needs_sync' => $integration->needsSync(),
                'can_sync' => $integration->canSync(),
                'needs_token_refresh' => $integration->needsTokenRefresh(),
                'last_sync' => $integration->last_sync_at ? $integration->last_sync_at->diffForHumans() : 'Never',
                'sync_status' => $integration->sync_status,
                'is_active' => $integration->is_active,
            ];
        }
        
        // Get recent appointments with location and sync info
        $recentAppointments = $company->appointments()
            ->with(['customer', 'service', 'location'])
            ->orderBy('appointment_date', 'desc')
            ->limit(10)
            ->get();
        
        // Get enhanced quick stats
        $quickStats = $this->getQuickStats($company);
        
        return view('dashboard.index', compact(
            'analytics',
            'realTimeMetrics',
            'liveMetrics',
            'performanceAlerts',
            'hourlyTrends',
            'locations',
            'locationAnalytics',
            'locationRecommendations',
            'locationActivity',
            'staffMetrics',
            'calendarIntegrations',
            'calendarSyncStatus',
            'recentAppointments',
            'quickStats',
            'company',
            'days'
        ));
    }

    /**
     * Admin dashboard with additional management features
     */
    public function admin(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Access denied.');
        }
        
        $company = $user->company;
        $days = $request->get('days', 30);
        
        // Get comprehensive analytics
        $analytics = $this->analyticsService->getDashboardAnalytics($company, $days);
        
        // Get all locations with detailed analytics
        $locations = $company->locations()->get();
        $locationPerformance = [];
        foreach ($locations as $location) {
            $locationPerformance[$location->id] = [
                'location' => $location,
                'analytics' => $this->locationService->getLocationAnalytics($location, $days),
                'capacity' => $this->locationService->getLocationCapacity($location, Carbon::today()),
            ];
        }
        
        // Get calendar integration health
        $calendarHealth = [];
        $calendarIntegrations = $company->calendarIntegrations()->get();
        foreach ($calendarIntegrations as $integration) {
            $calendarHealth[$integration->id] = [
                'integration' => $integration,
                'needs_sync' => $integration->needsSync(),
                'last_sync' => $integration->last_sync_at,
                'sync_errors' => $integration->syncLogs()->failed()->count(),
            ];
        }
        
        // Get staff performance
        $staffPerformance = $analytics['staff'];
        
        // Get system health metrics
        $systemHealth = $this->getSystemHealthMetrics($company);
        
        return view('dashboard.admin', compact(
            'analytics',
            'locationPerformance',
            'calendarHealth',
            'staffPerformance',
            'systemHealth',
            'company',
            'days'
        ));
    }

    /**
     * Staff dashboard with focused features
     */
    public function staff(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get today's schedule
        $todayAppointments = $company->appointments()
            ->with(['customer', 'service', 'location'])
            ->whereDate('appointment_date', Carbon::today())
            ->where(function($query) use ($user) {
                // Show all if admin, or just assigned appointments for staff
                if ($user->role === 'admin') {
                    return $query;
                } else {
                    return $query->where('assigned_user_id', $user->id);
                }
            })
            ->orderBy('appointment_date')
            ->get();
        
        // Get upcoming appointments
        $upcomingAppointments = $company->appointments()
            ->with(['customer', 'service', 'location'])
            ->where('appointment_date', '>', Carbon::today())
            ->where(function($query) use ($user) {
                if ($user->role === 'admin') {
                    return $query;
                } else {
                    return $query->where('assigned_user_id', $user->id);
                }
            })
            ->orderBy('appointment_date')
            ->limit(20)
            ->get();
        
        // Get staff-specific analytics
        $staffAnalytics = [];
        if ($user->role !== 'admin') {
            $staffAnalytics = $this->getStaffSpecificAnalytics($user, 30);
        }
        
        // Get available locations
        $locations = $this->locationService->getCompanyLocationsWithAvailability($company, Carbon::today());
        
        // Get calendar sync status
        $calendarIntegrations = $company->calendarIntegrations()->active()->get();
        
        return view('dashboard.staff', compact(
            'todayAppointments',
            'upcomingAppointments',
            'staffAnalytics',
            'locations',
            'calendarIntegrations',
            'company'
        ));
    }

    /**
     * Get quick stats for dashboard
     */
    private function getQuickStats($company)
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'today_appointments' => $company->appointments()
                ->whereDate('appointment_date', $today)
                ->count(),
            'today_revenue' => $company->appointments()
                ->whereDate('appointment_date', $today)
                ->where('status', 'completed')
                ->with('service')
                ->get()
                ->sum(function($appointment) {
                    return floatval($appointment->service->price ?? 0);
                }),
            'month_appointments' => $company->appointments()
                ->where('appointment_date', '>=', $thisMonth)
                ->count(),
            'month_revenue' => $company->appointments()
                ->where('appointment_date', '>=', $thisMonth)
                ->where('status', 'completed')
                ->with('service')
                ->get()
                ->sum(function($appointment) {
                    return floatval($appointment->service->price ?? 0);
                }),
            'total_customers' => $company->customers()->count(),
            'active_locations' => $company->locations()->active()->count(),
            'calendar_integrations' => $company->calendarIntegrations()->active()->count(),
        ];
    }

    /**
     * Get staff-specific analytics
     */
    private function getStaffSpecificAnalytics($user, $days)
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);
        
        $appointments = $user->company->appointments()
            ->where('assigned_user_id', $user->id)
            ->whereBetween('appointment_date', [$startDate, $endDate]);
        
        return [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('status', 'completed')->count(),
            'revenue_generated' => $appointments->where('status', 'completed')
                ->with('service')
                ->get()
                ->sum(function($appointment) {
                    return floatval($appointment->service->price ?? 0);
                }),
            'completion_rate' => $appointments->count() > 0 
                ? round(($appointments->where('status', 'completed')->count() / $appointments->count()) * 100, 1)
                : 0,
        ];
    }

    /**
     * Get system health metrics
     */
    private function getSystemHealthMetrics($company)
    {
        return [
            'database_health' => [
                'total_appointments' => $company->appointments()->count(),
                'total_customers' => $company->customers()->count(),
                'total_services' => $company->services()->count(),
                'analytics_records' => $company->analytics()->count(),
            ],
            'calendar_sync_health' => [
                'active_integrations' => $company->calendarIntegrations()->active()->count(),
                'integrations_needing_sync' => $company->calendarIntegrations()->active()->needsSync()->count(),
                'synced_appointments' => $company->appointments()->whereNotNull('calendar_event_id')->count(),
            ],
            'location_health' => [
                'total_locations' => $company->locations()->count(),
                'active_locations' => $company->locations()->active()->count(),
                'locations_with_gps' => $company->locations()->whereNotNull('latitude')->whereNotNull('longitude')->count(),
            ],
        ];
    }

    /**
     * Refresh dashboard data (AJAX)
     */
    public function refresh(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Generate fresh analytics for today
        $this->analyticsService->generateCompanyAnalytics($company, Carbon::today());
        
        // Sync pending appointments
        $this->calendarSyncService->syncPendingAppointments();
        
        return response()->json([
            'success' => true,
            'message' => 'Dashboard data refreshed successfully.',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get widget data (AJAX)
     */
    public function getWidgetData(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        $widget = $request->get('widget');
        
        switch ($widget) {
            case 'quick-stats':
                return response()->json([
                    'success' => true,
                    'data' => $this->getQuickStats($company),
                ]);
                
            case 'recent-appointments':
                $appointments = $company->appointments()
                    ->with(['customer', 'service', 'location'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                    
                return response()->json([
                    'success' => true,
                    'data' => $appointments,
                ]);
                
            case 'location-status':
                $locations = $this->locationService->getCompanyLocationsWithAvailability($company);
                return response()->json([
                    'success' => true,
                    'data' => $locations,
                ]);
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid widget type',
                ], 400);
        }
    }
}
