<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Include additional fields if the user is an administrator
        // $additionalFields = [];
        if ($request->user()->tokenCan('admin')) {
            return [
                'id' => $this->id,
                'adId' => $this->ad_id,
                'date' => $this->date,
                'views' => $this->views,
                'clicks' => $this->clicks
            ];
        } else {
            return [
                'id' => $this->id,
                'adId' => $this->ad_id,
                'date' => $this->date,
                'views' => $this->views,
                'clicks' => $this->clicks
            ];
        }
    }
}
