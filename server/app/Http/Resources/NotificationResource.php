<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;

class NotificationResource extends JsonResource
{
    protected $fields = [];

    public function returnFields($fields) {
        $this->fields = $fields;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Return only specified fields
        if (!empty($this->fields)) {
            $result = [];
            foreach ($this->fields as $field) {
                $result[Str::camel($field)] = $this->$field;
            }
            return $result;
        }

        if ($request->user()->tokenCan('admin')) {
            return [
                'id' => $this->id,
                'userId' => $this->user_id,
                'adId' => $this->ad_id,
                'date' => $this->date,
                'title' => $this->title,
                'description' => $this->description,
                'isSeen' => $this->is_seen,
                'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
                'updatedAt' => $this->updated_at->format('Y-m-d H:i:s'),
            ];
        } else {
            return [
                'id' => $this->id,
                'adId' => $this->ad_id,
                'date' => $this->date,
                'title' => $this->title,
                'description' => $this->description,
                'isSeen' => $this->is_seen
            ];
        }
    }
}
