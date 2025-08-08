<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CalendarIntegrationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [Auth::class, 'showLoginForm'])->name('login.show');
Route::post('/login', [Auth::class, 'login'])->name('login');

Route::get('/signup', [Auth::class, 'showRegistrationForm'])->name('signup.show');
Route::post('/signup', [Auth::class, 'register'])->name('signup');

// Public booking routes
Route::get('/book/{company}/{slug?}', [BookingController::class, 'showBookingPage'])->name('booking.page');
Route::get('/book/{company}/service/{service}', [BookingController::class, 'showServiceBooking'])->name('booking.service');
Route::post('/book/{company}', [BookingController::class, 'storeBooking'])->name('booking.store');
Route::get('/booking-confirmation/{appointment}', [BookingController::class, 'showConfirmation'])->name('booking.confirmation');

// Protected routes
Route::middleware('auth')->group(function () {
    // Enhanced Dashboard routes with enterprise features
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/dashboard/staff', [DashboardController::class, 'staff'])->name('dashboard.staff');
    Route::post('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');
    Route::get('/dashboard/widget/{widget}', [DashboardController::class, 'getWidgetData'])->name('dashboard.widget');
    
    Route::post('/logout', [Auth::class, 'logout'])->name('logout');
    Route::get('/logout', function() {
        // Auto-logout for GET requests (browser navigation)
        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout.confirm');
    
    // Admin only - User management
    Route::post('/add-user', [Auth::class, 'addUser'])->name('add.user');
    
    // Admin only - Team management
    Route::get('/team', [TeamController::class, 'index'])->name('team.index');
    Route::delete('/team/{user}', [TeamController::class, 'destroy'])->name('team.destroy');
    
    // Admin only - Service management
    Route::resource('services', ServiceController::class);
    
    // Staff and Admin - Customer management
    Route::resource('customers', CustomerController::class);
    Route::patch('/customers/{customer}/reactivate', [CustomerController::class, 'reactivate'])->name('customers.reactivate');
    
    // Staff and Admin - Appointment management
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/my', [AppointmentController::class, 'myAppointments'])->name('appointments.my');
    Route::get('/appointments/today', [AppointmentController::class, 'today'])->name('appointments.today');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::patch('/appointments/{appointment}/assign', [AppointmentController::class, 'assign'])->name('appointments.assign');
    
    // Notification routes
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Multi-location Support Routes
    Route::resource('locations', LocationController::class);
    Route::get('/locations/{location}/availability', [LocationController::class, 'getAvailability'])->name('locations.availability');
    Route::post('/locations/find-nearest', [LocationController::class, 'findNearest'])->name('locations.find-nearest');
    Route::post('/locations/{location}/transfer', [LocationController::class, 'transferAppointments'])->name('locations.transfer');
    
    // Task Management System
    Route::resource('tasks', TaskController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::get('/tasks/dashboard/data', [TaskController::class, 'getDashboardData'])->name('tasks.dashboard-data');
    
    // Calendar Integration Routes
    Route::resource('calendar', CalendarIntegrationController::class);
    Route::get('/calendar/{calendar}/authorize', [CalendarIntegrationController::class, 'authorize'])->name('calendar.authorize');
    Route::get('/calendar/{calendar}/callback', [CalendarIntegrationController::class, 'callback'])->name('calendar.callback');
    Route::post('/calendar/{calendar}/sync', [CalendarIntegrationController::class, 'sync'])->name('calendar.sync');
    Route::post('/calendar/{calendar}/test', [CalendarIntegrationController::class, 'test'])->name('calendar.test');
    Route::get('/calendar/{calendar}/status', [CalendarIntegrationController::class, 'syncStatus'])->name('calendar.status');
    
    // Advanced Analytics Routes
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/chart-data', [AnalyticsController::class, 'getChartData'])->name('analytics.chart-data');
    Route::get('/analytics/report', [AnalyticsController::class, 'generateReport'])->name('analytics.report');
    Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    Route::get('/analytics/{metricType}/{metricName}', [AnalyticsController::class, 'showMetric'])->name('analytics.metric');
});

// Super Admin routes
Route::middleware(['auth'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    
    // Company creation routes (must be before dynamic routes)
    Route::get('/company/create', [SuperAdminController::class, 'createCompany'])->name('company.create');
    Route::post('/company/create', [SuperAdminController::class, 'storeCompany'])->name('company.store');
    
    // Dynamic company routes
    Route::get('/company/{company}', [SuperAdminController::class, 'showCompany'])->name('company.show');
    Route::patch('/company/{company}/block', [SuperAdminController::class, 'blockCompany'])->name('company.block');
    Route::patch('/company/{company}/unblock', [SuperAdminController::class, 'unblockCompany'])->name('company.unblock');
    Route::patch('/company/{company}/subscription', [SuperAdminController::class, 'updateSubscription'])->name('company.subscription.update');
    Route::delete('/company/{company}', [SuperAdminController::class, 'deleteCompany'])->name('company.delete');
});

// Employee routes for subscription checking
Route::middleware(['auth'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::get('/company/{company}', [EmployeeController::class, 'viewCompany'])->name('company.view');
    Route::patch('/company/{company}/mark-payment', [EmployeeController::class, 'markPaymentReceived'])->name('company.mark-payment');
});
