<?php

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
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
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
            ],
            'phone' => 'nullable|string|max:20',
            'source' => 'nullable|string|max:100',
            'status' => 'sometimes|required|in:Baru,Terkualifikasi,Tidak Terkualifikasi,Konversi',
            'potential_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'contact_id' => 'sometimes|required|integer|exists:contacts,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Lead name is required',
            'name.string' => 'Lead name must be a text',
            'name.max' => 'Lead name must be less than 255 characters',
            'email.required' => 'Lead email is required',
            'email.email' => 'Lead email must be a valid email',
            'email.max' => 'Lead email must be less than 255 characters',
            'email.unique' => 'Lead email already exists',
            'phone.string' => 'Lead phone must be a text',
            'phone.max' => 'Lead phone must be less than 20 characters',
            'source.string' => 'Lead source must be a text',
            'source.max' => 'Lead source must be less than 100 characters',
            'status.required' => 'Lead status is required',
            'status.in' => 'Lead status must be one of: Baru, Terkualifikasi, Tidak Terkualifikasi, Konversi',
            'potential_value.numeric' => 'Lead potential value must be a number',
            'potential_value.min' => 'Lead potential value must be greater than or equal to 0',
            'notes.string' => 'Lead notes must be a text',
            'notes.max' => 'Lead notes must be less than 1000 characters',
            'contact_id.required' => 'Contact ID is required',
            'contact_id.integer' => 'Contact ID must be an integer',
            'contact_id.exists' => 'Contact not found',
        ];
    }
}
