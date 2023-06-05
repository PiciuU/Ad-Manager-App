<?php

namespace Database\Factories;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdStats>
 */
class AdStatsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ad_id' => Ad::inRandomOrder()->first()->id,
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'views' => $this->faker->numberBetween(500, 5000),
            'clicks' => $this->faker->numberBetween(10, 250),
        ];
    }
}
