<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;

class UserResource extends JsonResource
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

        $token = $this->lastUsedToken()->first();
        if ($token && $token->last_used_at) $recentlyActiveAt = $token->last_used_at->format('Y-m-d H:i:s');
        else $recentlyActiveAt = null;

        if ($request->user()->tokenCan('admin')) {
            return [
                'id' => $this->id,
                'userRoleId' => $this->user_role_id,
                'userRole' => $this->userRole->name,
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
                'recentlyActiveAt' =>$recentlyActiveAt,
                'activatedAt' => $this->activated_at,
                'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
                'updatedAt' => $this->updated_at->format('Y-m-d H:i:s'),
                'hasUnseenNotification' => $this->notifications()->where('is_seen', 0)->exists()
            ];
        } else {
            return [
                'id' => $this->id,
                'userRole' => $this->userRole->name,
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
                'hasUnseenNotification' => $this->notifications()->where('is_seen', 0)->exists()
            ];
        }
    }
}
