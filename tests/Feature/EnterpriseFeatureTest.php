<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Location;
use App\Models\CalendarIntegration;
use App\Services\RealTimeAnalyticsService;
use App\Services\LocationService;
use App\Services\CalendarSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnterpriseFeatureTest extends TestCase
{
    use RefreshDatabase;

    private $company;
    private $adminUser;
    private $staffUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test company
        $this->company = Company::factory()->create([
            'name' => 'Test Enterprise Company',
            'subscription_plan' => 'enterprise',
            'subscription_status' => 'active',
        ]);

        // Create admin user
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'company_id' => $this->company->id,
        ]);

        // Create staff user
        $this->staffUser = User::factory()->create([
            'role' => 'staff',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function test_real_time_analytics_service_works()
    {
        $realTimeAnalytics = app(RealTimeAnalyticsService::class);
        
        $metrics = $realTimeAnalytics->getRealTimeMetrics($this->company);
        
        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('live_appointments', $metrics);
        $this->assertArrayHasKey('hourly_trends', $metrics);
        $this->assertArrayHasKey('location_activity', $metrics);
        $this->assertArrayHasKey('staff_monitoring', $metrics);
    }

    /** @test */
    public function test_location_service_gps_functionality()
    {
        $locationService = app(LocationService::class);
        
        // Create test locations with GPS coordinates
        $location1 = Location::factory()->create([
            'company_id' => $this->company->id,
            'latitude' => 40.7128,  // New York
            'longitude' => -74.0060,
        ]);
        
        $location2 = Location::factory()->create([
            'company_id' => $this->company->id,
            'latitude' => 40.7580,  // Times Square
            'longitude' => -73.9855,
        ]);

        // Test GPS distance calculation
        $distance = $locationService->calculateDistance(
            40.7128, -74.0060, // New York
            40.7580, -73.9855  // Times Square
        );
        
        $this->assertIsFloat($distance);
        $this->assertGreaterThan(0, $distance);
        
        // Test nearby locations
        $nearbyLocations = $locationService->getNearbyLocations(40.7128, -74.0060, 10);
        $this->assertGreaterThan(0, $nearbyLocations->count());
        
        // Test location recommendations
        $recommendations = $locationService->getLocationRecommendations($this->company);
        $this->assertIsArray($recommendations);
    }

    /** @test */
    public function test_calendar_integration_advanced_features()
    {
        $calendarIntegration = CalendarIntegration::factory()->create([
            'company_id' => $this->company->id,
            'provider' => 'google',
            'is_active' => true,
            'access_token' => 'test_token',
            'refresh_token' => 'test_refresh',
            'token_expires_at' => now()->addHour(),
        ]);

        // Test enhanced calendar methods
        $this->assertTrue($calendarIntegration->canSync());
        $this->assertFalse($calendarIntegration->needsTokenRefresh());
        $this->assertEquals('active', $calendarIntegration->sync_status);
    }

    /** @test */
    public function test_dashboard_loads_with_enterprise_features()
    {
        $this->actingAs($this->adminUser)
             ->get('/dashboard')
             ->assertStatus(200)
             ->assertViewHas([
                 'analytics',
                 'realTimeMetrics',
                 'locationAnalytics',
                 'calendarIntegrations',
                 'performanceAlerts'
             ]);
    }

    /** @test */
    public function test_location_analytics_and_capacity()
    {
        $location = Location::factory()->create([
            'company_id' => $this->company->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $locationService = app(LocationService::class);
        
        $analytics = $locationService->getLocationAnalytics($location);
        $capacity = $locationService->getLocationCapacity($location, now());
        $utilization = $locationService->getLocationUtilization($location, now());

        $this->assertIsArray($analytics);
        $this->assertArrayHasKey('total_appointments', $analytics);
        $this->assertArrayHasKey('utilization_rate', $analytics);
        
        $this->assertIsArray($capacity);
        $this->assertArrayHasKey('total_slots', $capacity);
        $this->assertArrayHasKey('available_slots', $capacity);
        
        $this->assertIsFloat($utilization);
    }

    /** @test */
    public function test_performance_alerts_generation()
    {
        $realTimeAnalytics = app(RealTimeAnalyticsService::class);
        
        $alerts = $realTimeAnalytics->getPerformanceAlerts($this->company);
        
        $this->assertIsArray($alerts);
        // Alerts might be empty for a new company, which is fine
    }

    /** @test */
    public function test_optimal_location_recommendation()
    {
        $locationService = app(LocationService::class);
        
        // Create multiple locations
        Location::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $optimalLocation = $locationService->getOptimalLocation(
            $this->company,
            40.7500, // Customer latitude
            -74.0000, // Customer longitude
            now()->addDay()
        );

        // Should return a location or null if none available
        $this->assertTrue($optimalLocation === null || $optimalLocation instanceof Location);
    }
}
