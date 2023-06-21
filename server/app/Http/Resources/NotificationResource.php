<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Include additional fields if the user is an administrator
        // $additionalFields = [];
        if ($request->user()->tokenCan('admin')) {
            return [
                'userId' => $this->user_id,
                'title' => $this->title,
                'description' => $this->description,
                'date' => date('Y-m-d H:i:s'),
            ];
        } else {
            return [
                'title' => $this->title,
                'description' => $this->description,
                'date' => date('Y-m-d H:i:s'),
            ];
        }
        response()->json(['message' => 'Unauthorized'], 401);
    }
}
