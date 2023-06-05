<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::whereNull('activation_key')->inRandomOrder()->first()->id,
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'is_seen' => $this->faker->boolean,
        ];
    }
}
