<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Log;
use App\Models\Ad;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereNull('activation_key')->get();

        foreach ($users as $user) {

            // Log 1: Użytkownik aktywuje konto
            Log::factory()->count(1)->create([
                'user_id' => $user->id,
                'operation_tags' => 'AUTH/ACTIVATE',
                'message' => 'Użytkownik aktywował swoje konto.',
            ]);

            $ad = $user->ads()->inRandomOrder()->first();

            if ($ad) {
                // Log 2: Użytkownik aktualizuje reklamę
                Log::factory()->count(1)->create([
                    'user_id' => $user->id,
                    'ad_id' => $ad->id,
                    'operation_tags' => 'AD/UPDATE',
                    'message' => 'Użytkownik zaktualizował swoją reklamę.',
                ]);

                // Log 3: Użytkownik tworzy nową reklamę
                Log::factory()->count(1)->create([
                    'user_id' => $user->id,
                    'ad_id' => $ad->id,
                    'operation_tags' => 'AD/CREATE',
                    'message' => 'Użytkownik utworzył nową reklamę.',
                ]);

                // Log 4: Użytkownik opłaca fakturę
                Log::factory()->count(1)->create([
                    'user_id' => $user->id,
                    'ad_id' => $ad->id,
                    'operation_tags' => 'INVOICE/PAYMENT',
                    'message' => 'Użytkownik opłacił swoją reklamę.',
                ]);
            } else {
                Log::factory()->count(1)->create([
                    'user_id' => $user->id,
                    'operation_tags' => 'AUTH/UPDATE',
                    'message' => 'Użytkownik zaktualizował swoje dane.',
                ]);
            }
        }
    }
}
