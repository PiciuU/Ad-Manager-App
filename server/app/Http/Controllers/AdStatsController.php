<?php

namespace App\Http\Controllers;

use App\Models\AdStats;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdStatsController extends Controller
{
    public function index()
    {
        // Pobierz aktualnie zalogowanego użytkownika
        $user = Auth::user();

        // Pobierz wszystkie statystyki reklam
        $statsQuery = AdStats::query();

        // Jeśli użytkownik nie jest administratorem, ogranicz wyniki do statystyk przypisanych do użytkownika
        if (!$user->isAdmin()) {
            $statsQuery->whereHas('ad', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $stats = $statsQuery->get();
        //odkomentowanie poniższej częsci spowoduje grupowanie wyników po ad_id


        // // Grupowanie statystyk reklam według ad_id
        // $groupedStats = $stats->groupBy('ad_id');

        // // Utworzenie tablicy wynikowej w oczekiwanym formacie JSON
        // $result = [];

        // foreach ($groupedStats as $adId => $adStats) {
        //     $adStatsData = [];

        //     foreach ($adStats as $stat) {
        //         $adStatsData[] = [
        //             'id' => $stat->id,
        //             'ad_id' => $stat->ad_id,
        //             'date' => $stat->date,
        //             'views' => $stat->views,
        //             'clicks' => $stat->clicks,
        //         ];
        //     }

        //     $result[$adId] = $adStatsData;
        // }

        return response()->json($stats);
        // return response()->json($result);
    }

    public function store(Request $request, $ad_id)
    {
        // Sprawdzenie uprawnień użytkownika
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
        }

        // Walidacja danych wejściowych
        $this->validate($request, [
            'date' => 'required',
            'views' => 'required|integer',
            'clicks' => 'required|integer',
        ]);

        // Twórz nowe statystyki reklamy
        $stats = new AdStats;
        $stats->ad_id = $ad_id;
        $stats->date = $request->input('date');
        $stats->views = $request->input('views');
        $stats->clicks = $request->input('clicks');
        $stats->save();

        return response()->json($stats);
    }

    public function show($ad_id, $stat_id = null)
    {
        // Sprawdzenie uprawnień użytkownika
        $user = auth()->user();

        // Pobranie reklamy o podanym ad_id
        $ad = Ad::findOrFail($ad_id);

        // Jeśli użytkownik nie jest administratorem i nie jest właścicielem reklamy, zwróć odpowiedni komunikat
        if (!$user->isAdmin() && $ad->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($stat_id) {
            // Jeśli podano $stat_id, pobierz konkretną statystykę
            $stat = AdStats::where('ad_id', $ad_id)->findOrFail($stat_id);
            return response()->json($stat);
        } else {
            // Jeśli nie podano $stat_id, pobierz wszystkie statystyki dla danego $ad_id
            $stats = AdStats::where('ad_id', $ad_id)->get();
            return response()->json($stats);
        }
    }

    public function update(Request $request, $stat_id)
    {
        // Sprawdzenie uprawnień użytkownika
        $user = auth()->user();

        // Sprawdzenie, czy użytkownik jest administratorem
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized access'], 403);
        }

        // Pobranie statystyk reklamy o podanym stat_id
        $stats = AdStats::findOrFail($stat_id);

        // Aktualizacja danych statystyk reklamy
        $stats->date = $request->has('date') ? $request->input('date') : $stats->date;
        $stats->views = $request->has('views') ? $request->input('views') : $stats->views;
        $stats->clicks = $request->has('clicks') ? $request->input('clicks') : $stats->clicks;

        $stats->save();

        return response()->json($stats);
    }

    // Poniższa metoda na razie nie działa
    public function delete($stat_id)
    {
        $stat = AdStats::findOrFail($stat_id);

        // Sprawdzenie uprawnień użytkownika
        if (auth()->user()->isAdmin()) {
            $stat->delete();

            return response()->json(['message' => 'Invoice deleted']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
