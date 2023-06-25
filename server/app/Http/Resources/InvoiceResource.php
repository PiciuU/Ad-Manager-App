<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use DateTime;



class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        if ($request->user()->tokenCan('admin')) {
            return [
                'id' => $this->id,
                'adId' => $this->ad_id,
                'price' => $this->price,
                'date' => $this->date,
                'status' => $this->status,
                'number' => $this->number,
                'notes' => $this->notes
            ];
        } else {
            return [
                'adId' => $this->ad_id,
                'price' => $this->price,
                'date' => $this->date,
                'status' => $this->status,
                'number' => $this->number,
                'notes' => $this->notes
            ];
        }
    }
}
