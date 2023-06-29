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

        $lastInvoice = null;

        foreach ($ads as $ad) {

            if ($lastInvoice) {
                $lastNumber = substr($lastInvoice, 8); // Pobierz ostatnią część numeru faktury
                $nextNumber = str_pad((int)$lastNumber + 1, 8, '0', STR_PAD_LEFT); // Inkrementuj liczbę i dodaj wiodące zera
            } else {
                $nextNumber = '00000001'; // Jeśli nie ma poprzednich faktur, zacznij od 00000001
            }

            $lastInvoice = 'INV-' . $nextNumber;

            $invoiceStatus = $ad->status === 'unpaid' ? 'unpaid' : 'paid';
            $invoiceDate = $ad->ad_start_date;

            $startDate = Carbon::parse($ad->ad_start_date);
            $endDate = Carbon::parse($ad->ad_end_date);
            $diffInDays = $endDate->diffInDays($startDate);
            $price = round($diffInDays * 10, 2);
            $daysLabel = ($diffInDays === 1) ? 'dzień' : 'dni';
            $startFormatted = $startDate->translatedFormat('d F Y');
            $endFormatted = $endDate->translatedFormat('d F Y');
            $notes = "Faktura dotyczy opłaconej kampanii reklamowej, która trwać ma przez okres $diffInDays $daysLabel, od $startFormatted do $endFormatted.";

            Invoice::factory()->create([
                'ad_id' => $ad->id,
                'number' => $lastInvoice,
                'date' => $invoiceDate,
                'status' => $invoiceStatus,
                'price' => $price,
                'notes' => $notes
            ]);
        }
    }
}
