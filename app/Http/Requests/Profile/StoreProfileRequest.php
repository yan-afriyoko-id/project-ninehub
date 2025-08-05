<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
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
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a text.',
            'name.max' => 'Name must be less than 255 characters.',
            'age.integer' => 'Age must be an integer.',
            'age.min' => 'Age must be greater than 0.',
            'gender.in' => 'Gender must be either male or female.',
            'phone_number.string' => 'Phone number must be a text.',
            'phone_number.max' => 'Phone number must be less than 20 characters.',
            'address.string' => 'Address must be a text.',
            'address.max' => 'Address must be less than 500 characters.',
            'birth_date.date' => 'Birth date must be a valid date.',
            'user_id.required' => 'User ID is required.',
            'user_id.integer' => 'User ID must be an integer.',
            'user_id.exists' => 'User not found.',
        ];
    }
}
