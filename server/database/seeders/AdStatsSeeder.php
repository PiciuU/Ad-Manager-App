<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ad;
use App\Models\AdStats;
use Carbon\Carbon;

class AdStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ads = Ad::whereIn('status', ['active', 'inactive', 'expired'])->get();

        foreach ($ads as $ad) {
            $startDate = Carbon::parse($ad->ad_start_date);
            $endDate = Carbon::parse($ad->ad_end_date);

            $dates = [];
            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                if ($currentDate <= Carbon::today()) {
                    $dates[] = $currentDate->format('Y-m-d');
                }
                $currentDate = $currentDate->addDay();
            }

            foreach ($dates as $date) {
                AdStats::factory()->create([
                    'ad_id' => $ad->id,
                    'date' => $date,
                ]);
            }
        }
    }
}
