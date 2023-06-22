<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdRequest;

class AdResource extends JsonResource
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
                'name' => $this->name,
                'userId' => $this->user_id,
                'status' => 'unpaid',
                'adStartDate' => $this->ad_start_date,
                'adEndDate' => $this->ad_end_date,
                'fileName' => $this->file_name,
                'fileType' => $this->file_type,
                'url' => $this->url,
            ];
        } else {
            return [
                'name' => $this->name,
                'status' => 'unpaid',
                'adStartDate' => $this->ad_start_date,
                'adEndDate' => $this->ad_end_date,
                'fileName' => $this->file_name,
                'fileType' => $this->file_type,
                'url' => $this->url,
            ];
        }
    }
}
