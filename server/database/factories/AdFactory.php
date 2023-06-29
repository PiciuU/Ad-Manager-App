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

        if ($status === 'active' || $status === 'unpaid') {
            $startDate = $this->faker->dateTimeBetween('-3 days', 'now');
            $endDate = $this->faker->dateTimeBetween('+7 days', '+14 days');
        } elseif ($status === 'expired') {
            $endDate = $this->faker->dateTimeBetween('-1 month', 'now');
            $startDate = $this->faker->dateTimeBetween('-2 month', $endDate);
        } elseif ($status === 'inactive') {
            $endDate = $this->faker->dateTimeBetween('-1 month', 'now');
            $startDate = $this->faker->dateTimeBetween('-2 month', $endDate);
        }

        $extension = $this->faker->randomElement(['png', 'mp4']);
        $filename = substr(hash('sha256', time()), 0, 16) . '.' . $extension;
        $filetype = ($extension === 'png') ? 'img' : 'video';

        return [
            'name' => $this->faker->word . ' ' . $this->faker->word,
            'user_id' => User::whereNull('activation_key')->inRandomOrder()->first()->id,
            'status' => $status,
            'ad_start_date' => $startDate,
            'ad_end_date' => $endDate,
            'file_name' => $filename,
            'file_type' => $filetype,
            'url' => $this->faker->url,
        ];
    }
}
