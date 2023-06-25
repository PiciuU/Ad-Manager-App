<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Ad;

use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceCollection;
use App\Http\Requests\InvoiceRequest;

use Carbon\Carbon;

class InvoiceController extends Controller
{
    const PRICE_PER_DAY = 10;

    /**
     * Generate the next invoice number based on the last invoice number in the database.
     *
     * @return string
     */
    protected function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();

        if ($lastInvoice) {
            $lastNumber = substr($lastInvoice->number, 8); // Pobierz ostatnią część numeru faktury
            $nextNumber = str_pad((int)$lastNumber + 1, 8, '0', STR_PAD_LEFT); // Inkrementuj liczbę i dodaj wiodące zera
        } else {
            $nextNumber = '00000001'; // Jeśli nie ma poprzednich faktur, zacznij od 00000001
        }

        $invoiceNumber = 'INV-' . $nextNumber;
        return $invoiceNumber;
    }

    /**
     * Generate the price for the invoice based on the start and end dates of the ad.
     *
     * @param  string  $adStartDate
     * @param  string  $adEndDate
     * @return float
     */
    protected function generatePrice($adStartDate, $adEndDate)
    {
        $activeDays = strtotime($adEndDate) - strtotime($adStartDate);
        return round(($activeDays / (60 * 60 * 24)) * self::PRICE_PER_DAY, 2);
    }

    /**
     * Generate the notes for the invoice based on the payload.
     *
     * @param  array  $payload
     * @return string
     */
    protected function generateNotes($payload)
    {
        $startDate = Carbon::parse($payload['ad_start_date']);
        $endDate = Carbon::parse($payload['ad_end_date']);

        $diffInDays = $endDate->diffInDays($startDate);

        $daysLabel = ($diffInDays === 1) ? 'dzień' : 'dni';

        $startFormatted = $startDate->translatedFormat('d F Y');
        $endFormatted = $endDate->translatedFormat('d F Y');

        return "Faktura dotyczy opłaconej kampanii reklamowej, która trwać ma przez okres $diffInDays $daysLabel, od $startFormatted do $endFormatted.";
    }

    /**
     * Internal Method
     * Create a new invoice based on the payload.
     *
     * @param  array  $payload
     * @return \App\Http\Resources\InvoiceResource
     */
    public function createInvoice($payload)
    {
        $invoice = Invoice::create([
            'ad_id' => $payload['ad_id'],
            'number' => self::generateInvoiceNumber(),
            'price' => self::generatePrice($payload['ad_start_date'], $payload['ad_end_date']),
            'date' => date('Y-m-d'),
            'status' => 'unpaid',
            'notes' => self::generateNotes($payload)
        ]);

        return new InvoiceResource($invoice);
    }

    /**
     * Get the list of invoices for a specific ad.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index($id)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse('Ad not found.', 404);

        $invoices = new InvoiceCollection($ad->invoices()->orderBy('id', 'desc')->get());

        return $this->successResponse('Invoices has been successfully found', $invoices);
    }

    /**
     * Mark the invoice as paid and update the dates of the associated ad.
     *
     * @param  int  $id
     * @param  int  $invoiceId
     * @return \App\Http\Traits\ResponseTrait
     */
    public function payment($id, $invoiceId)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse('Ad not found.', 404);

        $invoice = $ad->invoices()->where('id', $invoiceId)->first();

        if (!$invoice) return $this->errorResponse('Invoice not found.', 404);

        /* Recalculate dates of advert emission */
        $startDate = Carbon::parse($ad->ad_start_date);
        $endDate = Carbon::parse($ad->ad_end_date);
        $currentDate = Carbon::now();

        $diffInDays = $endDate->diffInDays($startDate);

        if ($startDate < $currentDate) {
            $startDate = $currentDate;
            $endDate = $startDate->copy()->addDays($diffInDays);
        }

        $invoice->update([
            'status' => 'paid',
            'date' => Carbon::now()->format('Y-m-d')
        ]);

        $ad->update([
            'status' => 'active',
            'ad_start_date' => $startDate->format('Y-m-d'),
            'ad_end_date' => $endDate->format('Y-m-d')
        ]);

        return $this->successResponse('Invoice has been successfully paid, the ad is now active', ['advert' => $ad, 'invoice' => $invoice]);
    }

    /**
     * Display the specified invoice.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id) // CURRENTLY NOT USED
    {
        $invoice = Invoice::find($id);
        if (!$invoice) return $this->errorResponse("Invoice doesn\'t exists", 404);
        // Sprawdzenie uprawnień użytkownika
        if (auth()->user()->isAdmin() || $invoice->ad->user_id === auth()->user()->id) {
            return new InvoiceResource($invoice);
        }
        return $this->errorResponse('An error occured', 403);
    }

    /**
     * Update the invoice with the given ID.
     *
     * @param  int  $id
     * @param  \App\Http\Requests\InvoiceRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, InvoiceRequest $request) // CURRENTLY NOT USED
    {
        $user = auth()->user();
        if ($user->tokenCan('admin')) $invoice = Invoice::find($id);
        // else return $this->errorResponse('Unauthorized', 500);

        if (!$invoice) return $this->errorResponse('Invoice not found', 404);

        if (!($invoice->update($request->validate([])))) return $this->errorResponse('An error occurred while updating the Invoice, please try again later', 500);

        return $this->successResponse('Invoice has been successfully updated', new InvoiceResource($invoice));
    }

    /**
     * Delete the invoice with the given ID.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($id) // CURRENTLY NOT USED
    {
        $user = auth()->user();
        $invoice = Invoice::find($id);
        if ($user->tokenCan('admin')) {
            if (!$invoice) return $this->errorResponse('Invoice not found!', 404);
            if (!$invoice->delete()) return $this->errorResponse('An error occurred while deleting the Invoice, please try again later', 500);
            return $this->successResponse('Invoice has been successfully deleted');
        }
        return $this->errorResponse('Invoice not available', 403);
    }
}
