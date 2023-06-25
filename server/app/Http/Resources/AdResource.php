<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\AdRequest;

class AdResource extends JsonResource
{
    protected $fields = [];

    public function returnFields($fields) {
        $this->fields = $fields;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($request->user()->tokenCan('admin')) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'userId' => $this->user_id,
                'status' => $this->status,
                'adStartDate' => $this->ad_start_date,
                'adEndDate' => $this->ad_end_date,
                'fileName' => $this->file_name,
                'fileType' => $this->file_type,
                'url' => $this->url,
            ];
        } else {
            return [
                'name' => $this->name,
                'status' => $this->status,
                'adStartDate' => $this->ad_start_date,
                'adEndDate' => $this->ad_end_date,
                'fileName' => $this->file_name,
                'fileType' => $this->file_type,
                'url' => $this->url,
            ];
        }
    }
}
