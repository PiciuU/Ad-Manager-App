<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ad>
 */
class AdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['unpaid', 'active', 'inactive', 'expired']);
        $startDate = null;
        $endDate = null;

        if ($status === 'active') {
            $startDate = $this->faker->dateTimeBetween('-1 month', 'now');
            $endDate = $this->faker->dateTimeBetween('now', $startDate->format('Y-m-d').' +1 month');
        } elseif ($status === 'expired') {
            $endDate = $this->faker->dateTimeBetween('-1 month', 'now');
            $startDate = $this->faker->dateTimeBetween('-2 month', $endDate);
        } elseif ($status === 'inactive') {
            $endDate = $this->faker->dateTimeBetween('-1 month', 'now');
            $startDate = $this->faker->dateTimeBetween('-2 month', $endDate);
        }

        return [
            'name' => $this->faker->sentence,
            'user_id' => User::whereNull('activation_key')->inRandomOrder()->first()->id,
            'status' => $status,
            'ad_start_date' => $startDate,
            'ad_end_date' => $endDate,
            'file_name' => $this->faker->word,
            'file_type' => $this->faker->randomElement(['img', 'video']),
            'url' => $this->faker->url,
        ];
    }
}
