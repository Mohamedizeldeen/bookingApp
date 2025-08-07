<?php

namespace App\Http\Controllers;

use App\Models\CalendarIntegration;
use App\Services\CalendarSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CalendarIntegrationController extends Controller
{
    protected $calendarSyncService;

    public function __construct(CalendarSyncService $calendarSyncService)
    {
        $this->calendarSyncService = $calendarSyncService;
    }

    /**
     * Display calendar integrations
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;
        
        $integrations = $company->calendarIntegrations()->get();

        return view('calendar.index', compact('integrations', 'company'));
    }

    /**
     * Show calendar integration creation form
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can create calendar integrations.');
        }

        $availableProviders = [
            'google' => 'Google Calendar',
            'outlook' => 'Microsoft Outlook',
            'apple' => 'Apple iCloud Calendar',
            'caldav' => 'CalDAV (Generic)',
        ];

        return view('calendar.create', compact('availableProviders'));
    }

    /**
     * Store new calendar integration
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can create calendar integrations.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'provider' => 'required|in:google,outlook,apple,caldav',
            'calendar_id' => 'required|string|max:500',
            'sync_direction' => 'required|in:bidirectional,to_external,from_external',
            'auto_sync' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $integration = new CalendarIntegration();
            $integration->company_id = $user->company_id;
            $integration->user_id = $user->id;
            $integration->name = $request->name;
            $integration->provider = $request->provider;
            $integration->calendar_id = $request->calendar_id;
            $integration->sync_direction = $request->sync_direction;
            $integration->auto_sync = $request->boolean('auto_sync', true);
            $integration->is_active = $request->boolean('is_active', true);
            $integration->settings = $request->get('settings', []);
            $integration->save();
            
            return redirect()->route('calendar.index')
                ->with('success', 'Calendar integration created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create integration: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show calendar integration details
     */
    public function show(CalendarIntegration $calendar)
    {
        $user = Auth::user();
        
        if ($calendar->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $syncHistory = $calendar->syncLogs()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('calendar.show', compact('calendar', 'syncHistory'));
    }

    /**
     * Show calendar integration edit form
     */
    public function edit(CalendarIntegration $calendar)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $calendar->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $availableProviders = [
            'google' => 'Google Calendar',
            'outlook' => 'Microsoft Outlook',
            'apple' => 'Apple iCloud Calendar',
            'caldav' => 'CalDAV (Generic)',
        ];

        return view('calendar.edit', compact('calendar', 'availableProviders'));
    }

    /**
     * Update calendar integration
     */
    public function update(Request $request, CalendarIntegration $calendar)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $calendar->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'calendar_id' => 'required|string|max:500',
            'sync_direction' => 'required|in:bidirectional,to_external,from_external',
            'auto_sync' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $calendar->update([
                'name' => $request->name,
                'calendar_id' => $request->calendar_id,
                'sync_direction' => $request->sync_direction,
                'auto_sync' => $request->boolean('auto_sync'),
                'is_active' => $request->boolean('is_active'),
                'settings' => $request->get('settings', $calendar->settings),
            ]);
            
            return redirect()->route('calendar.show', $calendar)
                ->with('success', 'Calendar integration updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update integration: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete calendar integration
     */
    public function destroy(CalendarIntegration $calendar)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $calendar->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        try {
            $calendar->delete();
            
            return redirect()->route('calendar.index')
                ->with('success', 'Calendar integration deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete integration: ' . $e->getMessage()]);
        }
    }

    /**
     * Initiate OAuth authorization for calendar provider
     */
    public function authorize(Request $request, CalendarIntegration $calendar)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' || $calendar->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        try {
            $authUrl = $this->calendarSyncService->getAuthorizationUrl($calendar);
            
            if ($authUrl) {
                return redirect($authUrl);
            } else {
                return redirect()->back()
                    ->withErrors(['error' => 'OAuth not supported for this provider.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to initiate authorization: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle OAuth callback
     */
    public function callback(Request $request, CalendarIntegration $calendar)
    {
        $user = Auth::user();
        
        if ($calendar->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        try {
            $success = $this->calendarSyncService->handleOAuthCallback($calendar, $request->all());
            
            if ($success) {
                return redirect()->route('calendar.show', $calendar)
                    ->with('success', 'Calendar authorization completed successfully.');
            } else {
                return redirect()->route('calendar.show', $calendar)
                    ->withErrors(['error' => 'Calendar authorization failed.']);
            }
        } catch (\Exception $e) {
            return redirect()->route('calendar.show', $calendar)
                ->withErrors(['error' => 'Authorization error: ' . $e->getMessage()]);
        }
    }

    /**
     * Manually sync calendar
     */
    public function sync(CalendarIntegration $calendar)
    {
        $user = Auth::user();
        
        if ($calendar->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        try {
            $result = $this->calendarSyncService->syncCalendar($calendar);
            
            return redirect()->back()
                ->with('success', "Sync completed. Processed {$result['synced_count']} appointments.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Sync failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Test calendar connection
     */
    public function test(CalendarIntegration $calendar)
    {
        $user = Auth::user();
        
        if ($calendar->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        try {
            $isConnected = $this->calendarSyncService->testConnection($calendar);
            
            if ($isConnected) {
                return response()->json([
                    'success' => true,
                    'message' => 'Calendar connection is working properly.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Calendar connection failed.',
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sync status (AJAX)
     */
    public function syncStatus(CalendarIntegration $calendar)
    {
        $user = Auth::user();
        
        if ($calendar->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $lastSync = $calendar->last_sync_at;
        $needsSync = $calendar->needsSync();
        $isActive = $calendar->is_active;

        return response()->json([
            'success' => true,
            'last_sync' => $lastSync ? $lastSync->diffForHumans() : 'Never',
            'needs_sync' => $needsSync,
            'is_active' => $isActive,
            'status' => $isActive ? ($needsSync ? 'Needs Sync' : 'Up to Date') : 'Inactive',
        ]);
    }
}
