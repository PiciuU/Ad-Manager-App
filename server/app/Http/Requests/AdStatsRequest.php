<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdStatsRequest extends FormRequest
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
        return [
            'id' => ['required', 'string'],
            'ad_id' => ['required', 'string'],
            'date' => ['sometimes', 'date'],
            'views' => ['required', 'integer'],
            'clicks' => ['required', 'integer'],
        ];
    }

    protected function update()
    {
        // $user

        if ($this->hasAdminPrivileges()) {
            return [
                'id' => ['sometimes', 'required', 'string'],
                'ad_id' => ['sometimes', 'required', 'string'],
                'date' => ['sometimes', 'sometimes', 'date'],
                'views' => ['sometimes', 'required', 'integer'],
                'clicks' => ['sometimes', 'required', 'integer'],
            ];
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->filled('adId')) {
            $this->merge([
                'ad_id' => $this->adId
            ]);
        }
    }
}
