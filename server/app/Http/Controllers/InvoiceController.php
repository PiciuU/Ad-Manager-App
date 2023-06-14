<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Resources\InvoiceResource;

class InvoiceController extends Controller
{

    function generateInvoiceNumber()
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
    /**
     * Zwraca wszystkie faktury użytkownika.
     */
    public function index()
    {
        $userId = auth()->user()->id;
        $invoices = Invoice::whereHas('ad', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return response()->json($invoices);
    }

    /**
     * Tworzy nową fakturę dla użytkownika.
     */
    public function store(Request $request)
    {
        // // Walidacja danych wejściowych
        // $this->validate($request, [
        //     'ad_id' => 'required',
        //     'price' => 'required',
        //     'date' => 'required|date',
        //     'status' => 'required|in:paid,unpaid',
        // ]);

        // $ad = Ad::findOrFail($request->input('ad_id'));

        // // Sprawdzenie uprawnień użytkownika, tylko admin może dodawać nowe faktury 'ręcznie'
        // $invoiceController = new InvoiceController();

        // $currentDate = new \DateTime();
        // if (auth()->user()->isAdmin()) {
        //     $invoice = new Invoice();
        //     $invoice->ad_id = $ad->id;
        //     $invoice->number = $invoiceController->generateInvoiceNumber();
        //     $invoice->price = $request->input('price');
        //     $invoice->date = $currentDate->format('Y-m-d H:i:s');
        //     $invoice->status = $request->input('status');
        //     $invoice->save();

        //     return response()->json($invoice, 201);
        // }

        // return response()->json(['message' => 'Unauthorized'], 401);
        // $invoice = new InvoiceResource(Invoice::create($request));
        // return $this->successResponse('Ad has been created successfully', $invoice);
    }


    /**
     * Zwraca fakturę o podanym ID, ale tylko, jeśli użytkownik jest administratorem.
     * Jeśli nie jest administratorem, może zobaczyć fakturę o danym ID, tylko jeśli jest do niego przypisana.
     */
    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Sprawdzenie uprawnień użytkownika
        if (auth()->user()->isAdmin() || $invoice->ad->user_id === auth()->user()->id) {
            return response()->json($invoice);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Edytuje fakturę o podanym ID, ale tylko, jeśli użytkownik jest administratorem.
     * Jeśli nie jest administratorem, może edytować fakturę o danym ID, tylko jeśli jest do niego przypisana.
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Sprawdzenie uprawnień użytkownika
        if (auth()->user()->isAdmin()) {
            $invoice->ad_id = $request->has('ad_id') ? $request->input('ad_id') : $invoice->ad_id;
            $invoice->number = $request->has('number') ? $request->input('number') : $invoice->number;
            $invoice->price = $request->has('price') ? $request->input('price') : $invoice->price;
            $invoice->date = $request->has('date') ? $request->input('date') : $invoice->date;
            $invoice->status = $request->has('status') ? $request->input('status') : $invoice->status;

            $invoice->save();

            return response()->json($invoice);
        } elseif ($invoice->ad->user_id === auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }


    /**
     * Usuwa fakturę o podanym ID, ale tylko, jeśli użytkownik jest administratorem.
     */
    public function delete($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Sprawdzenie uprawnień użytkownika
        if (auth()->user()->isAdmin()) {
            $invoice->delete();

            return response()->json(['message' => 'Invoice deleted']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
