<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'logo' => 'nullable|string|max:255',
            'user_id' => 'sometimes|required|exists:users,id',
            'plan_id' => 'sometimes|required|exists:plans,id',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama tenant wajib diisi',
            'name.max' => 'Nama tenant maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'phone.max' => 'Nomor telepon maksimal 50 karakter',
            'user_id.required' => 'User ID wajib diisi',
            'user_id.exists' => 'User tidak ditemukan',
            'plan_id.required' => 'Plan ID wajib diisi',
            'plan_id.exists' => 'Plan tidak ditemukan',
        ];
    }
}
