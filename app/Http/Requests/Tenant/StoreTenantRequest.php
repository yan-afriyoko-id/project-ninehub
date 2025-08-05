<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'logo' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tenant name is required',
            'name.max' => 'Tenant name must be less than 255 characters',
            'email.email' => 'Tenant email must be a valid email',
            'phone.max' => 'Tenant phone must be less than 50 characters',
            'logo.string' => 'Tenant logo must be a text',
            'logo.max' => 'Tenant logo must be less than 255 characters',
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User not found',
            'plan_id.required' => 'Plan ID is required',
            'plan_id.exists' => 'Plan not found',
            'is_active.boolean' => 'Is active must be a boolean',
        ];
    }
}
