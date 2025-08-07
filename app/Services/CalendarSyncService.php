<?php

namespace App\Services;

use App\Models\CalendarIntegration;
use App\Models\Appointment;
use App\Models\Analytics;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CalendarSyncService
{
    /**
     * Sync an appointment to external calendar
     */
    public function syncAppointmentToCalendar(Appointment $appointment, CalendarIntegration $integration)
    {
        if (!$integration->is_active || !$integration->canSync()) {
            return false;
        }

        // Refresh token if needed
        if (!$this->refreshTokenIfNeeded($integration)) {
            return false;
        }

        switch ($integration->provider) {
            case 'google':
                return $this->syncToGoogleCalendar($appointment, $integration);
            case 'outlook':
                return $this->syncToOutlookCalendar($appointment, $integration);
            default:
                return false;
        }
    }

    /**
     * Sync appointment to Google Calendar
     */
    private function syncToGoogleCalendar(Appointment $appointment, CalendarIntegration $integration)
    {
        $eventData = [
            'summary' => $appointment->service->name . ' - ' . $appointment->customer->name,
            'description' => $this->generateEventDescription($appointment),
            'start' => [
                'dateTime' => $appointment->appointment_date->format('c'),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'end' => [
                'dateTime' => $appointment->appointment_date->copy()
                    ->addMinutes($appointment->service->duration ?? 60)->format('c'),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'location' => $appointment->location ? $appointment->location->full_address : '',
            'attendees' => [
                [
                    'email' => $appointment->customer->email,
                    'displayName' => $appointment->customer->name,
                ],
            ],
        ];

        $response = Http::withToken($integration->access_token)
            ->post("https://www.googleapis.com/calendar/v3/calendars/{$integration->calendar_id}/events", $eventData);

        if ($response->successful()) {
            $responseData = $response->json();
            $appointment->calendar_event_id = $responseData['id'];
            $appointment->last_synced_at = now();
            $appointment->save();

            return [
                'success' => true,
                'event_id' => $responseData['id'],
            ];
        }

        return [
            'success' => false,
            'error' => $response->body(),
        ];
    }

    /**
     * Sync appointment to Outlook Calendar
     */
    private function syncToOutlookCalendar(Appointment $appointment, CalendarIntegration $integration)
    {
        $eventData = [
            'subject' => $appointment->service->name . ' - ' . $appointment->customer->name,
            'body' => [
                'contentType' => 'text',
                'content' => $this->generateEventDescription($appointment),
            ],
            'start' => [
                'dateTime' => $appointment->appointment_date->toIso8601String(),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'end' => [
                'dateTime' => $appointment->appointment_date->copy()
                    ->addMinutes($appointment->service->duration ?? 60)->toIso8601String(),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'location' => [
                'displayName' => $appointment->location ? $appointment->location->full_address : '',
            ],
            'attendees' => [
                [
                    'emailAddress' => [
                        'address' => $appointment->customer->email,
                        'name' => $appointment->customer->name,
                    ],
                ],
            ],
        ];

        $response = Http::withToken($integration->access_token)
            ->post("https://graph.microsoft.com/v1.0/me/calendars/{$integration->calendar_id}/events", $eventData);

        if ($response->successful()) {
            $responseData = $response->json();
            $appointment->calendar_event_id = $responseData['id'];
            $appointment->last_synced_at = now();
            $appointment->save();

            return [
                'success' => true,
                'event_id' => $responseData['id'],
            ];
        }

        return [
            'success' => false,
            'error' => $response->body(),
        ];
    }

    /**
     * Refresh OAuth token if needed
     */
    private function refreshTokenIfNeeded(CalendarIntegration $integration)
    {
        if (!$integration->needsTokenRefresh()) {
            return true;
        }

        if (!$integration->refresh_token) {
            return false;
        }

        switch ($integration->provider) {
            case 'google':
                return $this->refreshGoogleToken($integration);
            case 'outlook':
                return $this->refreshOutlookToken($integration);
            default:
                return false;
        }
    }

    /**
     * Refresh Google OAuth token
     */
    private function refreshGoogleToken(CalendarIntegration $integration)
    {
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $integration->refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $integration->access_token = $data['access_token'];
            $integration->token_expires_at = now()->addSeconds($data['expires_in'] ?? 3600);
            $integration->save();
            return true;
        }

        return false;
    }

    /**
     * Refresh Outlook OAuth token
     */
    private function refreshOutlookToken(CalendarIntegration $integration)
    {
        $response = Http::post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
            'client_id' => config('services.microsoft.client_id'),
            'client_secret' => config('services.microsoft.client_secret'),
            'refresh_token' => $integration->refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $integration->access_token = $data['access_token'];
            $integration->token_expires_at = now()->addSeconds($data['expires_in'] ?? 3600);
            $integration->save();
            return true;
        }

        return false;
    }

    /**
     * Generate event description
     */
    private function generateEventDescription(Appointment $appointment)
    {
        $description = "Appointment Details:\n";
        $description .= "Service: {$appointment->service->name}\n";
        $description .= "Customer: {$appointment->customer->name}\n";
        $description .= "Phone: {$appointment->customer->phone}\n";
        $description .= "Email: {$appointment->customer->email}\n";
        $description .= "Duration: {$appointment->service->duration} minutes\n";
        $description .= "Price: $" . number_format((float)$appointment->service->price, 2) . "\n";
        
        if ($appointment->location) {
            $description .= "Location: {$appointment->location->name}\n";
            $description .= "Address: {$appointment->location->full_address}\n";
        }
        
        if ($appointment->notes) {
            $description .= "\nNotes: {$appointment->notes}\n";
        }

        return $description;
    }

    /**
     * Sync all pending appointments
     */
    public function syncPendingAppointments()
    {
        $integrations = CalendarIntegration::active()->needsSync()->get();

        foreach ($integrations as $integration) {
            $appointments = Appointment::where('company_id', $integration->company_id)
                ->whereNull('calendar_event_id')
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->where('appointment_date', '>=', now())
                ->get();

            foreach ($appointments as $appointment) {
                $this->syncAppointmentToCalendar($appointment, $integration);
            }
        }
    }

    /**
     * Get OAuth authorization URL for calendar provider
     */
    public function getAuthorizationUrl(CalendarIntegration $integration)
    {
        switch ($integration->provider) {
            case 'google':
                return $this->getGoogleAuthUrl($integration);
            case 'outlook':
                return $this->getOutlookAuthUrl($integration);
            default:
                return null;
        }
    }

    /**
     * Handle OAuth callback
     */
    public function handleOAuthCallback(CalendarIntegration $integration, array $callbackData)
    {
        switch ($integration->provider) {
            case 'google':
                return $this->handleGoogleCallback($integration, $callbackData);
            case 'outlook':
                return $this->handleOutlookCallback($integration, $callbackData);
            default:
                return false;
        }
    }

    /**
     * Sync entire calendar
     */
    public function syncCalendar(CalendarIntegration $integration)
    {
        $appointments = Appointment::where('company_id', $integration->company_id)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('appointment_date', '>=', now())
            ->get();

        $syncedCount = 0;
        
        foreach ($appointments as $appointment) {
            try {
                $this->syncAppointmentToCalendar($appointment, $integration);
                $syncedCount++;
            } catch (\Exception $e) {
                // Log error but continue syncing other appointments
                Log::error("Failed to sync appointment {$appointment->id}: " . $e->getMessage());
            }
        }

        $integration->last_sync_at = now();
        $integration->save();

        return ['synced_count' => $syncedCount];
    }

    /**
     * Test calendar connection
     */
    public function testConnection(CalendarIntegration $integration)
    {
        try {
            switch ($integration->provider) {
                case 'google':
                    return $this->testGoogleConnection($integration);
                case 'outlook':
                    return $this->testOutlookConnection($integration);
                default:
                    return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get Google OAuth authorization URL
     */
    private function getGoogleAuthUrl(CalendarIntegration $integration)
    {
        $clientId = config('services.google.client_id');
        $redirectUri = route('calendar.callback', $integration);
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => 'https://www.googleapis.com/auth/calendar',
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ];

        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
    }

    /**
     * Get Outlook OAuth authorization URL
     */
    private function getOutlookAuthUrl(CalendarIntegration $integration)
    {
        $clientId = config('services.microsoft.client_id');
        $redirectUri = route('calendar.callback', $integration);
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => 'https://graph.microsoft.com/calendars.readwrite offline_access',
            'response_type' => 'code',
        ];

        return 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . http_build_query($params);
    }

    /**
     * Handle Google OAuth callback
     */
    private function handleGoogleCallback(CalendarIntegration $integration, array $callbackData)
    {
        if (!isset($callbackData['code'])) {
            return false;
        }

        $tokenData = [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'code' => $callbackData['code'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => route('calendar.callback', $integration),
        ];

        $response = Http::post('https://oauth2.googleapis.com/token', $tokenData);
        
        if ($response->successful()) {
            $tokens = $response->json();
            
            $integration->access_token = $tokens['access_token'];
            $integration->refresh_token = $tokens['refresh_token'] ?? null;
            $integration->token_expires_at = now()->addSeconds($tokens['expires_in'] ?? 3600);
            $integration->save();
            
            return true;
        }

        return false;
    }

    /**
     * Handle Outlook OAuth callback
     */
    private function handleOutlookCallback(CalendarIntegration $integration, array $callbackData)
    {
        if (!isset($callbackData['code'])) {
            return false;
        }

        $tokenData = [
            'client_id' => config('services.microsoft.client_id'),
            'client_secret' => config('services.microsoft.client_secret'),
            'code' => $callbackData['code'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => route('calendar.callback', $integration),
        ];

        $response = Http::post('https://login.microsoftonline.com/common/oauth2/v2.0/token', $tokenData);
        
        if ($response->successful()) {
            $tokens = $response->json();
            
            $integration->access_token = $tokens['access_token'];
            $integration->refresh_token = $tokens['refresh_token'] ?? null;
            $integration->token_expires_at = now()->addSeconds($tokens['expires_in'] ?? 3600);
            $integration->save();
            
            return true;
        }

        return false;
    }

    /**
     * Test Google Calendar connection
     */
    private function testGoogleConnection(CalendarIntegration $integration)
    {
        if (!$this->refreshTokenIfNeeded($integration)) {
            return false;
        }

        $response = Http::withToken($integration->access_token)
            ->get('https://www.googleapis.com/calendar/v3/calendars/' . urlencode($integration->calendar_id));

        return $response->successful();
    }

    /**
     * Test Outlook Calendar connection
     */
    private function testOutlookConnection(CalendarIntegration $integration)
    {
        if (!$this->refreshTokenIfNeeded($integration)) {
            return false;
        }

        $response = Http::withToken($integration->access_token)
            ->get('https://graph.microsoft.com/v1.0/me/calendars/' . urlencode($integration->calendar_id));

        return $response->successful();
    }
}
