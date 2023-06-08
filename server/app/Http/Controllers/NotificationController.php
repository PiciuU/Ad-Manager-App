<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    //Adminstrator może wyświetlić wszystkie powiadomienia, użytkownik tylko te, które są przypisane do niego
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $notifications = Notification::all();
        } else {
            $notifications = $user->notifications;
        }

        return response()->json($notifications);
    }


    //Tylko Administrator może tworzyć nowe powiadomienia
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
        }

        $validatedData = $request->validate([
            'user_id' => 'required',
            'title' => 'required',
            'description' => 'required'
        ]);

        $currentDate = new \DateTime();

        $notification = new Notification();
        $notification->user_id = $validatedData['user_id'];
        $notification->title = $validatedData['title'];
        $notification->description = $validatedData['description'];
        $notification->date = $currentDate->format('Y-m-d H:i:s');;
        $notification->is_seen = false;

        $notification->save();

        return response()->json($notification, 201);
    }


    public function show($id)
    {
        $notification = Notification::findOrFail($id);

        //Dane powiadomienie może zobaczyć tylko Administrator albo użytkownik, do którego jest przypisane
        if (!(auth()->user()->isAdmin() || $notification->user_id === auth()->user()->id)) {
            return response()->json(['message' => 'You are not authorized to view this notification.'], 403);
        }

        return response()->json($notification);
    }


    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $user = Auth::user();

        // Sprawdzenie uprawnień użytkownika, tylko Administrator może modyfikować
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
        }

        // Walidacja danych wejściowych
        $this->validate($request, [
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'is_seen' => 'sometimes|string',
        ]);

        // Można zmodyfikować te 3 pola pojedynczo albo wszytskie naraz
        $notification->title = $request->has('title') ? $request->input('title') : $notification->title;
        $notification->description = $request->has('description') ? $request->input('description') : $notification->description;
        $notification->is_seen = $request->has('is_seen') ? $request->input('is_seen') : $notification->is_seen;

        $notification->save();

        return response()->json($notification);
    }


    // Tylko Administrator może całkowicie usunąć dane powiadomienie
    public function delete($id)
    {
        $notification = Notification::findOrFail($id);

        // Sprawdzenie uprawnień użytkownika
        if (auth()->user()->isAdmin()) {
            $notification->delete();

            return response()->json(['message' => 'Notification deleted']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }


    public function isSeen($id)
    {
        $notification = Notification::findOrFail($id);

        $notification->is_seen = true;
        $notification->save();

        return response()->json($notification);
    }
}
