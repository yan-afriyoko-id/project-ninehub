<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
            'slug' => 'required|string|max:255|unique:plans,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'max_users' => 'required|integer|min:1',
            'max_storage' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Plan name is required',
            'name.max' => 'Plan name must be less than 255 characters',
            'slug.required' => 'Plan slug is required',
            'slug.unique' => 'Plan slug already exists',
            'slug.max' => 'Plan slug must be less than 255 characters',
            'price.required' => 'Plan price is required',
            'price.numeric' => 'Plan price must be a number',
            'price.min' => 'Plan price must be greater than 0',
            'currency.required' => 'Plan currency is required',
            'currency.max' => 'Plan currency must be less than 3 characters',
            'max_users.required' => 'Plan max users is required',
            'max_users.integer' => 'Plan max users must be a number',
            'max_users.min' => 'Plan max users must be greater than 0',
            'max_storage.required' => 'Plan max storage is required',
            'max_storage.integer' => 'Plan max storage must be a number',
            'max_storage.min' => 'Plan max storage must be greater than 0',
            'features.array' => 'Features must be an array',
            'features.*.string' => 'Features must be a string',
            'is_active.boolean' => 'Is active must be a boolean',
        ];
    }
}
