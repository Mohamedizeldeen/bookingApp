<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition()
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->words(2, true) . ' Location',
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->safeEmail,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'timezone' => 'America/New_York',
            'working_hours' => json_encode([
                'monday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
                'tuesday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
                'wednesday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
                'thursday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
                'friday' => ['is_open' => true, 'start_time' => '09:00', 'end_time' => '17:00'],
                'saturday' => ['is_open' => false, 'start_time' => '10:00', 'end_time' => '14:00'],
                'sunday' => ['is_open' => false, 'start_time' => '10:00', 'end_time' => '14:00'],
            ]),
            'is_active' => true,
        ];
    }
}
