<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function store()
    {
        if ($this->hasAdminPrivileges()) {
            return [
                'user_id' => ['required', 'integer'],
                'title' => ['required', 'string'],
                'description' => ['required', 'string'],
                'date' => ['required', 'date'],
            ];
        }
    }

    protected function update()
    {
        if ($this->hasAdminPrivileges()) {
            return [
                'user_id' => ['sometimes', 'required', 'integer'],
                'title' => ['sometimes', 'required', 'string'],
                'description' => ['sometimes', 'required', 'string'],
                'date' => ['sometimes', 'required', 'date'],
            ];
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if (($this->hasAdminPrivileges()) && ($this->filled('userId'))) {
            $this->merge([
                'user_id' => $this->userId,
            ]);
        }
    }
}
