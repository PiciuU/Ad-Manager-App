<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereNull('activation_key')->get();

        foreach ($users as $user) {
            $ads = $user->ads;

            foreach ($ads as $ad) {
                if ($ad->status === 'expired') {
                    Notification::factory()->create([
                        'user_id' => $user->id,
                        'title' => 'Zakończenie publikacji reklamy',
                        'date' => $ad->ad_end_date,
                        'description' => 'Twoja reklama "' . $ad->name . '" zakończyła swoją publikację.',
                    ]);
                } elseif ($ad->status === 'unpaid') {
                    $invoice = $ad->invoices[0];
                    Notification::factory()->create([
                        'user_id' => $user->id,
                        'title' => 'Nieopłacona faktura',
                        'date' => Carbon::now(),
                        'description' => 'Masz nieopłaconą fakturę (' . $invoice->number . ') za reklamę "' . $ad->name . '".',
                    ]);
                }
            }
        }
    }
}
