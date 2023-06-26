<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;

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
    public function toArray(Request $request)
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
                'name' => $this->name,
                'userId' => $this->user_id,
                'userLogin' => $this->user->login,
                'status' => $this->status,
                'adStartDate' => $this->ad_start_date,
                'adEndDate' => $this->ad_end_date,
                'fileName' => $this->file_name,
                'fileType' => $this->file_type,
                'url' => $this->url,
                'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
                'updatedAt' => $this->updated_at->format('Y-m-d H:i:s'),
            ];
        } else {
            return [
                'id' => $this->id,
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
