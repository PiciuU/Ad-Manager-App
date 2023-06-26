<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LogCollection extends ResourceCollection
{
    protected $fields = [];

    public function returnFields($fields) {
        $this->fields = $fields;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($ad) use ($request) {
            $resource = new LogResource($ad);
            if (!empty($this->fields)) $resource->returnFields($this->fields);

            return $resource->toArray($request);
        })->all();
    }
}
