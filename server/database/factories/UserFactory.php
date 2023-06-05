<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_role_id' => 1,
            'name' => $this->faker->name,
            'login' => $this->faker->unique()->userName,
            'password' => Hash::make('password'), // Default password is "password"
            'email' => $this->faker->unique()->safeEmail,
            'activation_key' => null,
            'nip' => $this->faker->numerify('##########'),
            'address' => $this->faker->address,
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->country,
            'company_email' => $this->faker->companyEmail,
            'company_phone' => $this->faker->phoneNumber,
            'representative' => $this->faker->name,
            'representative_phone' => $this->faker->phoneNumber,
            'notes' => null,
            'is_banned' => false,
            'ban_reason' => null,
            'activated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
