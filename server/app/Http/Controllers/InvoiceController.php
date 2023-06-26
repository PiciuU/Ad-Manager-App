<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Http\Resources\AdResource;

use App\Models\Invoice;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;

use Carbon\Carbon;

class InvoiceController extends Controller
{
    const PRICE_PER_DAY = 10;

    private $logController;

    public function __construct(LogController $logController = new LogController())
    {
        $this->logController = $logController;
    }

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
     * Create a new invoice based on the payload.
     * This method is internal and should only be called by an instance of InvoiceController.
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
     * Checks if the user has administrator privileges.
     *
     * @return bool
     */
    public function hasAccess()
    {
        return auth()->user()->hasAdminPrivileges();
    }

    /**
     * =====================
     *     ADMIN SECTION
     * =====================
     */

    /**
     * Retrieve a list of invoices.
     * This method is accessible only to administrators.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function indexAsAdmin($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $invoices = new InvoiceCollection($ad->invoices()->orderBy('id', 'desc')->get());

        return $this->successResponse("Invoices has been successfully found.", $invoices);
    }

    /**
     * Mark the invoice as paid and update the dates of the associated ad.
     * This method is accessible only to administrators.
     *
     * @param  int  $id
     * @param  int  $invoiceId
     * @return \App\Http\Traits\ResponseTrait
     */
    public function paymentAsAdmin($id, $invoiceId)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $invoice = $ad->invoices()->where('id', $invoiceId)->first();

        if (!$invoice) return $this->errorResponse("Invoice not found.", 404);

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

        $this->logController->createLogEntry('ADMIN/INVOICE/PAYMENT', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Invoice has been successfully paid, the ad is now active.", ['advert' => new AdResource($ad), 'invoice' => new InvoiceResource($invoice)]);
    }

    /**
     * Store a new invoice.
     * This method is accessible only to administrators.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function storeAsAdmin($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $invoice = self::createInvoice([
            'ad_id' => $ad->id,
            'ad_start_date' => $ad->ad_start_date,
            'ad_end_date' => $ad->ad_end_date,
        ]);

        $this->logController->createLogEntry('ADMIN/INVOICE/CREATE', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Invoice has been successfully created.", $invoice);
    }

    /**
     * Retrieve a specific invoice by its ID.
     * This method is accessible only to administrators.
     * (Currently not used)
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function showAsAdmin($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $invoice = Invoice::find($id);

        if (!$invoice) return $this->errorResponse("Invoice not found.", 404);

        return $this->successResponse("Invoice has been successfully found.", new InvoiceResource($ad));
    }

    /**
     * Update an existing invoice.
     * This method is accessible only to administrators.
     * (Currently not used)
     *
     * @param  int  $id
     * @param  \App\Http\Requests\InvoiceRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function updateAsAdmin($id, $invoiceId, InvoiceRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $invoice = Invoice::find($invoiceId);

        if (!$invoice) return $this->errorResponse("Invoice not found.", 404);

        if (!$invoice->update($request->validated())) return $this->errorResponse("An error occurred while updating the invoice, try again later.", 500);

        return $this->successResponse("Invoice has been successfully updated.", new InvoiceResource($invoice));
    }

    /**
     * Delete the invoice with the given ID.
     * This method is accessible only to administrators.
     * (Currently not used)
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function deleteAsAdmin($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $invoice = Invoice::find($id);

        if (!$invoice) return $this->errorResponse("Invoice not found.", 404);

        if (!$invoice->delete()) return $this->errorResponse("An error occurred while deleting the invoice, try again later.", 500);

        return $this->successResponse("Invoice has been successfully deleted.");
    }

    /**
     * =====================
     *     USER SECTION
     * =====================
     */

    /**
     * Retrieve a list of invoices.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index($id)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $invoices = new InvoiceCollection($ad->invoices()->orderBy('id', 'desc')->get());

        return $this->successResponse("Invoices has been successfully found.", $invoices);
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

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $invoice = $ad->invoices()->where('id', $invoiceId)->first();

        if (!$invoice) return $this->errorResponse("Invoice not found.", 404);

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

        $this->logController->createLogEntry('INVOICE/PAYMENT', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Invoice has been successfully paid, the ad is now active.", ['advert' => new AdResource($ad), 'invoice' => new InvoiceResource($invoice)]);
    }
}
