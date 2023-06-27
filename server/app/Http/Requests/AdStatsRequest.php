<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

        if ($methodName === 'show') return $this->show();

        if ($this->hasAdminPrivileges()) {
            if ($methodName === 'storeAsAdmin') return $this->storeAsAdmin();
        }

        return [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function show()
    {
        $rules = [
            'format' => ['required', 'string', Rule::in(['week', 'month', 'year', 'monthrange'])],
            'date' => ['required',
                Rule::when($this->input('format') == 'week', ['date_format:Y-m-d']),
                Rule::when($this->input('format') == 'month', ['date_format:Y-m']),
                Rule::when($this->input('format') == 'year', ['date_format:Y']),
            ],
        ];

        if ($this->input('format') === 'monthrange') {
            $rules['date'][] = 'array';
            $rules['date'][] = function ($attribute, $value, $fail) {
                $dates = [
                    'date_from' => $value[0],
                    'date_to' => $value[1],
                ];

                $validator = validator($dates, [
                    'date_from' => 'required|date_format:Y-m',
                    'date_to' => 'required|date_format:Y-m|after:date_from',
                ]);

                if ($validator->fails()) {
                    $fail($validator->errors()->first());
                }
            };
        }

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function storeAsAdmin()
    {
        return [
            'ad_id' => ['required', 'integer'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'views' => ['required', 'integer'],
            'clicks' => ['required', 'integer'],
        ];
    }

    protected function update()
    {
        // $user

        if ($this->hasAdminPrivileges()) {
            return [
                'id' => ['sometimes', 'required', 'integer'],
                'ad_id' => ['sometimes', 'required', 'integer'],
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
