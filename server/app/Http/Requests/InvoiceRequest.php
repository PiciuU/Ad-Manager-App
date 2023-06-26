<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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

        if ($this->hasAdminPrivileges()) {
            if ($methodName === 'storeAsAdmin') return $this->store();
            elseif ($methodName === 'updateAsAdmin') return $this->update();
        }
        return [];
    }

    /**
     * Get the validation rules for creating invoice
     *
     * @return array
     */
    protected function store(): array
    {
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
            'ad_id' => ['sometimes', 'required', 'integer'],
            'number' => ['sometimes', 'required', 'string'],
            'price' => ['sometimes', 'required', 'numeric'],
            'date' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
            'status' => ['sometimes', 'required', 'string', Rule::in(['unpaid', 'paid'])],
            'notes' => ['sometimes', 'nullable']
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
                'ad_id' => $this->adId
            ]);
        }
    }
}
