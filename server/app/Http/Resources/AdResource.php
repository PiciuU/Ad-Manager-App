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
                'name' => $this->name,
                'id' => $this->id,
                'adStartDate' => $this->ad_start_date,
                'adEndDate' => $this->ad_end_date,
                'fileName' => $this->file_name,
                'fileType' => $this->file_type,
                'url' => $this->url,
                'status' => 'unpaid',
            ];
        } else {
            return [
                'name' => $this->name,
                'adStartDate' => $this->ad_start_date,
                'adEndDate' => $this->ad_end_date,
                'fileName' => $this->file_name,
                'fileType' => $this->file_type,
                'url' => $this->url,
                'status' => 'unpaid',
            ];
        }
        response()->json(['message' => 'Unauthorized'], 401);
    }
}
