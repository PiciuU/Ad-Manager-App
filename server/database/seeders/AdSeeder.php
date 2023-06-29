<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ad;
use App\Models\Invoice;

class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersWithoutActivationKey = User::whereNull('activation_key')->pluck('id')->toArray();

        foreach ($usersWithoutActivationKey as $userId) {

            if ($userId == 1) {
                Ad::factory()->count(6)->create([
                    'user_id' => $userId,
                ]);
            } else {
                Ad::factory()->count(rand(0, 5))->create([
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
