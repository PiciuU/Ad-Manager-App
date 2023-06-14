<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ad;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
    

    protected function store(): array{
        return [
            'ad_id' => ['sometimes', 'string'],
            'number' => ['sometimes'],
            'price' => ['required','double'],
            'date' => ['required', 'date'],
            'status' => ['sometimes', 'string']
        ];

    }

    protected
}