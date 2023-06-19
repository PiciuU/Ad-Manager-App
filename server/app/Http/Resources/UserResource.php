<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
                'userRoleId' => $this->user_role_id,
                'name' => $this->name,
                'login' => $this->login,
                'email' => $this->email,
                'activationKey' => $this->activation_key,
                'nip' => $this->nip,
                'address' => $this->address,
                'postalCode' => $this->postal_code,
                'country' => $this->country,
                'companyEmail' => $this->company_email,
                'companyPhone' => $this->company_phone,
                'representative' => $this->representative,
                'representativePhone' => $this->representative_phone,
                'notes' => $this->notes,
                'isBanned' => $this->is_banned,
                'banReason' => $this->ban_reason,
                'activatedAt' => $this->activated_at
            ];
        } else {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'login' => $this->login,
                'email' => $this->email,
                'nip' => $this->nip,
                'address' => $this->address,
                'postalCode' => $this->postal_code,
                'country' => $this->country,
                'companyEmail' => $this->company_email,
                'companyPhone' => $this->company_phone,
                'representative' => $this->representative,
                'representativePhone' => $this->representative_phone,
            ];
        }
    }
}
