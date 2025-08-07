<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\GenerateAnalytics;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateAnalytics::class,
            ]);
        }

        // Schedule tasks
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            
            // Generate analytics daily at 1 AM
            $schedule->command('analytics:generate')
                ->dailyAt('01:00')
                ->withoutOverlapping()
                ->runInBackground();
            
            // Sync calendars every 30 minutes
            $schedule->call(function () {
                $calendarSync = app(\App\Services\CalendarSyncService::class);
                $calendarSync->syncPendingAppointments();
            })->everyThirtyMinutes()
              ->name('sync-calendars')
              ->withoutOverlapping();
        });
    }
}
