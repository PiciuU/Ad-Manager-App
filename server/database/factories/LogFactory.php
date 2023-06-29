<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Ad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Log>
 */
class LogFactory extends Factory
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
            'ad_id' => null,
            'operation_tags' => $this->faker->words(1, true),
            'message' => $this->faker->sentence,
            'notes' => null
        ];
    }
}
