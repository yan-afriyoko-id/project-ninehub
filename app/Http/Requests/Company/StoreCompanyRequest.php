<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
            'email' => 'nullable|email|max:255|unique:companies,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required',
            'name.string' => 'Company name must be a text',
            'name.max' => 'Company name must be less than 255 characters',
            'email.email' => 'Company email must be a valid email',
            'email.max' => 'Company email must be less than 255 characters',
            'email.unique' => 'Company email already exists',
            'phone.string' => 'Company phone must be a text',
            'phone.max' => 'Company phone must be less than 20 characters',
            'address.string' => 'Company address must be a text',
            'address.max' => 'Company address must be less than 500 characters',
            'website.url' => 'Company website must be a valid URL',
            'website.max' => 'Company website must be less than 255 characters',
            'industry.string' => 'Company industry must be a text',
            'industry.max' => 'Company industry must be less than 255 characters',
            'user_id.required' => 'User ID is required',
            'user_id.integer' => 'User ID must be an integer',
            'user_id.exists' => 'User not found',
        ];
    }
}
