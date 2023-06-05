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
                'operation_tags' => 'ACTIVATE',
                'message' => 'Użytkownik poprawnie aktywował konto',
            ]);

            $ad = $user->ads()->inRandomOrder()->first();
            // Log 2: Użytkownik aktualizuje reklamę
            Log::factory()->count(1)->create([
                'user_id' => $user->id,
                'ad_id' => $ad->id ?? null,
                'operation_tags' => 'EDIT_ADVERT',
                'message' => 'Użytkownik zaktualizował treść reklamy',
            ]);

            // Log 3: Użytkownik tworzy nową reklamę
            Log::factory()->count(1)->create([
                'user_id' => $user->id,
                'ad_id' => $ad->id ?? null,
                'operation_tags' => 'CREATE_ADVERT',
                'message' => 'Użytkownik stworzył nową reklamę',
            ]);

            // Log 4: Użytkownik opłaca fakturę
            Log::factory()->count(1)->create([
                'user_id' => $user->id,
                'ad_id' => $ad->id ?? null,
                'operation_tags' => 'PAY_INVOICE',
                'message' => 'Użytkownik opłacił fakturę',
            ]);

            // Log 5: Losowy log dla różnorodności
            Log::factory()->count(1)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
