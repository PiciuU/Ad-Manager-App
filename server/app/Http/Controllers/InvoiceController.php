<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceCollection;
use App\Http\Requests\InvoiceRequest;

class InvoiceController extends Controller
{

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
    protected function generatePrice($adStartDate, $adEndDate)
    {
        $activeDays = strtotime($adEndDate) - strtotime($adStartDate);
        return round(($activeDays / (60 * 60 * 24)) * 5.99, 2);
    }

    /**
     * Zwraca wszystkie faktury użytkownika.
     */
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        $userId = strval($userId);

        if ($user->tokenCan('admin')) {
            return new InvoiceCollection(Invoice::paginate());
        } else {
            $invoices = Invoice::whereHas('ad', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->paginate();
            return new InvoiceCollection($invoices);
        }

        return $this->errorResponse('Ann error occured', 403);
    }

    /**
     * Tworzy nową fakturę dla użytkownika.
     */
    public function store(InvoiceRequest $request)
    {
        $invoice = new InvoiceResource(Invoice::create($request->validated()));
        if (!$invoice) {
            return $this->errorResponse('An error occurred during creating the invoice, please try again later', 500);
        } else {
            // $invoiceController = new InvoiceController();
            // $invoiceController->createFromAd()
            return $this->successResponse('Invoice has been created successfully', $invoice);
        }
    }

    public function storeFromAd($invoice)
    {

        print_r($invoice['adStartDate']);
        $newInvoice = new Invoice();
        $newInvoice = [
            'ad_id' => $invoice['ad_id'],
            'number' => InvoiceController::generateInvoiceNumber(),
            'price' => InvoiceController::generatePrice($invoice['adStartDate'], $invoice['adEndDate']),
            'date' => $invoice['date'],
            'status' => $invoice['status']
        ];
        return new InvoiceResource(Invoice::create($newInvoice));
    }

    /**
     * Zwraca fakturę o podanym ID, ale tylko, jeśli użytkownik jest administratorem.
     * Jeśli nie jest administratorem, może zobaczyć fakturę o danym ID, tylko jeśli jest do niego przypisana.
     */
    public function show($id)
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, InvoiceRequest $request)
    {
        $user = auth()->user();
        if ($user->tokenCan('admin')) $invoice = Invoice::find($id);
        // else return $this->errorResponse('Unauthorized', 500);

        if (!$invoice) return $this->errorResponse('Invoice not found', 404);

        if (!($invoice->update($request->validate([])))) return $this->errorResponse('An error occurred while updating the Invoice, please try again later', 500);

        return $this->successResponse('Invoice has been successfully updated', new InvoiceResource($invoice));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($id)
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
