<?php

namespace App\Http\Requests;

use App\Models\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UserRequest extends FormRequest
{
    // protected function failedValidation(Validator $validator)
    // {
    //     $response = response()->json([
    //         'status' => "Error",
    //         'message' => "Validation failed. Please check the following fields:",
    //         'data' => $validator->errors(),
    //     ], 422);

    //     throw (new ValidationException($validator, $response))
    //         ->errorBag($this->errorBag)
    //         ->redirectTo($this->getRedirectUrl());
    // }

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

        if ($methodName === 'validateAuthenticationKey') return $this->validateAuthenticationKey();
        elseif ($methodName === 'validateLogin') return $this->validateLogin();
        elseif ($methodName === 'validateEmail') return $this->validateEmail();
        elseif ($methodName === 'activateAccount') return $this->activateAccount();

        elseif ($methodName === 'login') return $this->login();
        elseif ($methodName === 'recover') return $this->recover();
        elseif ($methodName === 'resetPassword') return $this->resetPassword();
        elseif ($methodName === 'updateData') return $this->updateData();
        elseif ($methodName === 'updateMail') return $this->updateMail();
        elseif ($methodName === 'updatePassword') return $this->updatePassword();


        if ($this->hasAdminPrivileges()) {
            if ($methodName === 'update') return $this->update();
            elseif ($methodName === 'store') return $this->store();
            elseif ($methodName === 'assignActivationKey') return $this->assignActivationKey();
            elseif ($methodName === 'toggleBan') return $this->toggleBan();
            elseif ($methodName === 'changePassword') return $this->changePassword();
        }

        return [];
    }

    /**
     * Get the validation rules for authentication key while activating account.
     *
     * @return array
     */
    protected function validateAuthenticationKey(): array
    {
        return [
            'activation_key' => ['required', 'string', 'size:32']
        ];
    }

    /**
     * Get the validation rules for login while activating account.
     *
     * @return array
     */
    protected function validateLogin(): array
    {
        return [
            'activation_key' => ['required', 'string', 'size:32', Rule::exists('users', 'activation_key')],
            'login' => ['required', 'string', 'min:2', 'max:32', 'regex:/^[a-z0-9_]*$/i', Rule::unique('users')->ignore(User::where('activation_key', $this->activationKey)->first()?->id, 'id')],
        ];
    }

    /**
     * Get the validation rules for email while activating account.
     *
     * @return array
     */
    protected function validateEmail(): array
    {
        return [
            'activation_key' => ['required', 'string', 'size:32', Rule::exists('users', 'activation_key')],
            'email' => ['required', 'string', 'email', 'min:3', 'max:255', Rule::unique('users')->ignore(User::where('activation_key', $this->activationKey)->first()?->id, 'id')]
        ];
    }

    /**
     * Get the validation rules for activating account.
     *
     * @return array
     */
    protected function activateAccount(): array
    {
        return [
            'activation_key' => ['required', 'string', 'size:32', Rule::exists('users', 'activation_key')],
            'login' => ['required', 'string', 'min:2', 'max:32', 'regex:/^[a-z0-9_]*$/i', Rule::unique('users')->ignore(User::where('activation_key', $this->activationKey)->first()?->id, 'id')],
            'email' => ['required', 'string', 'email', 'min:3', 'max:255', Rule::unique('users')->ignore(User::where('activation_key', $this->activationKey)->first()?->id, 'id')],
            'password' => ['required', 'string', 'confirmed', 'min:6', 'max:255'],
        ];
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
     * Get the validation rules for attemp to recovering password.
     *
     * @return array
     */
    protected function recover() : array
    {
        return [
            'email'=> ['required', 'email']
        ];
    }

    /**
     * Get the validation rules for resetting password.
     *
     * @return array
     */
    protected function resetPassword() : array
    {
        return [
            'hash'=> ['required'],
            'password' => ['required', 'string', 'confirmed', 'min:6', 'max:255'],
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'representative' => ['sometimes', 'string', 'max:255'],
            'representative_phone' => ['sometimes', 'string', 'max:32', 'not_regex:/[a-z]/i'],
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
            'email' => ['sometimes', 'required', 'string', 'email', 'min:3', 'max:255', 'unique:users'],
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
            'password' => ['required', 'string', 'confirmed', 'min:6', 'max:255'],
        ];
    }


    /* ADMIN ONLY */

    /**
     * Get the validation rules for creating user account.
     *
     * @return array
     */
    protected function store(): array
    {
        return [
            'login' => ['required', 'string', 'min:2', 'max:32', 'regex:/^[a-z0-9_]*$/i', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'min:3', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'user_role_id' => ['required', Rule::in([1])],
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
            'login' => ['sometimes', 'required', 'string', 'min:2', 'max:32', 'regex:/^[a-z0-9_]*$/i', Rule::unique('users')->ignore($this->id, 'id')],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'min:3', 'max:255', Rule::unique('users')->ignore($this->id, 'id')],
            'representative' => ['sometimes','nullable', 'string', 'max:255'],
            'representative_phone' => ['sometimes','nullable', 'string', 'max:32', 'not_regex:/[a-z]/i'],
            'address' => ['sometimes','nullable', 'string', 'max:255'],
            'postal_code' => ['sometimes','nullable', 'string', 'max:255'],
            'country' => ['sometimes','nullable', 'string', 'max:255'],
            'nip' => ['sometimes','nullable', 'string', 'max:10', 'not_regex:/[a-z]/i'],
            'company_email' => ['sometimes', 'nullable', 'string', 'email', 'max:255'],
            'company_phone' => ['sometimes', 'nullable', 'string', 'max:32', 'not_regex:/[a-z]/i'],
        ];
    }

    /**
     * Get the validation rules for assigning activation key to user.
     *
     * @return array
     */
    protected function assignActivationKey(): array
    {
        return [
            'id' => ['required', 'integer'],
            'activation_key' => ['required', 'string', 'size:32']
        ];
    }

    /**
     * Get the validation rules for banning or unbanning user.
     *
     * @return array
     */
    protected function toggleBan(): array
    {
        return [
            'id' => ['required', 'integer'],
            'ban_reason' => ['sometimes', 'required', 'string', 'max:255']
        ];
    }

    /**
     * Get the validation rules for updating the user's password.
     *
     * @return array
     */
    protected function changePassword(): array
    {
        return [
            'id' => ['required', 'integer'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
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
            $this->merge([
                'name' => $this->login
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

        if ($this->filled('activationKey')) {
            $this->merge([
                'activation_key' => $this->activationKey
            ]);
        }

        if ($this->filled('banReason')) {
            $this->merge([
                'ban_reason' => $this->banReason
            ]);
        }
    }
}
