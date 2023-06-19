<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use app\Http\Controllers\UserController;


class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    protected function hasAdminPrivileges(): bool
    {
        if ($this->user() && $this->user()->hasAdminPrivileges()) return true;

        return false;
    }

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

        if ($methodName === 'login') {
            return $this->login();
        } elseif ($methodName === 'register') {
            return $this->register();
        } elseif ($methodName === 'recover') {
            return $this->recover();
        } elseif ($methodName === 'resetPassword') {
            return $this->reset();
        } elseif ($methodName === 'updateData') {
            return $this->updateData();
        } elseif ($methodName === 'updateMail') {
            return $this->updateMail();
        } elseif ($methodName === 'updatePassword') {
            return $this->updatePassword();
        }

        if ($this->hasAdminPrivileges() && $methodName === 'update') {
            return $this->update();
        }
    }

    /**
     * Get the validation rules for login.
     *
     * @return array
     */
    protected function login(): array
    {
        return [
            'login' => ['required'],
            'password' => ['required']
        ];
    }

    /**
     * Get the validation rules for user registration.
     *
     * @return array
     */
    protected function register(): array
    {
        return [
            'login' => ['required', 'unique:users'],
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'],
            'user_role_id' => ['required', Rule::in([1])],
        ];
    }

    /**
     * Get the validation rules for recover.
     *
     * @return array
     */
    protected function recover() : array {
        return [
            'email'=> ['required', 'email']
        ];
    }

    /**
     * Get the validation rules for reset.
     *
     * @return array
     */
    protected function reset() : array {
        return [
            'hash'=> ['required'],
            'password' => ['required', 'confirmed'],
        ];
    }

    /**
     * Get the validation rules for updating user information.
     *
     * @return array
     */
    protected function update(): array
    {
        return [
            'name' => ['sometimes', 'required'],
            'email' => ['sometimes', 'required', 'email', 'unique:users'],
            'login' => ['sometimes', 'required', 'unique:users'],
            'user_role_id' => ['sometimes', 'required', 'integer'],
            'password' => ['sometimes', 'required']
        ];
    }

    /**
     * Get the validation rules for updating the user's data.
     *
     * @return array
     */
    protected function updateData(): array
    {
        return [
            'representative' => ['sometimes', 'string', 'max:255'],
            'representative_phone' => ['sometimes', 'string', 'max:32', 'not_regex:/[a-z]/i'],
            'name' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'string', 'max:255'],
            'nip' => ['sometimes', 'string', 'max:10', 'not_regex:/[a-z]/i'],
            'company_email' => ['sometimes', 'nullable', 'string', 'email', 'max:255'],
            'company_phone' => ['sometimes', 'nullable', 'string', 'max:32', 'not_regex:/[a-z]/i'],
        ];
    }

    /**
     * Get the validation rules for updating the user's mail.
     *
     * @return array
     */
    protected function updateMail() : array {
        return [
            'email' => ['sometimes', 'required', 'email', 'unique:users'],
        ];
    }

    /**
     * Get the validation rules for updating the user's password.
     *
     * @return array
     */
    protected function updatePassword(): array
    {
        return [
            'password_current' => ['required'],
            'password' => ['required', 'confirmed'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->isMethod('POST')) {
            $this->merge([
                'user_role_id' => 1
            ]);
        } else if ($this->filled('userRoleId')) {
            $this->merge([
                'user_role_id' => $this->userRoleId
            ]);
        }

        if ($this->filled('representativePhone')) {
            $this->merge([
                'representative_phone' => $this->representativePhone
            ]);
        }

        if ($this->filled('postalCode')) {
            $this->merge([
                'postal_code' => $this->postalCode
            ]);
        }

        if ($this->filled('companyEmail')) {
            $this->merge([
                'company_email' => $this->companyEmail
            ]);
        }

        if ($this->filled('companyPhone')) {
            $this->merge([
                'company_phone' => $this->companyPhone
            ]);
        }

        if ($this->filled('passwordConfirmation')) {
            $this->merge([
                'password_confirmation' => $this->passwordConfirmation
            ]);
        }

        if ($this->filled('passwordCurrent')) {
            $this->merge([
                'password_current' => $this->passwordCurrent
            ]);
        }
    }
}
