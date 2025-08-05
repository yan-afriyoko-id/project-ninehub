<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:contacts,email',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'company_id' => 'nullable|integer|exists:companies,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'first_name.string' => 'First name must be a text',
            'first_name.max' => 'First name must be less than 255 characters',
            'last_name.string' => 'Last name must be a text',
            'last_name.max' => 'Last name must be less than 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email',
            'email.max' => 'Email must be less than 255 characters',
            'email.unique' => 'Email already exists',
            'phone.string' => 'Phone must be a text',
            'phone.max' => 'Phone must be less than 20 characters',
            'job_title.string' => 'Job title must be a text',
            'job_title.max' => 'Job title must be less than 255 characters',
            'company_id.integer' => 'Company ID must be an integer',
            'company_id.exists' => 'Company not found',
        ];
    }
}
