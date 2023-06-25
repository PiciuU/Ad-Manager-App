<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use Carbon\Carbon;

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
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the method name for the current request action.
     *
     * @return string
     */
    protected function getMethodName(): string
    {
        $action = $this->route()->getActionMethod();
        return Str::camel($action);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $methodName = $this->getMethodName();

        if ($methodName === 'store') return $this->store();
        elseif ($methodName === 'update') return $this->update();
        elseif ($methodName === 'renew') return $this->renew();

        return [];
    }

    /**
     * Get the validation rules for creating ad
     *
     * @return array
     */
    protected function store()
    {
        /* Internal Rule - Prevent injection by direct http request */
        $startDate = Carbon::parse($this->ad_start_date);
        $endDate = Carbon::parse($this->ad_end_date);
        $diffInDays = $endDate->diffInDays($startDate);

        if ($diffInDays > 30) {
            $endDate = $startDate->copy()->addDays(30)->format('Y-m-d');
            $this->merge(['ad_end_date' => $endDate]);
        }

        return [
            'name' => ['required', 'string'],
            'user_id' => ['required', 'integer'],
            'status' => ['required', Rule::in(['unpaid'])],
            'ad_start_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:'. date('Y-m-d')],
            'ad_end_date' => ['required', 'date', 'date_format:Y-m-d', 'after:ad_start_date'],
            'url' => ['sometimes', 'nullable', 'string']
        ];
    }

    /**
     * Get the validation rules for updating ad
     *
     * @return array
     */
    protected function update()
    {
        $rules = [
            'name' => ['sometimes', 'required', 'string'],
            'url' => ['sometimes', 'nullable', 'string'],
        ];

        // if ($this->hasAdminPrivileges()) {
        //     $rules = array_merge($rules, [
        //         'name' => ['sometimes', 'required', 'string'],
        //         'user_id' => ['sometimes', 'required', 'integer'],
        //         'status' => ['sometimes', 'required', Rule::in(['unpdaid', 'paid', 'expired', 'inactive'])],
        //         'ad_start_date' => ['sometimes', 'required', 'date'],
        //         'ad_end_date' => ['sometimes', 'required', 'date'],
        //         'file_name' => ['sometimes', 'required', 'string'],
        //         'file_type' => ['sometimes', 'required', Rule::in(['img', 'video'])],
        //         'url' => ['sometimes', 'required', 'string']
        //     ]);
        // }
        return $rules;
    }

    /**
     * Get the validation rules for renewing ad.
     *
     * @return array
     */
    protected function renew()
    {
        /* Internal Rule - Prevent injection by direct http request */
        $startDate = Carbon::parse($this->ad_start_date);
        $endDate = Carbon::parse($this->ad_end_date);
        $diffInDays = $endDate->diffInDays($startDate);

        if ($diffInDays > 30) {
            $endDate = $startDate->copy()->addDays(30)->format('Y-m-d');
            $this->merge(['ad_end_date' => $endDate]);
        }

        return [
            'ad_start_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:'. date('Y-m-d')],
            'ad_end_date' => ['required', 'date', 'date_format:Y-m-d', 'after:ad_start_date'],
        ];
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
            $this->merge([
                'status' => 'unpaid',
            ]);
        } else if ($this->hasAdminPrivileges() && $this->filled('userId')) {
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
