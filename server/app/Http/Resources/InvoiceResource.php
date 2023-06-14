<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;


class InvoiceResource extends JsonResource
{

    public function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();

        if ($lastInvoice) {
            $lastNumber = substr($lastInvoice->number, 4); // Pobierz ostatnią część numeru faktury
            $nextNumber = str_pad((int)$lastNumber + 1, 8, '0', STR_PAD_LEFT); // Inkrementuj liczbę i dodaj wiodące zera
        } else {
            $nextNumber = '00000001'; // Jeśli nie ma poprzednich faktur, zacznij od 00000001
        }

        $invoiceNumber = 'INV-' . $nextNumber;
        return $invoiceNumber;
    }

    public function generatePrice($ad_start_date, $ad_end_date)
    {
        $activeDays = strtotime($ad_end_date) - strtotime($ad_start_date);
        return round(($activeDays / (60 * 60 * 24)) * 5.99, 2);
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        $currentDate = new \DateTime();
        // Obliczanie ceny faktury

        if (Auth::user()->isAdmin()) {
            return [
                // 'ad_id' => $request->id,
                'ad_id' => '12',
                'number' => generateInvoiceNumber(),
                'price' => $request->$price,
                'date' => $currentDate->format('Y-m-d H:i:s'),
                'status' => 'inactive',
            ];

            // return response()->json($invoice, 201);
        }

        // if ((Auth::user())->isAdmin()) {
        //     return [
        //         'ad_id' => $this->ad_id,
        //         'price' => $this->price,
        //         'date' => $this->date,
        //         'status' => $this->status,
        //     ];
        // } else {
        //     return [
        //         'ad_id' => $this->ad_id,
        //         'price' => $this->price,
        //         'date' => $this->date,
        //         'status' => $this->status,
        //     ];
        // }
    }
}
