<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class InvoiceRequest extends FormRequest
{
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
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
     * Get the validation rules for creating invoice
     *
     * @return array
     */
    protected function store(): array
    {
        if ($this->hasAdminPrivileges()) {
            return [
                'ad_id' => ['required', 'string'],
                'number' => ['required'],
                'price' => ['required', 'numeric', 'regex:/^\d{0,6}\.\d{2}$/'],
                'date' => ['required', 'date'],
                'status' => ['required', 'string']
            ];
        }

        return [];
    }

    /**
     * Get the validation rules for updating invoice
     *
     * @return array
     */
    protected function update(): array
    {
        return [
            'ad_id' => ['sometimes', 'string'],
            'number' => ['sometimes', 'required'],
            'price' => ['sometimes', 'required', 'numeric', 'regex:/^\d{0,6}\.\d{2}$/'],
            'date' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', 'required', 'string']
        ];
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

        if (($this->hasAdminPrivileges()) && ($this->filled('adId'))) {
            $this->merge([
                'ad_id' => $this->adId
            ]);
        }
    }
}
