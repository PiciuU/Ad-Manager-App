<?php

namespace App\Http\Controllers;

use App\Models\AdStats;
use App\Models\Ad;
use App\Http\Resources\AdStatsCollection;
use App\Http\Resources\AdStatsResource;
use App\Http\Requests\AdStatsRequest;
use App\Http\Controllers\InvoiceController;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class AdStatsController extends Controller
{
    private function createStatement($format, $date)
    {
        $formats = [
            'week' => ["%Y-%m-%d", "Y-m-d", "d M Y"],
            'month' => ["%Y-%m-%d", "Y-m-d", "d M Y"],
            'year' => ["%Y-%m", "Y-m", "M Y"],
            'monthrange' => ["%Y-%m", "Y-m", "M Y"],
        ];

        [$dateFormat, $valueFormat, $translatedFormat] = $formats[$format];

        $condition = match ($format) {
            'week' => "DATE_FORMAT(date, '%Y-%m-%d') BETWEEN '{$date}' AND DATE_ADD('{$date}', INTERVAL 6 day)",
            'month' => "DATE_FORMAT(date, '%Y-%m') = '{$date}'",
            'year' => "DATE_FORMAT(date, '%Y') = '{$date}'",
            'monthrange' => "DATE_FORMAT(date, '%Y-%m') BETWEEN '{$date[0]}' AND '{$date[1]}'",
        };

        return (object) compact('dateFormat', 'valueFormat', 'translatedFormat', 'condition');
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
     * Store or update ad statistics based on provided day.
     * This method is accessible only to administrators.
     *
     * @param int $id The ID of the ad.
     * @param \App\Http\Requests\AdStatsRequest $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function storeAsAdmin($id, AdStatsRequest $request)
    {
        if ($this->hasAccess()) $ad = Ad::find($id);
        else $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $adStats = AdStats::where('ad_id', $ad->id)
            ->where('date', $request->date)
            ->first();

        if ($adStats) {
            $adStats->views += $request->validated()['views'];
            $adStats->clicks += $request->validated()['clicks'];
            $adStats->save();
        } else {
            $adStats = AdStats::create($request->validated());
        }

        return $this->successResponse('Ad stats has been successfully created.', $adStats);
    }

    /**
     * =====================
     *     USER SECTION
     * =====================
     */

     /**
     * Retrieve summary of ad statistics for the authenticated user.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function summary()
    {
        $ads = auth()->user()->ads()->get();

        if (!$ads) return $this->errorResponse("Ad not found.", 404);

        $viewsData = [];
        $clicksData = [];

        $numOfAllAds = $ads->count();

        $numOfActiveAds = $ads->where('status', 'active')
            ->count();

        $numOfInteractions = AdStats::selectRaw('CAST(IFNULL(SUM(views), 0) AS UNSIGNED) AS num_of_today_views, CAST(IFNULL(SUM(clicks), 0) AS UNSIGNED) AS num_of_today_clicks')
            ->whereIn('ad_id', $ads->pluck('id'))
            ->where('date', '>=', date('Y-m-d'))
            ->first();

        $summaryData = (object) [
            'num_of_all_ads' => $numOfAllAds,
            'num_of_active_ads' => $numOfActiveAds,
            'num_of_today_views' => $numOfInteractions->num_of_today_views ?? 0,
            'num_of_today_clicks' => $numOfInteractions->num_of_today_clicks ?? 0
        ];

        $queryResult = AdStats::selectRaw('SUM(views) AS num_of_views, SUM(clicks) AS num_of_clicks, DATE_FORMAT(date, "%Y-%m") AS formatted_date')
            ->whereIn('ad_id', $ads->pluck('id'))
            ->whereRaw('DATE_FORMAT(date, "%Y-%m") > DATE_FORMAT(CURDATE() - INTERVAL 12 month, "%Y-%m")')
            ->groupBy('formatted_date')
            ->get();

        foreach($queryResult as $element) {
            $element->formatted_date = ucfirst(Carbon::createFromFormat('Y-m', $element->formatted_date)->translatedFormat('M Y'));
            $viewsData[$element->formatted_date] = intval($element->num_of_views);
            $clicksData[$element->formatted_date] = intval($element->num_of_clicks);
        }

        if (end($viewsData) > end($clicksData)) $clickThroughRate = round(end($clicksData) / end($viewsData) * 100, 2);
        else $clickThroughRate = 0;

        return $this->successResponse('Summary', ['summary' => $summaryData, 'views' => $viewsData, 'clicks' => $clicksData, 'ctr' => $clickThroughRate]);
    }

    /**
     * Retrieve ad statistics for the specified ad.
     *
     * @param int $id
     * @param \App\Http\Requests\AdStatsRequest
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id, AdStatsRequest $request)
    {
        if ($this->hasAccess()) $ad = Ad::find($id);
        else $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $statement = $this->createStatement($request->validated()['format'], $request->validated()['date']);

        $queryResult = $ad->adStats()
            ->selectRaw('SUM(views) AS num_of_views, SUM(clicks) AS num_of_clicks, DATE_FORMAT(date, ?) AS formatted_date', [$statement->dateFormat])
            ->whereRaw($statement->condition)
            ->groupBy('formatted_date')
            ->get();

        $viewsData = [];
        $clicksData = [];
        $additionalData = (object) [
            'all_views' => 0,
            'most_views' => ['views' => 0, 'date' => 'Brak danych'],
            'least_views' => ['views' => 0, 'date' => 'Brak danych'],
            'all_clicks' => 0,
            'most_clicks' => ['clicks' => 0, 'date' => 'Brak danych'],
            'least_clicks' => ['clicks' => 0, 'date' => 'Brak danych'],
        ];

        foreach($queryResult as $element) {
            $date = ucfirst(Carbon::createFromFormat($statement->valueFormat, $element->formatted_date)->translatedFormat($statement->translatedFormat));

            $views = intval($element->num_of_views);
            $clicks = intval($element->num_of_clicks);

            $additionalData->all_views += $views;
            $additionalData->all_clicks += $clicks;

            $additionalData->least_views['views'] = $additionalData->all_views;
            $additionalData->least_clicks['clicks'] = $additionalData->all_clicks;

            if ($views > $additionalData->most_views['views']) {
                $additionalData->most_views = ['views' => $views, 'date' => $date];
            }

            if ($views < $additionalData->least_views['views']) {
                $additionalData->least_views = ['views' => $views, 'date' => $date];
            }

            if ($clicks > $additionalData->most_clicks['clicks']) {
                $additionalData->most_clicks = ['clicks' => $clicks, 'date' => $date];
            }

            if ($clicks < $additionalData->least_clicks['clicks']) {
                $additionalData->least_clicks = ['clicks' => $clicks, 'date' => $date];
            }

            $viewsData[$date] = $views;
            $clicksData[$date] = $clicks;
        }

        return $this->successResponse('Ad stats has been successfully found.', ['views' => $viewsData, 'clicks' => $clicksData, 'summary' => $additionalData]);
    }









    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $user = auth()->user();

    //     if ($user->tokenCan('admin')) {
    //         $ads = new AdStatsCollection(AdStats::all());
    //     } else {
    //         $ads = new AdStatsCollection(AdStats::whereHas('ad', function ($query) use ($user) {
    //             $query->whereHas('user', function ($query) use ($user) {
    //                 $query->where('id', $user->id);
    //             });
    //         })->with('ad')->get());

    //         if ($ads->isEmpty()) {
    //             return $this->errorResponse('No stats found for the user.', 404);
    //         }

    //         return $this->successResponse('List of stats found', $ads);
    //     }

    //     return $this->successResponse('List of stats found', $ads);
    // }


    /**
     * Store a newly created ad in storage.
     *
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    // public function store(AdStatsRequest $request)
    // {
    //     $stats = new AdStatsResource(AdStats::create($request->validated()));
    //     if (!$stats) {
    //         return $this->errorResponse('An error occurred during creating the stats, please try again later', 500);
    //     } else {
    //         print_r($stats->toArray);
    //         return $this->successResponse('Stats have been created successfully', $stats);
    //     }
    // }

    // /**
    //  * Display the specified exercise.
    //  *
    //  * @param  int  $id
    //  * @return \App\Http\Traits\ResponseTrait
    //  *
    //  */
    // public function show($ad_id, $stat_id = null)
    // {
    //     // Sprawdzenie uprawnień użytkownika
    //     $user = auth()->user();

    //     // Pobranie reklamy o podanym ad_id
    //     $ad = Ad::find($ad_id);

    //     if (!$ad) return $this->errorResponse('Ad doesn\'t exist!', 404);

    //     // Jeśli użytkownik nie jest administratorem i nie jest właścicielem reklamy, zwróć odpowiedni komunikat
    //     if (!$user->isAdmin() && $ad->user_id !== $user->id) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     if ($stat_id) {
    //         // Jeśli podano $stat_id, pobierz konkretną statystykę
    //         return $this->successResponse('Stats found', new AdStatsResource($stat_id));
    //     } else {
    //         // Jeśli nie podano $stat_id, pobierz wszystkie statystyki dla danego $ad_id
    //         $stats = AdStats::where('ad_id', $ad_id)->get();
    //         return $this->successResponse('Stats found', new AdStatsCollection($stats));
    //     }
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update($id, AdStatsRequest $request)
    // {
    //     $user = auth()->user();
    //     if ($user->tokenCan('admin')) $stats = AdStats::find($id);

    //     // if ($user->tokenCan('admin')) $stats = AdStats::find($id);
    //     // else $stats = $user->ads()->find($id);

    //     if (!$stats) return $this->errorResponse('Stats not found', 404);


    //     if (!$stats->update($request->validate())) return $this->errorResponse('An error occurred while updating the stats, please try again later', 500);


    //     return $this->successResponse('Stats has been successfully updated', new AdStatsResource($stats));
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    // public function destroy($id)
    // {
    //     $user = auth()->user();
    //     $stats = AdStats::find($id);

    //     if (!$stats) return $this->errorResponse('Stats not found!', 404);

    //     $adId = $stats->ad_id;
    //     $ad = Ad::find($adId);
    //     if ($ad->user_id == $user->id || $user->tokenCan('admin')) {
    //         if (!$ad->delete()) return $this->errorResponse('An error occurred while deleting stats, please try again later', 500);
    //         return $this->successResponse('Stats have been successfully deleted');
    //     }
    //     return $this->errorResponse('Stats not available', 403);
    // }
}
