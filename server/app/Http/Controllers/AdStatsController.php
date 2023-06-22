<?php

namespace App\Http\Controllers;

use App\Models\AdStats;
use App\Models\Ad;
use App\Http\Resources\AdStatsCollection;
use App\Http\Resources\AdStatsResource;
use App\Http\Requests\AdStatsRequest;
use App\Http\Controllers\InvoiceController;


class AdStatsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->tokenCan('admin')) {
            $ads = new AdStatsCollection(AdStats::all());
        } else {
            $ads = new AdStatsCollection(AdStats::whereHas('ad', function ($query) use ($user) {
                $query->whereHas('user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                });
            })->with('ad')->get());

            if ($ads->isEmpty()) {
                return $this->errorResponse('No stats found for the user.', 404);
            }

            return $this->successResponse('List of stats found', $ads);
        }

        return $this->successResponse('List of stats found', $ads);
    }


    /**
     * Store a newly created ad in storage.
     *
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(AdStatsRequest $request)
    {
        $stats = new AdStatsResource(AdStats::create($request->validated()));
        if (!$stats) {
            return $this->errorResponse('An error occurred during creating the stats, please try again later', 500);
        } else {
            print_r($stats->toArray);
            return $this->successResponse('Stats have been created successfully', $stats);
        }
    }

    /**
     * Display the specified exercise.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     *
     */
    public function show($ad_id, $stat_id = null)
    {
        // Sprawdzenie uprawnień użytkownika
        $user = auth()->user();

        // Pobranie reklamy o podanym ad_id
        $ad = Ad::find($ad_id);

        if (!$ad) return $this->errorResponse('Ad doesn\'t exist!', 404);

        // Jeśli użytkownik nie jest administratorem i nie jest właścicielem reklamy, zwróć odpowiedni komunikat
        if (!$user->isAdmin() && $ad->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($stat_id) {
            // Jeśli podano $stat_id, pobierz konkretną statystykę
            return $this->successResponse('Stats found', new AdStatsResource($stat_id));
        } else {
            // Jeśli nie podano $stat_id, pobierz wszystkie statystyki dla danego $ad_id
            $stats = AdStats::where('ad_id', $ad_id)->get();
            return $this->successResponse('Stats found', new AdStatsCollection($stats));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, AdStatsRequest $request)
    {
        $user = auth()->user();
        if ($user->tokenCan('admin')) $stats = AdStats::find($id);

        // if ($user->tokenCan('admin')) $stats = AdStats::find($id);
        // else $stats = $user->ads()->find($id);

        if (!$stats) return $this->errorResponse('Stats not found', 404);


        if (!$stats->update($request->validate())) return $this->errorResponse('An error occurred while updating the stats, please try again later', 500);


        return $this->successResponse('Stats has been successfully updated', new AdStatsResource($stats));
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
        $stats = AdStats::find($id);

        if (!$stats) return $this->errorResponse('Stats not found!', 404);

        $adId = $stats->ad_id;
        $ad = Ad::find($adId);
        if ($ad->user_id == $user->id || $user->tokenCan('admin')) {
            if (!$ad->delete()) return $this->errorResponse('An error occurred while deleting stats, please try again later', 500);
            return $this->successResponse('Stats have been successfully deleted');
        }
        return $this->errorResponse('Stats not available', 403);
    }
}
