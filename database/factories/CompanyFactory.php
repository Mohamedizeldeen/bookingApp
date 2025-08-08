<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'user_id' => 1,
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'website' => $this->faker->url,
            'subscription_plan' => $this->faker->randomElement(['starter', 'professional', 'enterprise']),
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addYear(),
            'settings' => json_encode([]),
            'is_active' => true,
        ];
    }
}
