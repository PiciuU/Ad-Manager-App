<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class NotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function hasAdminPrivileges(): bool
    {
        return $this->user()->tokenCan('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return $this->isMethod('POST') ? $this->store() : $this->update();
    }

    /**
     * Get the validation rules for creating notification.
     *
     * @return array
     */
    protected function store()
    {
        if ($this->hasAdminPrivileges()) {
            return [
                'user_id' => ['required', 'integer'],
                'ad_id' => ['sometimes', 'required', 'integer'],
                'title' => ['required', 'string'],
                'description' => ['required', 'string'],
                'date' => ['required', 'date'],
            ];
        }

        return [];
    }

    /**
     * Get the validation rules for updating notification.
     *
     * @return array
     */
    protected function update()
    {
        if ($this->hasAdminPrivileges()) {
            return [
                'user_id' => ['sometimes', 'required', 'integer'],
                'ad_id' => ['sometimes', 'required', 'integer'],
                'title' => ['sometimes', 'required', 'string'],
                'description' => ['sometimes', 'required', 'string'],
                'date' => ['sometimes', 'required', 'date'],
                'is_seen' => ['sometimes', 'required', 'integer', Rule::in([0, 1])]
            ];
        }

        return [
            'is_seen' => ['required', 'integer', Rule::in([0, 1])]
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->filled('userId')) {
            $this->merge([
                'user_id' => $this->userId,
            ]);
        }

        if ($this->filled('adId')) {
            $this->merge([
                'ad_id' => $this->adId,
            ]);
        }

        if ($this->filled('isSeen')) {
            $this->merge([
                'is_seen' => $this->isSeen
            ]);
        }
    }
}
