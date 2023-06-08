<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Invoice;
use App\Http\Controllers\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdController extends Controller
{
    /**
     * Pobiera wszystkie reklamy przypisane do danego użytkownika
     * lub, jeśli użytkownik jest administratorem to wyświetla wszystkie reklamy.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();

        // Sprawdzenie, czy użytkownik jest administratorem
        if ($user->isAdmin()) {
            $ads = Ad::all();
        } else {
            $ads = $user->ads;
        }

        return response()->json($ads);
    }


    /**
     * Zapisuje nową reklamę i tworzy nową fakturę.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        $user = Auth::user();

        // Walidacja danych
        $validatedData = $request->validate([
            'name' => 'required',
            'ad_start_date' => 'date',
            'ad_end_date' => 'date',
            'file_name' => 'required',
            'file_type' => 'required',
            'url' => 'required|url',
        ]);

        // Sprawdzenie, czy użytkownik jest administratorem
        if ($user->isAdmin()) {
            // Jeśli użytkownik jest administratorem, pobierz ID użytkownika z danych
            $validatedData += $request->validate([
                'user_id' => 'required',
            ]);
            $userId = $request->input('user_id') ?? null;
        } else {
            // Jeśli użytkownik nie jest administratorem, przypisz ID zalogowanego użytkownika
            $userId = $user->id;
        }

        // Tworzenie nowej reklamy
        $ad = new Ad();
        $ad->name = $validatedData['name'];
        $ad->user_id = $userId;
        $ad->ad_start_date = $validatedData['ad_start_date'];
        $ad->ad_end_date = $validatedData['ad_end_date'];
        $ad->file_name = $validatedData['file_name'];
        $ad->file_type = $validatedData['file_type'];
        $ad->url = $validatedData['url'];
        $ad->save();

        // Obliczanie ceny faktury
        $activeDays = strtotime($ad->ad_end_date) - strtotime($ad->ad_start_date);
        $price = round(($activeDays / (60 * 60 * 24)) * 5.99, 2);

        $invoiceController = new InvoiceController();

        // Tworzenie nowego obiektu faktury
        $invoice = new Invoice();
        $invoice->ad_id = $ad->id;
        $invoice->number = $invoiceController->generateInvoiceNumber(); // Przykładowy numer faktury
        $invoice->price = $price;
        $invoice->date = date('Y-m-d'); // Bieżąca data
        $invoice->status = 'unpaid'; // Domyślny status dla nowej faktury
        $invoice->save();

        return response()->json([
            'ad' => $ad,
            'invoice' => $invoice
        ]);
    }




    /**
     * Wyświetla szczegóły reklamy o podanym ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        $ad = Ad::findOrFail($id);

        if ($user->isAdmin() || $ad->user_id === $user->id) {
            return response()->json($ad);
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    /**
     * Aktualizuje reklamę o podanym ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $ad = Ad::findOrFail($id);

        if ($user->isAdmin() || $ad->user_id === $user->id) {
            // Aktualizacja pól tylko jeśli są obecne w żądaniu
            $ad->name = $request->has('name') ? $request->input('name') : $ad->name;
            $ad->ad_start_date = $request->has('ad_start_date') ? $request->input('ad_start_date') : $ad->ad_start_date;
            $ad->ad_end_date = $request->has('ad_end_date') ? $request->input('ad_end_date') : $ad->ad_end_date;
            $ad->file_name = $request->has('file_name') ? $request->input('file_name') : $ad->file_name;
            $ad->file_type = $request->has('file_type') ? $request->input('file_type') : $ad->file_type;
            $ad->url = $request->has('url') ? $request->input('url') : $ad->url;

            $ad->save();

            return response()->json($ad);
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    /**
     * Usuwa reklamę o podanym ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $user = Auth::user();
        $ad = Ad::findOrFail($id);
        // $status = $ad('status')

        if ($user->isAdmin()) {
            $ad->delete();

            return response()->json(['message' => 'Ad deleted successfully']);
        } else {
            // Jeśli użytkownik nie jest administratorem, może tylko ustawić status reklamy na "expired"
            $ad->status = 'inactive';
            $ad->save();

            return response()->json([
                'message' => 'Ad status updated successfully',
                $ad
            ]);
        }
    }
}
