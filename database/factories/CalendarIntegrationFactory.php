<?php

namespace Database\Factories;

use App\Models\CalendarIntegration;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarIntegrationFactory extends Factory
{
    protected $model = CalendarIntegration::class;

    public function definition()
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => null,
            'provider' => $this->faker->randomElement(['google', 'outlook', 'apple', 'caldav']),
            'external_calendar_id' => $this->faker->uuid,
            'access_token' => $this->faker->sha256,
            'refresh_token' => $this->faker->sha256,
            'token_expires_at' => now()->addHour(),
            'sync_frequency' => $this->faker->randomElement(['15min', '30min', '1hour', '2hours']),
            'sync_direction' => $this->faker->randomElement(['bidirectional', 'to_external', 'from_external']),
            'last_sync_at' => now()->subMinutes($this->faker->numberBetween(1, 60)),
            'sync_status' => 'active',
            'settings' => json_encode([
                'auto_sync' => true,
                'conflict_resolution' => 'prefer_external',
            ]),
            'is_active' => true,
        ];
    }
}
