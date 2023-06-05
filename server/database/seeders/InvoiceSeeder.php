<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ad;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ads = Ad::all();

        foreach ($ads as $ad) {
            $invoiceStatus = $ad->status === 'unpaid' ? 'unpaid' : 'paid';
            $invoiceDate = $ad->ad_start_date ? $ad->ad_start_date : Carbon::now();

            Invoice::factory()->create([
                'ad_id' => $ad->id,
                'date' => $invoiceDate,
                'status' => $invoiceStatus,
            ]);
        }
    }
}
