<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->user() && $request->user()->hasAdminPrivileges()) {
            return [
                'id' => $this->id,
                'userId' => $this->user_id,
                'adId' => $this->ad_id,
                'operationTags' => $this->operation_tags,
                'message' => $this->message,
                'notes' => $this->notes,
                'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return [];
    }
}
