<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Http\Resources\AdCollection;
use App\Http\Resources\AdResource;
use App\Http\Requests\AdRequest;
use App\Http\Controllers\InvoiceController;


class AdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $user_id = $user->id;

        if ($user->tokenCan('admin')) $ads = new AdCollection(Ad::all());
        else $ads = new AdCollection(Ad::where('user_id', $user_id));


        return $this->successResponse('List of Ads found', $ads);
    }

    /**
     * Store a newly created ad in storage.
     *
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(AdRequest $request)
    {

        $ad = new AdResource(Ad::create($request->validated()));
        $invoiceController = new InvoiceController();

        $invoice = [
            'ad_id' => $ad->id,
            'adEndDate' => $ad->ad_end_date,
            'adStartDate' => $ad->ad_start_date,
            'date' => date('Y-m-d H:i:s'),
            'status' => 'unpaid'

        ];
        $newInvoice = $invoiceController->storeFromAd($invoice);


        if (!$ad) {
            return $this->errorResponse('An error occurred during creating the Ad, please try again later', 500);
        } else {
            print_r($ad->toArray);
            return $this->successResponse('Ad has been created successfully', [$ad, $newInvoice]);
        }
    }

    /**
     * Display the specified exercise.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     *
     */
    public function show($id)
    {
        $ad = Ad::find($id);

        if (!$ad) return $this->errorResponse('Ad not found.', 404);

        $user = auth()->user();

        if ($ad->user_id !== $user->id && !$user->tokenCan('admin')) {
            return $this->errorResponse('Ad not available', 403);
        }

        return $this->successResponse('Ad found', new AdResource($ad));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, AdRequest $request)
    {
        $user = auth()->user();

        if ($user->tokenCan('admin')) $ad = Ad::find($id);
        else $ad = $user->ads()->find($id);

        if (!$ad) return $this->errorResponse('Ad not found', 404);

        if (!$ad->update($request->validate())) return $this->errorResponse('An error occurred while updating the Ad, please try again later', 500);

        return $this->successResponse('Ad has been successfully updated', new AdResource($ad));
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
        $ad = Ad::find($id);
        if ($ad->user_id == $user->id || $user->tokenCan('admin')) {
            if (!$ad) return $this->errorResponse('Ad not found!', 404);
            if (!$ad->delete()) return $this->errorResponse('An error occurred while deleting the Ad, please try again later', 500);
            return $this->successResponse('Ad has been successfully deleted');
        }
        return $this->errorResponse('Ad not available', 403);
    }
}
