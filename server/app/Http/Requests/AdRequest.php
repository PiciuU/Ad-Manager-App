<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'user_id' => ['required', 'integer'],
            'status' => ['sometimes', 'required', Rule::in(['unpdaid', 'paid', 'expired', 'inactive'])],
            'ad_start_date' => ['required', 'date'],
            'ad_end_date' => ['required', 'date'],
            'file_name' => ['required', 'string'],
            'file_type' => ['required', Rule::in(['img', 'video'])],
            'url' => ['required', 'string']
        ];
    }

    protected function update()
    {
        $rules = [
            'name' => ['sometimes', 'required', 'string'],
            'status' => ['sometimes', 'required'],
        ];
        if ($this->hasAdminPrivileges()) {
            $rules = array_merge($rules, [
                'name' => ['sometimes', 'required', 'string'],
                'user_id' => ['sometimes', 'required', 'integer'],
                'status' => ['sometimes', 'required', Rule::in(['unpdaid', 'paid', 'expired', 'inactive'])],
                'ad_start_date' => ['sometimes', 'required', 'date'],
                'ad_end_date' => ['sometimes', 'required', 'date'],
                'file_name' => ['sometimes', 'required', 'string'],
                'file_type' => ['sometimes', 'required', Rule::in(['img', 'video'])],
                'url' => ['sometimes', 'required', 'string']
            ]);
        }
        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->isMethod('POST')) {
            $this->merge([
                'user_id' => $this->user()->id,
            ]);
        } else if ($this->filled('userId')) {
            $this->merge([
                'user_id' => $this->userId,
            ]);
        } else if (($this->hasAdminPrivileges()) && ($this->filled('userId'))) {
            $this->merge([
                'user_id' => $this->userId,
            ]);
        }

        if ($this->filled('adStartDate')) {
            $this->merge([
                'ad_start_date' => $this->adStartDate
            ]);
        }
        if ($this->filled('adEndDate')) {
            $this->merge([
                'ad_end_date' => $this->adEndDate
            ]);
        }
        if ($this->filled('fileName')) {
            $this->merge([
                'file_name' => $this->fileName
            ]);
        }
        if ($this->filled('fileType')) {
            $this->merge([
                'file_type' => $this->fileType
            ]);
        }
    }
}
