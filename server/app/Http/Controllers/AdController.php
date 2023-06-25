<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Resources\AdCollection;
use App\Http\Resources\AdResource;
use App\Http\Requests\AdRequest;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    /**
     * Get the list of ads.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        $ads = new AdCollection(auth()->user()->ads()->select('id', 'name')->orderBy('created_at', 'desc')->get());

        $ads->returnFields(['id', 'name']);

        return $this->successResponse('Ads has been successfully found', $ads);
    }

    /**
     * Store a newly created ad in storage.
     *
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(AdRequest $request)
    {
        $ad = Ad::create($request->validated());

        if (!$ad) return $this->errorResponse('An error occurred while creating the advert, try again later', 500);

        $invoiceController = new InvoiceController();

        $invoice = $invoiceController->createInvoice([
            'ad_id' => $ad->id,
            'ad_start_date' => $ad->ad_start_date,
            'ad_end_date' => $ad->ad_end_date,
        ]);

        return $this->successResponse('Ad has been created successfully', new AdResource($ad));
    }

    /**
     * Display the specified ad.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse('Ad not found.', 404);

        return $this->successResponse('Ad has been successfully found', new AdResource($ad));
    }

    /**
     * Update the specified ad in storage.
     *
     * @param  int  $id
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, AdRequest $request)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse('Ad not found.', 404);

        if (!$ad->update($request->validated())) return $this->errorResponse('An error occurred while updating the ad, please try again later', 500);

        return $this->successResponse('Ad has been successfully updated', new AdResource($ad));
    }

    /**
     * Deactivate the specified ad.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function deactivate($id) {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse('Ad not found.', 404);

        if (!$ad->update(['status' => 'inactive'])) return $this->errorResponse('An error occurred while deactivatng the ad, please try again later', 500);

        return $this->successResponse('Ad has been successfully deactivated', new AdResource($ad));
    }

    /**
     * Renew the specified ad.
     *
     * @param  int  $id
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function renew($id, AdRequest $request) {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse('Ad not found.', 404);

        $validatedData = $request->validated();
        $validatedData['status'] = 'unpaid';

        if (!$ad->update($validatedData)) return $this->errorResponse('An error occurred while renewing the ad, please try again later', 500);

        $invoiceController = new InvoiceController();

        $invoice = $invoiceController->createInvoice([
            'ad_id' => $ad->id,
            'ad_start_date' => $ad->ad_start_date,
            'ad_end_date' => $ad->ad_end_date,
        ]);

        return $this->successResponse('Ad has been successfully renewed', [ 'advert' => new AdResource($ad), 'invoice' => $invoice]);
    }

    /**
     * Remove the specified ad from storage.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($id) // CURRENTLY NOT USED
    {
        $user = auth()->user();
        $ad = Ad::find($id);
        if ($ad->user_id == $user->id || $user->tokenCan('admin')) {
            if (!$ad) return $this->errorResponse('Ad not found!', 404);
            if (!$ad->delete()) return $this->errorResponse('An error occurred while deleting the Ad, please try again later', 500);
            return $this->successResponse('Ad has been successfully deleted');
        }
        return $this->errorResponse('Ad not available', 403);
    }
}
