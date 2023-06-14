<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
    public function store()
    {
        return [
            'name' => 'required|string|max:255',
            'user_id' => 'required|int',
            'status' => 'required|in:unpaid,active,inactive,expired',
            'ad_start_date' => 'required|date',
            'ad_end_date' => 'required|date|after:ad_start_date',
            'file_name' => 'required|string|max:255',
            'file_type' => 'required|in:img,video',
            'url' => 'nullable|string|max:255',
        ];
    }

    public function update()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ];
        if ($this->hasAdminPrivileges()) {
            $rules = array_merge($rules, [
                'name' => 'required|string|max:255',
                'user_id' => 'required|string|max:255',
                'status' => 'required|in:unpaid,active,inactive,expired',
                'ad_start_date' => 'required|date',
                'ad_end_date' => 'required|date|after:ad_start_date',
                'file_name' => 'required|string|max:255',
                'file_type' => 'required|in:img,video',
                'url' => 'nullable|string|max:255',
            ]);
            return $rules;
        }
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
        } else if ($this->hasAdminPrivileges()) {
            $this->merge([
                'user_id' => $his->userId,
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
