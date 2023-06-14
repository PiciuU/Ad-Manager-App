<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdModel;
use App\Models\Ad;
use App\Http\Resources\AdCollection;
use App\Http\Resources\AdResource;
use App\Http\Requests\AdRequest;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Auth;

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
     * @param  \App\Http\Requests\ExerciseRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(AdRequest $request)
    {
        $ad = new AdResource(Ad::create($request->validated()));

        if (!$ad) {
            return $this->errorResponse('An error occurred during creating the Ad, please try again later', 500);
        } else {
            // $invoiceController = new InvoiceController();
            // $invoiceController->createFromAd()
            return $this->successResponse('Ad has been created successfully', $ad);
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
        $ad = Ad::find($id);
        $user_id = $user->id;

        if ($user->tokenCan('admin')) $ad = Ad::find($id);
        else $ad = new AdCollection(Ad::where('user_id', $user_id));

        if (!$ad) return $this->errorResponse('Ad not found', 404);

        if (!$ad->update($request->validated())) return $this->errorResponse('An error occurred while updating the Ad, please try again later', 500);

        return $this->successResponse('Ad has been successfully updated', new AdResource($ad));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $ad = Ad::find($id);

        if ($ad->user_id === $user->id && !$user->tokenCan('admin')) {
            if (!$ad->delete()) return $this->errorResponse('An error occurred while deleting the Ad, please try again later', 500);
            return $this->successResponse('Ad has been successfully deleted');
        }
        return $this->errorResponse('Ad not available', 403);

        if (!$ad) return $this->errorResponse('Ad not found!', 404);
    }
}
