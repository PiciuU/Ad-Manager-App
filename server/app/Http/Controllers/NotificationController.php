<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\NotificationCollection;

class NotificationController extends Controller
{
    //Adminstrator może wyświetlić wszystkie powiadomienia, użytkownik tylko te, które są przypisane do niego
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $user_id = $user->id;

        if ($user->tokenCan('admin')) {
            $notification = new NotificationCollection(Notification::all());
        } else {
            $notification = new NotificationCollection(Notification::where('user_id', $user_id)->get());
        }

        return $this->successResponse('List of Notifications found', $notification);
    }


    //Tylko Administrator może tworzyć nowe powiadomienia
    public function store(NotificationRequest $request)
    {
        $notification = new NotificationResource(Notification::create($request->validated()));
        if (!$notification) {
            return $this->errorResponse('An error occurred during creating the notification, please try again later', 500);
        } else {
            return $this->successResponse('Ad has been created successfully', $notification);
        }
    }


    public function show($id)
    {
        $notification = Notification::find($id);

        if (!$notification) return $this->errorResponse('Notification not found.', 404);

        $user = auth()->user();

        if ($notification->user_id !== $user->id && !$user->tokenCan('admin')) {
            return $this->errorResponse('Notification not available', 403);
        }

        return $this->successResponse('Notification found', new NotificationResource($notification));
    }


    public function update(NotificationRequest $request, $id)
    {
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
        $notification = Notification::find($id);
        if ($notification->user_id == $user->id || $user->tokenCan('admin')) {
            if (!$notification) return $this->errorResponse('Nntification not found!', 404);
            if (!$notification->delete()) return $this->errorResponse('An error occurred while deleting the notification, please try again later', 500);
            return $this->successResponse('Notification has been successfully deleted');
        }
        return $this->errorResponse('Ad not available', 403);
    }


    public function isSeen($id)
    {
        $notification = Notification::find($id);

        if ($notification) {
            $notification->is_seen = true;
            $notification->save();
        }
        return $this->successResponse('Notification seen', new NotificationResource($notification));
    }
}
