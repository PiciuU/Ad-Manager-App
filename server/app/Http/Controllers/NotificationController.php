<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\NotificationCollection;

class NotificationController extends Controller
{
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
     * Retrieve a paginated list of notifications.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->orderBy('date', 'desc')->paginate(10);

        $responseData = [
            'current_page' => $notifications->currentPage(),
            'entries' => new NotificationCollection($notifications->items()),
            'per_page' => $notifications->perPage(),
            'total' => $notifications->total(),
        ];

        return $this->successResponse("Notifications has been successfully found.", $responseData);
    }

    /**
     * Retrieve the latest notifications.
     * The method retrieves a maximum of 3 unseen notifications and fills the remaining slots with seen notifications.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function latest()
    {
        $unseenNotifications = auth()->user()->notifications()->where('is_seen', 0)->orderBy('date', 'desc')->take(3)->get();
        $seenNotifications = auth()->user()->notifications()->where('is_seen', 1)->orderBy('date', 'desc')->take(3 - $unseenNotifications->count())->get();

        $mergedNotifications = new NotificationCollection($unseenNotifications->concat($seenNotifications));

        return $this->successResponse("Notifications has been successfully found.", $mergedNotifications);
    }

    /**
     * Store a new notification.
     * This method is accessible only to administrators.
     *
     * @param  \App\Http\Requests\NotificationRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(NotificationRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $notification = Notification::create($request->validated());

        if (!$notification) return $this->errorResponse("An error occurred while creating the notification, try again later.", 500);

        return $this->successResponse("Notification has been successfully created.", new NotificationResource($notification));
    }

    /**
     * Mark a notification as seen or unseen.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function isSeen($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if (!$notification) return $this->errorResponse("Notification not found.", 404);

        $notification->update([
            'is_seen' => (int)!$notification->is_seen
        ]);

        $state = $notification->is_seen == true ? "seen" : "unseen";

        return $this->successResponse("Notification has been successfully marked as $state.", new NotificationResource($notification));
    }

    /**
     * Show a specific notification by its ID.
     * This method is accessible only to administrators.
     * (Currently not used)
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $notification = Notification::find($id);

        if (!$notification) return $this->errorResponse("Notification not found.", 404);

        return $this->successResponse("Notification has been successfully found.", new NotificationResource($notification));
    }

    /**
     * Delete a specific notification by its ID.
     * This method is accessible only to administrators.
     * (Currently not used)
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $notification = Notification::find($id);
        if ($notification->user_id == $user->id || $user->tokenCan('admin')) {
            if (!$notification) return $this->errorResponse("Notification not found.", 404);
            if (!$notification->delete()) return $this->errorResponse("An error occurred while deleting the notification, please try again later.", 500);
            return $this->successResponse("Notification has been successfully deleted.");
        }
        return $this->errorResponse("Ad not found.", 403);
    }
}
